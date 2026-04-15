<?php

/**
 * Delete Role API
 * ===============
 * POST /api/roles/delete
 */

$delete = function () {
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!\Session::hasPermission('roles', 'manage')) {
        $this->response($this->json(['error' => 'Permission Denied']), 403);
    }

    if ($this->paramsExists(['id'])) {
        $id = (int)$this->_request['id'];
        $db = \Aether\Database::getConnection();
        
        // Check if any users are assigned to this role
        $stmt = $db->prepare("SELECT COUNT(*) FROM `auth` WHERE `role_id` = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            $this->response($this->json(['error' => 'Cannot delete role assigned to active identities.']), 400);
            return;
        }

        $stmt = $db->prepare("DELETE FROM `roles` WHERE `id` = ?");
        $stmt->execute([$id]);
        $this->response($this->json(['message' => 'Security cluster deconstructed.']), 200);
    } else {
        $this->response($this->json(['error' => 'Missing ID']), 400);
    }
};
