<?php

/**
 * API Endpoint: /api/app_config/update
 * ====================================
 * Updates an existing app configuration record (metadata or inline flags).
 * Also handles set_active action to switch the live build target.
 */

use App\AppConfig;
use Aether\SupabaseClient;

${basename(__FILE__, '.php')} = function () {

    // Ensure session is active (Admin only)
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    // --- Action: set_active ---
    // Switches the active build target. Syncs to Supabase edge function.
    if (($this->_request['action'] ?? null) === 'set_active') {
        $id = $this->_request['id'] ?? null;
        if (!$id) {
            $this->response($this->json(['error' => 'Missing target configuration ID.']), 400);
        }

        $config = AppConfig::get((int)$id);
        if (!$config) {
            $this->response($this->json(['error' => 'Target configuration record not found.']), 404);
        }

        $success = AppConfig::setActive((int)$id);
        if (!$success) {
            $this->response($this->json(['error' => 'Failed to set active version.']), 500);
        }

        // Sync to Supabase so the mobile app can pick it up
        $syncResult = SupabaseClient::syncAppVersion(
            $config['latest_version'],
            (bool)$config['is_mandatory'],
            $config['update_url'],
            $config['description'] ?? ''
        );

        $this->response($this->json([
            'message'      => 'Active build target updated successfully.',
            'version'      => $config['latest_version'],
            'supabase_sync' => $syncResult['success'] ? 'ok' : ('warn: ' . ($syncResult['error'] ?? 'unknown'))
        ]), 200);
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
