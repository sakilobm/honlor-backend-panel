<?php

namespace Api;

use App\Channel;
use Session;

/**
 * Endpoint: /api/channels/send
 * Synchronizes a new message packet to the node cluster.
 */
return function($params) {
    $user = Session::ensureLogin();

    $channelId = (int)($params['channel_id'] ?? 0);
    $message = trim($params['message'] ?? '');
    $type = $params['type'] ?? 'text';

    if (!$channelId || empty($message)) {
        return ['error' => 'Incomplete data packet. Transmission failed.'];
    }

    $messageId = Channel::postMessage($channelId, $user['id'], $message, $type);

    if ($messageId) {
        return [
            'success' => true,
            'message_id' => $messageId,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    return ['error' => 'Internal sync failure. Check node health.'];
};
