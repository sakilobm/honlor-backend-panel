<?php

/**
 * API: logs/list
 * ==============
 */
$list = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    $limit = (int)($_GET['limit'] ?? 100);
    $logs = \App\DashboardStats::getRecentActivity($limit);
    
    $this->response($this->json([
        'logs' => $logs
    ]), 200);
};
