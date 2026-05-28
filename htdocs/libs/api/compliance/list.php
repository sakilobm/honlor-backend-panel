<?php

/**
 * API Endpoint: /api/compliance/list
 * =================================
 * Returns the incident queue from the Supabase `reports` table,
 * enriched with reporter and reported user profile data.
 */

use Aether\SupabaseClient;

${basename(__FILE__, '.php')} = function () {

    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    // ── Fetch all reports ────────────────────────────────────────────────────
    $result = SupabaseClient::queryTable('reports', [
        'select' => '*',
        'order'  => 'created_at.desc',
    ]);

    if (!$result['success']) {
        error_log("API /compliance/list — reports query failed: " . ($result['error'] ?? 'unknown'));
        $this->response($this->json(['error' => 'Failed to fetch reports.']), 500);
    }

    $rows = $result['data'] ?? [];

    // ── Enrich with profile data ─────────────────────────────────────────────
    $profileMap = [];
    $userIds = [];
    foreach ($rows as $r) {
        if (!empty($r['reporter_id'])) $userIds[] = $r['reporter_id'];
        if (!empty($r['reported_id'])) $userIds[] = $r['reported_id'];
    }
    $userIds = array_values(array_unique(array_filter($userIds)));

    if (!empty($userIds)) {
        $pResult = SupabaseClient::queryTable('profiles', [
            'select' => 'id,username,full_name,avatar_url',
            'id'     => 'in.(' . implode(',', $userIds) . ')',
        ]);
        foreach (($pResult['data'] ?? []) as $p) {
            if (is_array($p) && !empty($p['id'])) $profileMap[$p['id']] = $p;
        }
    }

    // ── Enrich with channel data ─────────────────────────────────────────────
    $channelMap = [];
    $channelIds = [];
    foreach ($rows as $r) {
        if (!empty($r['channel_id'])) $channelIds[] = $r['channel_id'];
    }
    // Only keep UUID-format channel IDs (Stream Chat DM CIDs like "!members-..." are not in channels table)
    $channelIds = array_values(array_unique(array_filter($channelIds, function ($cid) {
        return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $cid);
    })));

    if (!empty($channelIds)) {
        $cResult = SupabaseClient::queryTable('channels', [
            'select'            => 'stream_channel_id,name,image_url',
            'stream_channel_id' => 'in.(' . implode(',', $channelIds) . ')',
        ]);
        if ($cResult['success']) {
            foreach (($cResult['data'] ?? []) as $c) {
                if (is_array($c) && !empty($c['stream_channel_id'])) {
                    $channelMap[$c['stream_channel_id']] = $c;
                }
            }
        }
    }

    // ── Severity map ─────────────────────────────────────────────────────────
    $severityMap = [
        'Violence'             => 'Critical',
        'Nudity or Pornography'=> 'Critical',
        'Illegal Drugs/Content'=> 'Critical',
        'Abuse or Harassment'  => 'Moderate',
        'Spam'                 => 'Moderate',
        'Personal Details'     => 'Moderate',
    ];

    // ── Build response ───────────────────────────────────────────────────────
    $pending  = 0;
    $resolved = 0;
    $dismissed = 0;
    $incidents = [];
    $history   = [];

    foreach ($rows as $r) {
        $status = strtolower($r['status'] ?? 'pending');

        if ($status === 'pending') {
            $pending++;
        } elseif ($status === 'resolved') {
            $resolved++;
        } elseif ($status === 'dismissed') {
            $dismissed++;
        }

        $rid  = $r['reporter_id'] ?? null;
        $rpro = $rid ? ($profileMap[$rid] ?? []) : [];

        $tid  = $r['reported_id'] ?? null;
        $tpro = $tid ? ($profileMap[$tid] ?? []) : [];

        $reason   = $r['reason'] ?? 'Other';
        $severity = $severityMap[$reason] ?? 'Low';

        $row = [
            'id'                 => $r['id'],
            'report_type'        => $r['report_type'] ?? 'user',
            'reporter_id'        => $rid,
            'reporter_username'  => $rpro['username']   ?? null,
            'reporter_full_name' => $rpro['full_name']  ?? null,
            'reporter_avatar'    => $rpro['avatar_url'] ?? null,
            'reported_id'        => $tid,
            'reported_username'  => $tpro['username']   ?? null,
            'reported_full_name' => $tpro['full_name']  ?? null,
            'reported_avatar'    => $tpro['avatar_url'] ?? null,
            'channel_id'         => $r['channel_id']   ?? null,
            'channel_name'       => isset($r['channel_id']) ? ($channelMap[$r['channel_id']]['name']      ?? null) : null,
            'channel_image'      => isset($r['channel_id']) ? ($channelMap[$r['channel_id']]['image_url'] ?? null) : null,
            'reason'             => $reason,
            'details'            => $r['details']      ?? null,
            'status'             => $r['status']       ?? 'pending',
            'admin_notes'        => $r['admin_notes']  ?? null,
            'reviewed_by'        => $r['reviewed_by']  ?? null,
            'severity'           => $severity,
            'created_at'         => $r['created_at']   ?? null,
        ];

        $incidents[] = $row;

        if ($status !== 'pending') {
            $history[] = [
                'id'         => $r['id'],
                'action'     => ucfirst($status),
                'reason'     => $reason,
                'created_at' => $r['created_at'] ?? null,
            ];
        }
    }

    $total    = count($rows);
    $velocity = $total > 0 ? round((($resolved + $dismissed) / $total) * 100, 1) : 0;

    $this->response($this->json([
        'incidents' => $incidents,
        'history'   => array_slice($history, 0, 20),
        'metrics'   => [
            'active_queue'    => $pending,
            'resolved'        => $resolved,
            'dismissed'       => $dismissed,
            'total_incidents' => $total,
            'velocity'        => $velocity,
        ],
    ]), 200);
};
