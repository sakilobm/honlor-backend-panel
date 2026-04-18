<?php

namespace Api;

use App\Channel;
use Session;

/**
 * Endpoint: /api/channels/messages
 * Fetches the message history for a specific node cluster.
 */
return function($params) {
    Session::ensureLogin();

    $channelId = (int)($params['id'] ?? 0);
    if (!$channelId) {
        return ['error' => 'Valid Node ID required.'];
    }

    $messages = Channel::getMessages($channelId);
    $members = Channel::getDetailedMembers($channelId);
    $channel = Channel::getById($channelId);

    return [
        'success' => true,
        'channel' => $channel,
        'messages' => $messages,
        'members' => $members
    ];
};
