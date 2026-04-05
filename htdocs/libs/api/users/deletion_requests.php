<?php

/**
 * API: users/deletion_requests
 * ===========================
 * Lists all account deletion requests.
 */

use Aether\Database;

${basename(__FILE__, '.php')} = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    $db = Database::getConnection();
    
    try {
        $sql = "SELECT dr.*, a.username, a.email 
                FROM deletion_requests dr 
                JOIN auth a ON dr.user_id = a.id 
                ORDER BY dr.created_at DESC";
        $stmt = $db->query($sql);
        $requests = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Stats
        $pending = 0;
        foreach ($requests as $req) if ($req['status'] === 'pending') $pending++;

        $this->response($this->json([
            'requests' => $requests,
            'stats' => [
                'total' => count($requests),
                'pending' => $pending
            ]
        ]), 200);
    } catch (\Exception $e) {
        $this->response($this->json(['error' => $e->getMessage()]), 500);
    }
};
