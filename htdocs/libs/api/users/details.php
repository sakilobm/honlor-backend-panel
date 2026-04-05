<?php

/**
 * API: users/details
 * ==================
 */
$details = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!$this->paramsExists(['id'])) {
        $this->response($this->json(['error' => 'Missing id']), 400);
    }

    $id = (int)$this->_request['id'];

    try {
        $user = new \Aether\User($id);
        $data = [
            'id'       => $user->id,
            'username' => $user->username,
            'email'    => $user->email,
            'active'   => $user->getActive(),
            'blocked'  => $user->getBlocked(),
            'joined'   => $user->getCreatedAt()
        ];
        
        $this->response($this->json(['user' => $data]), 200);
    } catch (\Exception $e) {
        $this->response($this->json(['error' => $e->getMessage()]), 404);
    }
};
