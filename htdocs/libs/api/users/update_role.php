<?php

/**
 * Update User Role API
 * ===================
 * POST /api/users/update_role
 */

$update_role = function () {
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!\Session::hasPermission('roles', 'manage')) {
        $this->response($this->json(['error' => 'Permission Denied']), 403);
    }

    if ($this->paramsExists(['id', 'role_id'])) {
        $id = (int)$this->_request['id'];
        $role_id = (int)$this->_request['role_id'];

        try {
            $user = new \Aether\User($id);
            if ($user->setRoleId($role_id)) {
                $this->response($this->json(['message' => 'Identity cluster re-assigned.']), 200);
            } else {
                $this->response($this->json(['error' => 'Failed to update protocol.']), 500);
            }
        } catch (Exception $e) {
            $this->response($this->json(['error' => $e->getMessage()]), 404);
        }
    } else {
        $this->response($this->json(['error' => 'Missing parameters']), 400);
    }
};
