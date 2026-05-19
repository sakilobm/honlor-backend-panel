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

    try {
        $reports = SupabaseClient::getReports();
        
        // Calculate metrics
        $total = count($reports);
        $resolved = 0;
        $pending = 0;
        foreach ($reports as $r) {
            $status = strtolower($r['status'] ?? 'pending');
            if ($status === 'resolved' || $status === 'solved') {
                $resolved++;
            } else {
                $pending++;
            }
        }
        $velocity = $total > 0 ? ($resolved / $total) * 100 : 0;

        $this->response($this->json([
            'reports' => $reports,
            'metrics' => [
                'total'    => $total,
                'resolved' => $resolved,
                'pending'  => $pending,
                'velocity' => round($velocity, 1)
            ]
        ]), 200);
    } catch (Exception $e) {
        error_log("API Error /supabase_reports/list: " . $e->getMessage());
        $this->response($this->json(['error' => 'Internal Server Error']), 500);
    }
};
