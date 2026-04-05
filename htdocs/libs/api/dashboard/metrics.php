<?php

/**
 * API Endpoint: /api/dashboard/metrics
 * ====================================
 * Returns overview statistics and user growth chart data for 7 or 30 days.
 */

use App\DashboardStats;

${basename(__FILE__, '.php')} = function () {

    // Ensure session is active (Admin only)
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    // Default range is 7 days, but can be 30
    $range = (int)($this->_request['range'] ?? 7);
    if ($range !== 7 && $range !== 30) {
        $range = 7;
    }

    try {
        $data = [
            'total_users'       => DashboardStats::getTotalUsers(),
            'messages_today'    => DashboardStats::getMessagesToday(),
            'active_channels'   => DashboardStats::getActiveChannels(),
            'active_ads'        => \App\Ad::getActiveCount(),
            'growth_data'       => DashboardStats::getUserGrowth($range),
            'server_health'     => [
                'status' => 'Operational',
                'uptime' => '99.99%',
                'nodes'  => 34
            ]
        ];

        $this->response($this->json($data), 200);

    } catch (Exception $e) {
        error_log("API Error /dashboard/metrics: " . $e->getMessage());
        $this->response($this->json(['error' => 'Internal Server Error']), 500);
    }
};
