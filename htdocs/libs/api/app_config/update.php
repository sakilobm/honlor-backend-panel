<?php

/**
 * API Endpoint: /api/app_config/update
 * ====================================
 * Updates an existing app configuration record (metadata or inline flags).
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

    // Gather update data
    $updateData = [];
    
    if (isset($this->_request['latest_version'])) {
        $updateData['latest_version'] = $this->_request['latest_version'];
    }
    
    if (isset($this->_request['is_mandatory'])) {
        // Handle both raw booleans and custom string triggers
        $val = $this->_request['is_mandatory'];
        $updateData['is_mandatory'] = ($val === true || $val === 'true' || $val === 1 || $val === '1' || $val === 'on');
    }
    
    if (isset($this->_request['update_url'])) {
        $url = $this->_request['update_url'];
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->response($this->json(['error' => 'Invalid deployment URL format.']), 400);
        }
        $updateData['update_url'] = $url;
    }
    
    if (isset($this->_request['description'])) {
        $updateData['description'] = $this->_request['description'];
    }

    if (empty($updateData)) {
        $this->response($this->json(['error' => 'No modification parameters provided.']), 400);
    }

    try {
        $success = AppConfig::update((int)$id, $updateData);

        if ($success) {
            $this->response($this->json(['message' => 'Configuration protocol updated successfully.']), 200);
        } else {
            throw new Exception("Database update operation reported zero affected rows.");
        }

    } catch (Exception $e) {
        error_log("API Error /app_config/update: " . $e->getMessage());
        $this->response($this->json(['error' => 'Internal Server Error', 'details' => $e->getMessage()]), 500);
    }
};
