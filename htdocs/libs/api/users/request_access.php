<?php

/**
 * API: users/request_access
 */
$request_access = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Authentication required']), 401);
    }

    $user = \Aether\Session::getUser();
    if ((int)$user->getRoleId() > 0) {
        $this->response($this->json(['error' => 'Identity already cleared.']), 400);
    }

    if ($user->requestAccess()) {
        $this->response($this->json(['message' => 'Identity Handshake initiated. Pending Administrator migration.']), 200);
    } else {
        $this->response($this->json(['error' => 'Handshake protocol failed. Try again.']), 500);
    }
};
