<?php

/**
 * API Endpoint: /api/app_config/list
 * ====================================
 * Fetches all app version configurations and key orchestration telemetry.
 */

use App\AppConfig;

${basename(__FILE__, '.php')} = function () {

    // Ensure session is active (Admin only)
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    try {
        $configs = AppConfig::getAll();

        // Calculate dynamic telemetry metrics
        $total = count($configs);
        $mandatoryCount = 0;
        $activeVersion = 'v0.0.0';

        if ($total > 0) {
            // Use is_active flag; fallback to latest ID if none is set
            foreach ($configs as $cfg) {
                if ((int)($cfg['is_active'] ?? 0) === 1) {
                    $activeVersion = $cfg['latest_version'];
                }
                if ((int)($cfg['is_mandatory'] ?? 0) === 1) {
                    $mandatoryCount++;
                }
            }
            // Fallback: no row has is_active set yet
            if ($activeVersion === 'v0.0.0') {
                $activeVersion = $configs[0]['latest_version'] ?? 'v0.0.0';
            }
        }

        $enforcementRatio = $total > 0 ? round(($mandatoryCount / $total) * 100, 1) : 0;

        $this->response($this->json([
            'configs' => $configs,
            'metrics' => [
                'total_releases' => $total,
                'active_version' => $activeVersion,
                'enforcement_ratio' => $enforcementRatio . '%',
                'sync_status' => 'ONLINE'
            ]
        ]), 200);

    } catch (Exception $e) {
        error_log("API Error /app_config/list: " . $e->getMessage());
        $this->response($this->json(['error' => 'Internal Server Error', 'details' => $e->getMessage()]), 500);
    }
};
