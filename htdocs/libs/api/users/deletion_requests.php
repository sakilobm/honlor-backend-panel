<?php

/**
 * API: users/deletion_requests
 * ===========================
 * Lists all account deletion requests from Supabase via the
 * request-account-deletion edge function (list_requests action).
 * 
 * Falls back to local MySQL if Supabase credentials are not configured.
 */

use Aether\Database;
use Aether\SupabaseClient;

${basename(__FILE__, '.php')} = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!\Session::hasPermission('deletion', 'view') && !\Session::hasPermission('deletion', 'manage')) {
        $this->response($this->json(['error' => 'Permission Denied']), 403);
    }

    // ── Try Supabase REST API directly ──────────────────────────────────────
    $supabaseUrl = get_config('supabase_url');
    $supabaseKey = get_config('supabase_service_key');

    if (!empty($supabaseUrl) && !empty($supabaseKey)) {
        $result = SupabaseClient::queryTable('deletion_requests', [
            'select' => '*',
            'order'  => 'created_at.desc',
        ]);

        if ($result['success']) {
            $rows = $result['data'] ?? [];

            $pending  = 0;
            $approved = 0;
            $rejected = 0;

            $requests = [];
            foreach ($rows as $req) {
                $status = $req['status'] ?? 'pending';
                if ($status === 'pending')  $pending++;
                if ($status === 'approved') $approved++;
                if ($status === 'rejected') $rejected++;

                $requests[] = [
                    'id'          => $req['id']          ?? '',
                    'username'    => $req['full_name']   ?? ($req['email'] ?? 'Unknown'),
                    'email'       => $req['email']       ?? '',
                    'reason'      => $req['reason']      ?? null,
                    'status'      => $status,
                    'created_at'  => $req['created_at']  ?? null,
                    'admin_note'  => $req['admin_note']  ?? null,
                    'reviewed_at' => $req['reviewed_at'] ?? null,
                    'reviewed_by' => $req['reviewed_by'] ?? null,
                ];
            }

            $total    = count($requests);
            $velocity = $total > 0 ? round((($approved + $rejected) / $total) * 100, 1) : 0;

            $this->response($this->json([
                'requests' => $requests,
                'stats'    => [
                    'total'    => $total,
                    'pending'  => $pending,
                    'approved' => $approved,
                    'rejected' => $rejected,
                    'velocity' => $velocity,
                ],
            ]), 200);
            return;
        }

        error_log("Supabase deletion_requests query failed: " . ($result['error'] ?? 'Unknown error'));
    }

    // ── Fallback: Local MySQL ───────────────────────────────────────────────
    $db = Database::getConnection();
    
    try {
        $sql = "SELECT dr.*, a.username, a.email 
                FROM deletion_requests dr 
                JOIN auth a ON dr.user_id = a.id 
                ORDER BY dr.created_at DESC";
        $stmt = $db->query($sql);
        $requests = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Stats
        $pending = 0;
        $approved = 0;
        foreach ($requests as $req) {
            if ($req['status'] === 'pending') $pending++;
            if ($req['status'] === 'approved') $approved++;
        }

        $velocity = count($requests) > 0 ? ($approved / count($requests)) * 100 : 0;

        $this->response($this->json([
            'requests' => $requests,
            'stats' => [
                'total' => count($requests),
                'pending' => $pending,
                'approved' => $approved,
                'velocity' => round($velocity, 1)
            ]
        ]), 200);
    } catch (\Exception $e) {
        $this->response($this->json(['error' => $e->getMessage()]), 500);
    }
};
