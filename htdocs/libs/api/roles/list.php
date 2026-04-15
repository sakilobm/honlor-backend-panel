<?php

/**
 * List Roles API
 * ==============
 * GET /api/roles/list
 */

$list = function () {
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!\Session::hasPermission('roles', 'view') && !\Session::hasPermission('roles', 'manage')) {
        $this->response($this->json(['error' => 'Permission Denied']), 403);
    }

    $roles = \App\Role::getAll();
    $this->response($this->json([
        'roles' => $roles
    ]), 200);
};
