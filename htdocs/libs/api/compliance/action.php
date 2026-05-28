<?php

/**
 * API Endpoint: /api/compliance/action
 * =====================================
 * Resolve or dismiss a report in the Supabase `reports` table.
 */

use Aether\SupabaseClient;
use Aether\Session;

${basename(__FILE__, '.php')} = function () {

    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    $id     = trim($_POST['id']     ?? '');
    $action = trim($_POST['action'] ?? ''); // 'resolved' | 'dismissed'
    $notes  = trim($_POST['notes']  ?? '');

    if (!$id || !$action) {
        $this->response($this->json(['error' => 'Missing id or action.']), 400);
    }

    $status = strtolower($action);
    if (!in_array($status, ['resolved', 'dismissed'], true)) {
        $this->response($this->json(['error' => 'Invalid action. Use resolved or dismissed.']), 400);
    }

    $admin     = Session::getUser();
    $adminName = $admin ? ($admin->username ?? 'admin') : 'admin';

    $patch = [
        'status'      => $status,
        'reviewed_by' => $adminName,
        'reviewed_at' => date('c'),
    ];
    if ($notes !== '') {
        $patch['admin_notes'] = $notes;
    }

    $result = SupabaseClient::patchRow('reports', 'id', $id, $patch);

    if ($result['success']) {
        $this->response($this->json([
            'success' => true,
            'message' => 'Report ' . $status . ' successfully.',
        ]), 200);
    } else {
        error_log("API /compliance/action — patch failed: " . ($result['error'] ?? 'unknown'));
        $this->response($this->json(['error' => 'Failed to update report.']), 500);
    }
};
