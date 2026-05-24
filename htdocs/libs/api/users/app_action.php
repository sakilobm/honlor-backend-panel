<?php

/**
 * API Endpoint: /api/users/app_action
 * =====================================
 * Handles admin actions on Supabase app users.
 *
 * Actions:
 *   get_profile   — fetch enriched single user + stats (friends, channels, reports)
 *   ban           — set is_banned=true, record ban_reason & banned_at
 *   unban         — clear ban fields
 *   update_name   — update full_name / username
 */

use Aether\SupabaseClient;

${basename(__FILE__, '.php')} = function () {

    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized.']), 401);
    }

    $action = $this->_request['action'] ?? null;
    $userId = $this->_request['user_id'] ?? null;

    if (!$action) {
        $this->response($this->json(['error' => 'Missing action.']), 400);
    }

    // ── GET PROFILE ───────────────────────────────────────────────────────────
    if ($action === 'get_profile') {
        if (!$userId) {
            $this->response($this->json(['error' => 'Missing user_id.']), 400);
        }

        $select = 'id,username,full_name,avatar_url,email,website,created_at,updated_at,onboarding_completed,is_banned,banned_at,ban_reason,settings';
        $result = SupabaseClient::queryTable('profiles', [
            'select=' . $select,
            'id=eq.' . urlencode($userId),
            'limit=1',
        ]);

        if (!$result['success'] || empty($result['data'])) {
            $this->response($this->json(['error' => 'User not found.']), 404);
        }

        $profile = $result['data'][0];

        // Aggregate stats: friends, channels joined, reports received
        $friendsResult  = SupabaseClient::queryTableWithCount('friends',         ['select=id', 'user_id=eq.' . urlencode($userId), 'limit=1']);
        $channelsResult = SupabaseClient::queryTableWithCount('channel_members', ['select=member_id', 'member_id=eq.' . urlencode($userId), 'limit=1']);
        $reportsResult  = SupabaseClient::queryTableWithCount('reports',         ['select=id', 'reported_id=eq.' . urlencode($userId), 'limit=1']);

        $profile['friend_count']  = $friendsResult['total']  ?? 0;
        $profile['channel_count'] = $channelsResult['total'] ?? 0;
        $profile['report_count']  = $reportsResult['total']  ?? 0;

        $this->response($this->json(['success' => true, 'user' => $profile]), 200);
    }

    // ── BAN ───────────────────────────────────────────────────────────────────
    if ($action === 'ban') {
        if (!$userId) {
            $this->response($this->json(['error' => 'Missing user_id.']), 400);
        }

        $reason = trim($this->_request['reason'] ?? 'Banned by administrator.');
        $result = SupabaseClient::patchRow('profiles', 'id', $userId, [
            'is_banned'  => true,
            'banned_at'  => (new DateTime())->format('c'),
            'ban_reason' => $reason,
        ]);

        if (!$result['success']) {
            $this->response($this->json(['error' => 'Ban failed.', 'details' => $result['error']]), 500);
        }

        $this->response($this->json(['success' => true, 'message' => 'User banned successfully.']), 200);
    }

    // ── UNBAN ─────────────────────────────────────────────────────────────────
    if ($action === 'unban') {
        if (!$userId) {
            $this->response($this->json(['error' => 'Missing user_id.']), 400);
        }

        $result = SupabaseClient::patchRow('profiles', 'id', $userId, [
            'is_banned'  => false,
            'banned_at'  => null,
            'ban_reason' => null,
        ]);

        if (!$result['success']) {
            $this->response($this->json(['error' => 'Unban failed.', 'details' => $result['error']]), 500);
        }

        $this->response($this->json(['success' => true, 'message' => 'User unbanned successfully.']), 200);
    }

    // ── UPDATE NAME ───────────────────────────────────────────────────────────
    if ($action === 'update_name') {
        if (!$userId) {
            $this->response($this->json(['error' => 'Missing user_id.']), 400);
        }

        $updateData = [];
        if (!empty($this->_request['full_name'])) {
            $updateData['full_name'] = trim($this->_request['full_name']);
        }
        if (!empty($this->_request['username'])) {
            $updateData['username'] = trim($this->_request['username']);
        }
        if (isset($this->_request['website'])) {
            $updateData['website'] = trim($this->_request['website']);
        }

        if (empty($updateData)) {
            $this->response($this->json(['error' => 'No update fields provided.']), 400);
        }

        $result = SupabaseClient::patchRow('profiles', 'id', $userId, $updateData);

        if (!$result['success']) {
            $this->response($this->json(['error' => 'Update failed.', 'details' => $result['error']]), 500);
        }

        $this->response($this->json(['success' => true, 'message' => 'Profile updated successfully.']), 200);
    }

    $this->response($this->json(['error' => 'Unknown action: ' . $action]), 400);
};
