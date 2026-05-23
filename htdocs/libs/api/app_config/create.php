<?php

/**
 * API Endpoint: /api/app_config/create
 * ====================================
 * Registers and publishes a new app version config.
 */

use App\AppConfig;

${basename(__FILE__, '.php')} = function () {

    // Ensure session is active (Admin only)
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    $version     = $this->_request['latest_version'] ?? null;
    $isMandatory = isset($this->_request['is_mandatory']) ? (bool)$this->_request['is_mandatory'] : false;
    $url         = $this->_request['update_url']     ?? null;
    $description = $this->_request['description']    ?? null;

    // Validate inputs
    if (empty($version) || empty($url)) {
        $this->response($this->json(['error' => 'Missing version identifier or deployment URL.']), 400);
    }

    // Validate URL syntax
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $this->response($this->json(['error' => 'Invalid deployment URL format.']), 400);
    }

    try {
        $newId = AppConfig::create($version, $isMandatory, $url, $description);

        if ($newId > 0) {
            $this->response($this->json([
                'message' => 'App configuration published successfully.',
                'id' => $newId
            ]), 200);
        } else {
            throw new Exception("SQL execution failed to return last insert ID.");
        }

    } catch (Exception $e) {
        error_log("API Error /app_config/create: " . $e->getMessage());
        $this->response($this->json(['error' => 'Internal Server Error', 'details' => $e->getMessage()]), 500);
    }
};
