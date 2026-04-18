<?php

/**
 * API: users/search
 * =================
 * Search for users by identity strings.
 */

use Aether\Database;

${basename(__FILE__, '.php')} = function() 
{
    if (!\Session::isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    $query = $_GET['q'] ?? '';
    
    if (strlen($query) < 2) {
        $this->response($this->json(['users' => []]), 200);
    }

    $db = Database::getConnection();
    $sql = "
        SELECT a.id, a.username, p.first_name, p.last_name 
        FROM `auth` a
        LEFT JOIN `profiles` p ON a.id = p.uid
        WHERE a.username LIKE ? OR p.first_name LIKE ? OR p.last_name LIKE ?
        LIMIT 10
    ";
    
    $stmt = $db->prepare($sql);
    $search = "%$query%";
    $stmt->execute([$search, $search, $search]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $this->response($this->json(['users' => $users]), 200);
};
