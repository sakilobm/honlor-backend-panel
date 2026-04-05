<?php

/**
 * API: messages/list
 * ==================
 */
$list = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    $limit = (int)($_GET['limit'] ?? 50);
    $messages = \App\Message::getLatest($limit);
    
    $this->response($this->json([
        'messages' => $messages
    ]), 200);
};
