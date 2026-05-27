<?php

/**
 * API: users/process_deletion
 * ==========================
 * Approves or Rejects a deletion request via the Supabase
 * request-account-deletion edge function (update_request action).
 *
 * Sends an email notification to the user after processing:
 *   - Approved: templated approval email
 *   - Rejected: rejection email with the admin-provided reason
 *
 * Falls back to local MySQL if Supabase credentials are not configured.
 */

use Aether\Database;
use Aether\Mailer;
use Aether\Session;
use Aether\SupabaseClient;

${basename(__FILE__, '.php')} = function()
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!\Session::hasPermission('deletion', 'manage')) {
        $this->response($this->json(['error' => 'Permission Denied']), 403);
    }

    if (!$this->paramsExists(['id', 'status'])) {
        $this->response($this->json(['error' => 'Missing id or status']), 400);
    }

    $id               = $this->_request['id'];
    $status           = $this->_request['status']; // 'approved' or 'rejected'
    $userEmail        = trim($this->_request['user_email']    ?? '');
    $userName         = trim($this->_request['user_name']     ?? '');
    $rejectionReason  = trim($this->_request['rejection_reason'] ?? '');
    $admin            = Session::getUser();
    $adminName        = $admin ? ($admin->username ?? 'admin') : 'admin';

    if (!in_array($status, ['approved', 'rejected'], true)) {
        $this->response($this->json(['error' => 'Invalid status value.']), 400);
    }

    if ($status === 'rejected' && empty($rejectionReason)) {
        $this->response($this->json(['error' => 'A rejection reason is required.']), 400);
    }

    // Admin note stored in Supabase / local DB
    $adminNote = $status === 'rejected'
        ? "Rejected by {$adminName}: {$rejectionReason}"
        : "Approved by {$adminName} from dashboard.";

    // ── Try Supabase REST API directly ──────────────────────────────────────
    $supabaseUrl = get_config('supabase_url');
    $supabaseKey = get_config('supabase_service_key');

    if (!empty($supabaseUrl) && !empty($supabaseKey)) {
        $result = SupabaseClient::patchRow('deletion_requests', 'id', $id, [
            'status'      => $status,
            'admin_note'  => $adminNote,
            'reviewed_by' => $adminName,
            'reviewed_at' => date('c'),
        ]);

        if ($result['success']) {
            // ── Send email notification ───────────────────────────────────
            $emailSent = false;
            if (!empty($userEmail)) {
                if ($status === 'approved') {
                    $emailSent = Mailer::sendDeletionApproved($userEmail, $userName);
                } else {
                    $emailSent = Mailer::sendDeletionRejected($userEmail, $userName, $rejectionReason);
                }
                if (!$emailSent) {
                    error_log("Mailer: failed to send deletion {$status} email to {$userEmail}");
                }
            }

            $this->response($this->json([
                'message'    => "Request '{$status}' successfully.",
                'email_sent' => $emailSent,
            ]), 200);
            return;
        }

        error_log("Supabase process_deletion failed: " . ($result['error'] ?? 'Unknown error'));
    }

    // ── Fallback: Local MySQL ───────────────────────────────────────────────
    $db = Database::getConnection();

    try {
        $db->beginTransaction();

        $stmt = $db->prepare(
            "UPDATE deletion_requests SET status = ?, admin_note = ?, reviewed_at = NOW(), reviewed_by = ? WHERE id = ?"
        );
        $stmt->execute([$status, $adminNote, $admin ? $admin->id : 0, (int)$id]);

        if ($status === 'approved') {
            $stmt = $db->prepare("SELECT user_id FROM deletion_requests WHERE id = ?");
            $stmt->execute([(int)$id]);
            $uid = $stmt->fetchColumn();

            $stmt = $db->prepare("UPDATE `auth` SET active = 0, blocked = 1 WHERE id = ?");
            $stmt->execute([$uid]);
        }

        $db->commit();

        // ── Send email notification ───────────────────────────────────────
        $emailSent = false;
        if (!empty($userEmail)) {
            if ($status === 'approved') {
                $emailSent = Mailer::sendDeletionApproved($userEmail, $userName);
            } else {
                $emailSent = Mailer::sendDeletionRejected($userEmail, $userName, $rejectionReason);
            }
        }

        $this->response($this->json([
            'message'    => "Request '{$status}' successfully.",
            'email_sent' => $emailSent,
        ]), 200);

    } catch (\Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        $this->response($this->json(['error' => $e->getMessage()]), 500);
    }
};
