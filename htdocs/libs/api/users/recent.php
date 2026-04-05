<?php

/**
 * API Endpoint: /api/users/recent
 * ===============================
 * Returns the most recently joined users for the dashboard grid.
 */

use Aether\User;

${basename(__FILE__, '.php')} = function () {

    // Ensure session is active
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    try {
        // Fetch last 6 users
        $users = User::listAll(6, 0, '');
        
        $this->response($this->json([
            'users' => $users
        ]), 200);

    } catch (Exception $e) {
        error_log("API Error /users/recent: " . $e->getMessage());
        $this->response($this->json(['error' => 'Internal Server Error']), 500);
    }
};
