<?php

/**
 * API: users/handshakes
 * =====================
 * Fetches all identities currently in the 'Handshake Pending' state,
 * awaiting administrative authorization and security cluster migration.
 */
$handshakes = function() 
{
    if (!$this->isAuthenticated() || !\Aether\Session::isMaster()) {
        $this->response($this->json(['error' => 'Unauthorized Access']), 401);
    }

    $conn = \Aether\Database::getConnection();
    
    // Fetch users with pending requests
    $sql = "SELECT a.id, a.username, a.email, a.created_at, p.firstname, p.lastname, p.avatar 
            FROM `auth` a
            LEFT JOIN `profiles` p ON a.id = p.id
            WHERE a.`request_pending` = 1 AND a.`role_id` = 0
            ORDER BY a.created_at DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $requests = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $this->response($this->json([
        'requests' => $requests,
        'count' => count($requests)
    ]), 200);
};
