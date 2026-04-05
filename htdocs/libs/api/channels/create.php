<?php

/**
 * API: channels/create
 * ====================
 */
$create = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!$this->paramsExists(['name', 'type'])) {
        $this->response($this->json(['error' => 'Missing name or type']), 400);
    }

    $name = $this->_request['name'];
    $type = $this->_request['type'];

    if (\App\Channel::create($name, $type)) {
        $this->response($this->json(['message' => 'Channel created successfully']), 200);
    } else {
        $this->response($this->json(['error' => 'Failed to create channel']), 500);
    }
};
