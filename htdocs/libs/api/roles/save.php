<?php

/**
 * Save Role API
 * =============
 * POST /api/roles/save
 */

$save = function () {
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!\Session::hasPermission('roles', 'manage')) {
        $this->response($this->json(['error' => 'Permission Denied']), 403);
    }

    if ($this->paramsExists(['name', 'permissions'])) {
        $id = $this->_request['id'] ?? null;
        $name = $this->_request['name'];
        $permissions = json_decode($this->_request['permissions'], true);

        if ($id && $id > 0) {
            try {
                $role = new \App\Role((int)$id);
                $role->setName($name);
                $role->updatePermissions($permissions);
                $this->response($this->json(['message' => 'Security protocol updated.']), 200);
            } catch (Exception $e) {
                $msg = $e->getMessage();
                if (str_contains($msg, 'Duplicate entry') && str_contains($msg, 'slug')) {
                    $msg = 'A security cluster with a similar protocol name already exists. Unique identity required.';
                }
                $this->response($this->json(['error' => $msg]), 404);
            }
        } else {
            try {
                \App\Role::create($name, $permissions);
                $this->response($this->json(['message' => 'New security cluster established.']), 200);
            } catch (Exception $e) {
                $msg = $e->getMessage();
                if (str_contains($msg, 'Duplicate entry') && str_contains($msg, 'slug')) {
                    $msg = 'Failed to establish cluster: A similar security protocol name is already active.';
                }
                $this->response($this->json(['error' => $msg]), 400);
            }
        }
    } else {
        $this->response($this->json(['error' => 'Missing parameters']), 400);
    }
};
