<?php

/**
 * API: channels/create
 * ====================
 * Creates a new channel with members and settings.
 */

use App\Channel;

${basename(__FILE__, '.php')} = function() 
{
    if (!\Session::isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!$this->paramsExists(['name', 'type'])) {
        $this->response($this->json(['error' => 'Missing required fields (name, type)']), 400);
    }

    try {
        $data = $this->_request;
        $data['owner_id'] = \Session::getUser()->id;
        
        $channelId = Channel::createWithDetails($data);
        
        if ($channelId) {
            $this->response($this->json([
                'message' => 'Channel registry successful.',
                'channel_id' => $channelId
            ]), 200);
        } else {
            throw new Exception("Database record creation failed.");
        }

    } catch (Exception $e) {
        $this->response($this->json(['error' => $e->getMessage()]), 500);
    }
};
