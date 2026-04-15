<?php

/**
 * Get Role API
 * ============
 * GET /api/roles/get?id=1
 */

$get = function () {
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!\Session::hasPermission('roles', 'view') && !\Session::hasPermission('roles', 'manage')) {
        $this->response($this->json(['error' => 'Permission Denied']), 403);
    }

    if ($this->paramsExists(['id'])) {
        try {
            $role = new \App\Role((int)$this->_request['id']);
            $this->response($this->json([
                'role' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => json_encode($role->getPermissions())
                ]
            ]), 200);
        } catch (Exception $e) {
            $this->response($this->json(['error' => $e->getMessage()]), 404);
        }
    } else {
        $this->response($this->json(['error' => 'Missing ID']), 400);
    }
};
