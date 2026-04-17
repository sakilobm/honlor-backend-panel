<?php

/**
 * API Endpoint: /api/compliance/action
 * ===================================
 * Resolve or dismiss safety incidents.
 */

use App\Compliance;

${basename(__FILE__, '.php')} = function () {
    
    // Auth check
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    $id = $_POST['id'] ?? null;
    $action = $_POST['action'] ?? null; // 'Resolved', 'Dismissed'

    if (!$id || !$action) {
        $this->response($this->json(['error' => 'Invalid parameters.']), 400);
    }

    try {
        if (Compliance::updateIncidentStatus((int)$id, $action)) {
            $this->response($this->json(['success' => true]), 200);
        } else {
            $this->response($this->json(['error' => 'Action failed.']), 500);
        }
    } catch (Exception $e) {
        error_log("API Error /compliance/action: " . $e->getMessage());
        $this->response($this->json(['error' => 'Internal Server Error']), 500);
    }
};
