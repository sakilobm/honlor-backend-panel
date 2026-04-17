<?php

/**
 * API Endpoint: /api/messages/action
 * =====================================
 * Governance controller for stream moderation.
 * Actions: flag, unflag, purge.
 */

${basename(__FILE__, '.php')} = function () {

    if (!\Session::isAuthenticated()) {
        $this->response($this->json(['error' => 'Identity mismatch. Unauthorized.']), 401);
    }

    if (!$this->paramsExists(['id', 'action'])) {
        $this->response($this->json(['error' => 'Protocol error: Missing parameters.']), 400);
    }

    $id = (int)$this->_request['id'];
    $action = $this->_request['action'];

    try {
        $msg = new \App\Message($id);

        switch ($action) {
            case 'flag':
                $msg->flag();
                $message = "Packet #{$id} isolated in the Chamber.";
                break;
            case 'unflag':
                $msg->unflag();
                $message = "Packet #{$id} restored to safe stream.";
                break;
            case 'purge':
                $msg->deleteRow();
                $message = "Packet #{$id} purged from global ledger.";
                break;
            default:
                throw new Exception("Unknown governance protocol.");
        }

        $this->response($this->json([
            'message' => $message,
            'metrics' => [
                'flagged' => \App\Message::getFlaggedCount()
            ]
        ]), 200);

    } catch (Exception $e) {
        $this->response($this->json(['error' => $e->getMessage()]), 400);
    }
};
