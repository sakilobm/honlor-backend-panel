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

    // ── Try Supabase Edge Function first ────────────────────────────────────
    $supabaseUrl = get_config('supabase_url');
    $supabaseKey = get_config('supabase_service_key');

    if (!empty($supabaseUrl) && !empty($supabaseKey)) {
        $result = SupabaseClient::invokeFunction('request-account-deletion', [
            'action' => 'list_requests',
        ]);

        if ($result['success'] && !empty($result['data'])) {
            $data = $result['data'];

            // Map Supabase response fields to dashboard expected format
            $requests = [];
            foreach (($data['requests'] ?? []) as $req) {
                $requests[] = [
                    'id'         => $req['id'] ?? '',
                    'username'   => $req['full_name'] ?? ($req['email'] ?? 'Unknown'),
                    'email'      => $req['email'] ?? '',
                    'reason'     => $req['reason'] ?? null,
                    'status'     => $req['status'] ?? 'pending',
                    'created_at' => $req['created_at'] ?? null,
                    'admin_note' => $req['admin_note'] ?? null,
                    'reviewed_at'=> $req['reviewed_at'] ?? null,
                    'reviewed_by'=> $req['reviewed_by'] ?? null,
                ];
            }

            $this->response($this->json([
                'requests' => $requests,
                'stats'    => $data['stats'] ?? [
                    'total'    => count($requests),
                    'pending'  => 0,
                    'approved' => 0,
                    'velocity' => 0,
                ],
            ]), 200);
            return;
        }

        // If the edge function call failed, log and fall through to local DB
        error_log("Supabase deletion_requests failed: " . ($result['error'] ?? 'Unknown error'));
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
