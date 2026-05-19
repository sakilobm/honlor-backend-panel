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

    try {
        if (SupabaseClient::updateReport($id, $action, $reply)) {
            $this->response($this->json([
                'success' => true,
                'message' => 'Report updated successfully.'
            ]), 200);
        } else {
            $this->response($this->json(['error' => 'Action failed.']), 500);
        }
    } catch (Exception $e) {
        error_log("API Error /supabase_reports/action: " . $e->getMessage());
        $this->response($this->json(['error' => 'Internal Server Error']), 500);
    }
};
