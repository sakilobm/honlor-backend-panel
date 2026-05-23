<?php

/**
 * API Endpoint: /api/app_config/delete
 * ====================================
 * Decommissions and purges an app version config entry.
 */

use App\AppConfig;

${basename(__FILE__, '.php')} = function () {

    // Ensure session is active (Admin only)
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    $id = $this->_request['id'] ?? null;
    if (!$id) {
        $this->response($this->json(['error' => 'Missing target configuration ID.']), 400);
    }

    // Verify record exists
    $existing = AppConfig::get((int)$id);
    if (!$existing) {
        $this->response($this->json(['error' => 'Target configuration record not found.']), 404);
    }

    try {
        $success = AppConfig::delete((int)$id);

        if ($success) {
            $this->response($this->json(['message' => 'Configuration protocol decommissioned successfully.']), 200);
        } else {
            throw new Exception("Database delete operation failed.");
        }

    } catch (Exception $e) {
        error_log("API Error /app_config/delete: " . $e->getMessage());
        $this->response($this->json(['error' => 'Internal Server Error', 'details' => $e->getMessage()]), 500);
    }
};
