<?php

/**
 * API Endpoint: /api/ads/list
 * ===========================
 * Returns a JSON array of all campaigns.
 */

use App\Ad;

${basename(__FILE__, '.php')} = function () {
    
    // Auth check
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    try {
        $ads = Ad::getAllAds();
        $this->response($this->json([
            'ads' => $ads,
            'metrics' => Ad::getMarketingMetrics()
        ]), 200);
    } catch (Exception $e) {
        error_log("API Error /ads/list: " . $e->getMessage());
        $this->response($this->json(['error' => 'Internal Server Error']), 500);
    }
};
