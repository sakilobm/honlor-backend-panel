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

    $limit  = (int)($_GET['limit'] ?? 50);
    $status = $_GET['status'] ?? 'all';
    $filter = $_GET['filter'] ?? '';

    $messages = \App\Message::getFiltered($status, $limit, $filter);
    
    $this->response($this->json([
        'messages' => $messages,
        'metrics'  => [
            'velocity' => \App\Message::getVelocity(),
            'flagged'  => \App\Message::getFlaggedCount(),
            'recent_activity' => \App\DashboardStats::getRecentActivity(5)
        ]
    ]), 200);
};
