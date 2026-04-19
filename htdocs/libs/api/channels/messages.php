<?php

namespace Api;

use App\Channel;
use Session;

/**
 * Endpoint: /api/channels/messages
 * Fetches the message history for a specific node cluster.
 */
$messages = function() {
    Session::ensureLogin();

    $channelId = (int)($this->_request['id'] ?? 0);
    if (!$channelId) {
        $this->response($this->json(['error' => 'Valid Node ID required.']), 400);
    }

    $channel = Channel::getById($channelId);
    
    // Graceful handling for deleted channels
    if (!$channel) {
        $this->response($this->json([
            'success' => true,
            'messages' => [],
            'members' => [],
            'channel' => null,
            'note' => 'Node decommissioned or target unreachable.'
        ]), 200);
    }

    $messages = Channel::getMessages($channelId);
    $members = Channel::getDetailedMembers($channelId);

    $this->response($this->json([
        'success' => true,
        'channel' => $channel,
        'messages' => $messages,
        'members' => $members
    ]), 200);
};
