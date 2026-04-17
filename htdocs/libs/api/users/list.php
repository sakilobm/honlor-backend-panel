<?php

/**
 * API: users/list
 * ===============
 */
$list = function() 
{
    if (!\Session::isAuthenticated() || !\Session::isMaster()) {
        $this->response($this->json(['error' => 'Unauthorized Access']), 401);
    }

    $limit  = (int)($_GET['limit'] ?? 10);
    $page   = (int)($_GET['page'] ?? 1);
    $filter = $_GET['filter'] ?? '';
    $offset = ($page - 1) * $limit;

    $users = \Aether\User::listAll($limit, $offset, $filter);
    $total = \Aether\User::getTotalCount($filter);

    $this->response($this->json([
        'total' => $total,
        'page'  => $page,
        'limit' => $limit,
        'users' => $users
    ]), 200);
};
