<?php

namespace Api;

use App\Channel;
use Session;

/**
 * Endpoint: /api/channels/send
 * Transmits a message through the specified node cluster.
 */
$send = function() {
    $user = Session::ensureLogin();

    $channelId = (int)($this->_request['channel_id'] ?? 0);
    $message = $this->_request['message'] ?? '';

    if (!$channelId || empty($message)) {
        $this->response($this->json(['error' => 'Valid node ID and message payload required.']), 400);
    }

    $channel = Channel::getById($channelId);
    if (!$channel) {
        $this->response($this->json(['error' => 'Target node unreachable.']), 404);
    }

    $msgId = Channel::postMessage($channelId, $user->id, $message);

    if ($msgId) {
        $this->response($this->json([
            'success' => true,
            'message_id' => $msgId,
            'status' => 'transmitted'
        ]), 200);
    } else {
        $this->response($this->json(['error' => 'Transmission failure.']), 500);
    }
};
