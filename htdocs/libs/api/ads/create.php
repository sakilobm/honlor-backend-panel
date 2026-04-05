<?php

/**
 * API: ads/create
 * ===============
 * Creates a new ad campaign.
 */

use App\Ad;

${basename(__FILE__, '.php')} = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!$this->paramsExists(['name', 'type', 'budget', 'start_date', 'end_date'])) {
        $this->response($this->json(['error' => 'Missing required fields']), 400);
    }

    try {
        $success = Ad::create($this->_request);
        if ($success) $this->response($this->json(['message' => 'Campaign created successfully.']), 200);
        else throw new Exception("Database record creation failed.");

    } catch (Exception $e) {
        $this->response($this->json(['error' => $e->getMessage()]), 500);
    }
};
