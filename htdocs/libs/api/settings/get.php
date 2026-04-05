<?php

/**
 * API Endpoint: /api/settings/get
 * ====================================
 * Fetches a list of requested system settings based on keys.
 */

use App\Settings;

${basename(__FILE__, '.php')} = function () {

    // Ensure session is active (Admin only)
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    $keys = $this->_request['keys'] ?? null;

    if (!$keys) {
        $this->response($this->json(['error' => 'Missing keys parameter.']), 400);
    }

    // Keys can be a comma-separated string
    if (is_string($keys)) {
        $keys = explode(',', $keys);
    }

    try {
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = Settings::get(trim($key));
        }

        $this->response($this->json(['settings' => $data]), 200);

    } catch (Exception $e) {
        error_log("API Error /settings/get: " . $e->getMessage());
        $this->response($this->json(['error' => 'Internal Server Error']), 500);
    }
};
