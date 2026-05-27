<?php

/**
 * API Endpoint: /api/supabase_reports/list
 * ========================================
 * Returns the active and resolved Supabase reports queue.
 */

use Aether\SupabaseClient;

${basename(__FILE__, '.php')} = function () {
    
    // Auth check
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    $result = SupabaseClient::queryTable('app_feedback', [
        'select' => '*',
        'order'  => 'created_at.desc',
    ]);

    if (!$result['success']) {
        error_log("API Error /supabase_reports/list: " . ($result['error'] ?? 'unknown'));
        $this->response($this->json(['error' => 'Failed to fetch app reports.']), 500);
    }

    $rows     = $result['data'] ?? [];
    $total    = count($rows);
    $resolved = 0;
    $pending  = 0;

    $reports = [];
    foreach ($rows as $r) {
        $status = strtolower($r['status'] ?? 'pending');
        if ($status === 'resolved' || $status === 'solved') {
            $resolved++;
        } else {
            $pending++;
        }

        // Map app_feedback columns → shape the JS renderer expects
        $reports[] = [
            'id'          => $r['id']         ?? '',
            'reporter_id' => $r['user_id']    ?? null,
            'reported_id' => null,
            'channel_id'  => null,
            'reason'      => $r['category']   ?? 'App Feedback',
            'details'     => $r['details']    ?? null,
            'status'      => $r['status']     ?? 'pending',
            'admin_reply' => $r['admin_reply'] ?? null,
            'created_at'  => $r['created_at'] ?? null,
        ];
    }

    $velocity = $total > 0 ? round(($resolved / $total) * 100, 1) : 0;

    $this->response($this->json([
        'reports' => $reports,
        'metrics' => [
            'total'    => $total,
            'resolved' => $resolved,
            'pending'  => $pending,
            'velocity' => $velocity,
        ],
    ]), 200);
};
