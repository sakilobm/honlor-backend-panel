<?php

/**
 * API: ads/update
 * ===============
 * Updates an existing ad campaign.
 */

use App\Ad;

${basename(__FILE__, '.php')} = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!$this->paramsExists(['id', 'name', 'type', 'budget', 'start_date', 'end_date'])) {
        $this->response($this->json(['error' => 'Missing required fields']), 400);
    }

    $id = (int)$this->_request['id'];

    try {
        $success = Ad::update($id, $this->_request);
        if ($success) $this->response($this->json(['message' => "Campaign '{$id}' updated successfully."]), 200);
        else throw new \Exception("Database update failed.");

    } catch (\Exception $e) {
        $this->response($this->json(['error' => $e->getMessage()]), 500);
    }
};
