<?php

/**
 * API: users/check_clearance
 */
$check_clearance = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Authentication required']), 401);
    }

    $user = \Aether\Session::getUser();
    // Force refresh user data from DB to check for role updates
    $freshUser = new \Aether\User($user->id);
    
    if ((int)$freshUser->getRoleId() > 0 || $freshUser->isMaster()) {
        $this->response($this->json([
            'cleared' => true,
            'message' => 'Identity cleared. Migration successful.'
        ]), 200);
    } else {
        $this->response($this->json([
            'cleared' => false,
            'request_pending' => (int)$freshUser->getRequestPending() === 1,
            'message' => 'Identity still restricted. Handshake pending.'
        ]), 200);
    }
};
