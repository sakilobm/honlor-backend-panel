<?php

use App\Settings;
use Aether\REST;

/**
 * API: Settings Update
 * ====================
 * Updates global system settings dynamically.
 */

// Ensure we are in an API context
if (!defined('HTDOCS_ROOT')) {
    require_once dirname(__DIR__, 3) . '/libs/load.php';
}

$rest = new REST();
$rest->requireAuth(); // Ensure only logged-in sessions can change settings

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = $rest->getJsonPayload();
    $key = $payload['key'] ?? null;
    $value = $payload['value'] ?? null;

    if (!$key) {
        $rest->response(['error' => 'Missing setting key'], 400);
    }

    $success = Settings::set($key, $value);

    if ($success) {
        $rest->response([
            'message' => "Setting '$key' updated successfully",
            'key' => $key,
            'value' => $value
        ]);
    } else {
        $rest->response(['error' => 'Failed to update setting'], 500);
    }
} else {
    $rest->response(['error' => 'Invalid request method'], 405);
}
