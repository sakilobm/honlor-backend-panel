<?php

/**
 * API Endpoint: /api/supabase_reports/action
 * ==========================================
 * Resolve or dismiss Supabase reports with optional admin reply message.
 */

use Aether\SupabaseClient;

${basename(__FILE__, '.php')} = function () {
    
    // Auth check
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    $id = $_POST['id'] ?? null;
    $action = $_POST['action'] ?? null; // 'resolved' or 'dismissed'
    $reply = $_POST['reply'] ?? null;

    if (!$id || !$action) {
        $this->response($this->json(['error' => 'Invalid parameters.']), 400);
    }

    $patch = ['status' => $action];
    if (!empty($reply)) {
        $patch['admin_reply'] = $reply;
    }

    $result = SupabaseClient::patchRow('app_feedback', 'id', $id, $patch);

    if ($result['success']) {
        $this->response($this->json([
            'success' => true,
            'message' => 'Report updated successfully.',
        ]), 200);
    } else {
        error_log("API Error /supabase_reports/action: " . ($result['error'] ?? 'unknown'));
        $this->response($this->json(['error' => 'Action failed.']), 500);
    }
};
