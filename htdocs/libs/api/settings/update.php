<?php

/**
 * API Endpoint: /api/settings/update
 * ====================================
 * Updates or inserts a global system setting.
 */

use App\Settings;
use Aether\Session;
use Aether\Database;

${basename(__FILE__, '.php')} = function () {

    // Ensure session is active (Admin only)
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    $key   = $this->_request['key']   ?? null;
    $value = $this->_request['value'] ?? null;

    if (!$key || $value === null) {
        $this->response($this->json(['error' => 'Missing key or value.']), 400);
    }

    try {
        // Save the setting
        $success = Settings::set($key, $value);

        if ($success) {
            // Log the activity
            $user = Session::getUser();
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO logs (user_id, action, ip) VALUES (?, ?, ?)");
            $stmt->execute([
                $user ? $user->id : 0,
                "Updated setting: {$key} to {$value}",
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
            ]);

            $this->response($this->json(['message' => "Setting '{$key}' updated successfully."]), 200);
        } else {
            throw new Exception("Database update failed.");
        }

    } catch (Exception $e) {
        error_log("API Error /settings/update: " . $e->getMessage());
        $this->response($this->json(['error' => 'Internal Server Error', 'details' => $e->getMessage()]), 500);
    }
};
