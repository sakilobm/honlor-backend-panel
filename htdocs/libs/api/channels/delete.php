<?php

/**
 * API: channels/delete
 * ====================
 */
$delete = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!$this->paramsExists(['id'])) {
        $this->response($this->json(['error' => 'Missing node identifier']), 400);
    }

    $id = (int)$this->_request['id'];

    try {
        $channel = new \App\Channel($id);
        if ($channel->delete()) {
            $this->response($this->json(['message' => 'Node decommissioned successfully']), 200);
        } else {
            $this->response($this->json(['error' => 'Failed to remove node from network']), 500);
        }
    } catch (\Exception $e) {
        $this->response($this->json(['error' => $e->getMessage()]), 404);
    }
};
