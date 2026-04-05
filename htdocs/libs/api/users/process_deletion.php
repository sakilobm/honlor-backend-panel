<?php

/**
 * API: users/process_deletion
 * ==========================
 * Approves or Rejects a deletion request.
 */

use Aether\Database;
use Aether\Session;

${basename(__FILE__, '.php')} = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!$this->paramsExists(['id', 'status'])) {
        $this->response($this->json(['error' => 'Missing id or status']), 400);
    }

    $id = (int)$this->_request['id'];
    $status = $this->_request['status']; // 'approved' or 'rejected'
    $admin = Session::getUser();

    $db = Database::getConnection();

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("UPDATE deletion_requests SET status = ?, reviewed_at = NOW(), reviewed_by = ? WHERE id = ?");
        $stmt->execute([$status, $admin ? $admin->id : 0, $id]);

        if ($status === 'approved') {
            // Fetch user id
            $stmt = $db->prepare("SELECT user_id FROM deletion_requests WHERE id = ?");
            $stmt->execute([$id]);
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
