<?php

/**
 * API: users/process_deletion
 * ==========================
 * Approves or Rejects a deletion request via the Supabase
 * request-account-deletion edge function (update_request action).
 * 
 * Falls back to local MySQL if Supabase credentials are not configured.
 */

use Aether\Database;
use Aether\Session;
use Aether\SupabaseClient;

${basename(__FILE__, '.php')} = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!$this->paramsExists(['id', 'status'])) {
        $this->response($this->json(['error' => 'Missing id or status']), 400);
    }

    $id = $this->_request['id'];
    $status = $this->_request['status']; // 'approved' or 'rejected'
    $admin = Session::getUser();
    $adminName = $admin ? ($admin->username ?? 'admin') : 'admin';

    // ── Try Supabase Edge Function first ────────────────────────────────────
    $supabaseUrl = get_config('supabase_url');
    $supabaseKey = get_config('supabase_service_key');

    if (!empty($supabaseUrl) && !empty($supabaseKey)) {
        $result = SupabaseClient::invokeFunction('request-account-deletion', [
            'action'      => 'update_request',
            'id'          => $id,
            'status'      => $status,
            'admin_note'  => "Processed by {$adminName} from dashboard.",
            'reviewed_by' => $adminName,
        ]);

        if ($result['success']) {
            $this->response($this->json([
                'message' => "Request '{$status}' successfully.",
            ]), 200);
            return;
        }

        // If the edge function call failed, log and fall through to local DB
        error_log("Supabase process_deletion failed: " . ($result['error'] ?? 'Unknown error'));
    }

    // ── Fallback: Local MySQL ───────────────────────────────────────────────
    $db = Database::getConnection();

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("UPDATE deletion_requests SET status = ?, reviewed_at = NOW(), reviewed_by = ? WHERE id = ?");
        $stmt->execute([$status, $admin ? $admin->id : 0, (int)$id]);

        if ($status === 'approved') {
            // Fetch user id
            $stmt = $db->prepare("SELECT user_id FROM deletion_requests WHERE id = ?");
            $stmt->execute([(int)$id]);
            $uid = $stmt->fetchColumn();

            // Perform deletion logic (deactivate user first to be safe, or full delete)
            // For now, let's just mark the user as blocked and inactive.
            $stmt = $db->prepare("UPDATE `auth` SET active = 0, blocked = 1 WHERE id = ?");
            $stmt->execute([$uid]);
        }

        $db->commit();
        $this->response($this->json(['message' => "Request '{$status}' successfully."]), 200);

    } catch (\Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        $this->response($this->json(['error' => $e->getMessage()]), 500);
    }
};
