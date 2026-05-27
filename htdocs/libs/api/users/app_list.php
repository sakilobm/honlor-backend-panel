<?php

/**
 * API Endpoint: /api/users/app_list
 * ===================================
 * Returns enriched Supabase app-user profiles for the admin panel.
 * Supports search, status filter (all/active/banned), and pagination.
 */

use Aether\SupabaseClient;

${basename(__FILE__, '.php')} = function () {

    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized.']), 401);
    }

    if (!\Session::hasPermission('users', 'view') && !\Session::hasPermission('users', 'manage')) {
        $this->response($this->json(['error' => 'Permission Denied']), 403);
    }

    $page   = max(1, (int)($this->_request['page']   ?? 1));
    $limit  = min(24, max(1, (int)($this->_request['limit']  ?? 12)));
    $filter = trim($this->_request['filter'] ?? '');
    $status = $this->_request['status'] ?? 'all'; // all | active | banned
    $offset = ($page - 1) * $limit;

    $select = 'id,username,full_name,avatar_url,email,website,created_at,updated_at,onboarding_completed,is_banned,banned_at,ban_reason';

    $queryParts = [
        'select=' . $select,
        'order=created_at.desc',
        'limit=' . $limit,
        'offset=' . $offset,
    ];

    // Status filter
    if ($status === 'banned') {
        $queryParts[] = 'is_banned=eq.true';
    } elseif ($status === 'active') {
        $queryParts[] = 'is_banned=eq.false';
        $queryParts[] = 'onboarding_completed=eq.true';
    }

    // Search filter (full_name OR username OR email)
    if (!empty($filter)) {
        $safe = str_replace(['*', '(', ')', ','], '', $filter);
        $queryParts[] = 'or=(full_name.ilike.*' . $safe . '*,username.ilike.*' . $safe . '*,email.ilike.*' . $safe . '*)';
    }

    $result = SupabaseClient::queryTableWithCount('profiles', $queryParts);

    if (!$result['success']) {
        $this->response($this->json(['error' => 'Failed to fetch app users.', 'details' => $result['error']]), 500);
    }

    $users = $result['data'];
    $total = $result['total'];

    // Summary stats (all users, unfiltered) — separate quick count queries
    $statsAll    = SupabaseClient::queryTableWithCount('profiles', ['select=id', 'limit=1']);
    $statsBanned = SupabaseClient::queryTableWithCount('profiles', ['select=id', 'is_banned=eq.true', 'limit=1']);
    $statsOnboarded = SupabaseClient::queryTableWithCount('profiles', ['select=id', 'onboarding_completed=eq.true', 'limit=1']);

    // New this week
    $weekAgo = (new DateTime('-7 days'))->format('c');
    $statsNewWeek = SupabaseClient::queryTableWithCount('profiles', [
        'select=id',
        'created_at=gte.' . urlencode($weekAgo),
        'limit=1',
    ]);

    $this->response($this->json([
        'users' => $users,
        'total' => $total,
        'page'  => $page,
        'limit' => $limit,
        'stats' => [
            'total'     => $statsAll['total'],
            'banned'    => $statsBanned['total'],
            'onboarded' => $statsOnboarded['total'],
            'new_week'  => $statsNewWeek['total'],
        ],
    ]), 200);
};
