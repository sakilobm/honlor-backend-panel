<?php

/**
 * API: channels/list
 * ==================
 */
$list = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    $channels = \App\Channel::listAll();
    
    $this->response($this->json([
        'channels' => $channels
    ]), 200);
};
