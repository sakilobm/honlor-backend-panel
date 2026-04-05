<?php

/**
 * API: ads/delete
 * ===============
 * Deletes an existing ad campaign.
 */

use App\Ad;

${basename(__FILE__, '.php')} = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!$this->paramsExists(['id'])) {
        $this->response($this->json(['error' => 'Missing campaign ID']), 400);
    }

    $id = (int)$this->_request['id'];

    try {
        $success = Ad::delete($id);
        if ($success) $this->response($this->json(['message' => 'Campaign deleted successfully.']), 200);
        else throw new Exception("Database record deletion failed.");

    } catch (Exception $e) {
        $this->response($this->json(['error' => $e->getMessage()]), 500);
    }
};
