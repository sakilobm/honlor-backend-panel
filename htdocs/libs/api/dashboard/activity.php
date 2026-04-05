<?php

/**
 * API Endpoint: /api/dashboard/activity
 * =====================================
 * Returns 10 most recent activity logs for the dashboard feed.
 */

use App\DashboardStats;

${basename(__FILE__, '.php')} = function () {

    // Ensure session is active (Admin only)
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    try {
        $activity = DashboardStats::getRecentActivity(10);
        
        $this->response($this->json([
            'activity' => $activity
        ]), 200);

    } catch (Exception $e) {
        error_log("API Error /dashboard/activity: " . $e->getMessage());
        $this->response($this->json(['error' => 'Internal Server Error']), 500);
    }
};
