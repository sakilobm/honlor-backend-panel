<?php

/**
 * API Endpoint: /api/compliance/list
 * =================================
 * Returns the incident queue and safety telemetry.
 */

use App\Compliance;

${basename(__FILE__, '.php')} = function () {
    
    // Auth check
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    try {
        $this->response($this->json([
            'incidents' => Compliance::getIncidentQueue(),
            'history'   => Compliance::getResolutionHistory(),
            'metrics'   => Compliance::getSafetyTelemetry()
        ]), 200);
    } catch (Exception $e) {
        error_log("API Error /compliance/list: " . $e->getMessage());
        $this->response($this->json(['error' => 'Internal Server Error']), 500);
    }
};
