<?php

/**
 * API Endpoint: /api/ads/create
 * =============================
 * Creates a new ad campaign.
 */

use App\Ad;

${basename(__FILE__, '.php')} = function () {
    
    // Auth check for write operation
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized access.']), 401);
    }

    // Validate required fields
    if ($this->paramsExists(['name', 'type', 'budget', 'start_date', 'end_date'])) {
        
        $name      = $this->_request['name'];
        $type      = $this->_request['type'];
        $budget    = (float)$this->_request['budget'];
        $startDate = $this->_request['start_date'];
        $endDate   = $this->_request['end_date'];

        $id = Ad::createAd($name, $type, $budget, $startDate, $endDate);

        if ($id) {
            $this->response($this->json([
                'message' => 'Campaign created successfully.',
                'id'      => $id
            ]), 201);
        } else {
            $this->response($this->json(['error' => 'Failed to create campaign.']), 500);
        }

    } else {
        $this->response($this->json(['error' => 'Missing required parameters.']), 400);
    }
};
