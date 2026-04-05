<?php

/**
 * API: messages/flag
 * ==================
 */
$flag = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!$this->paramsExists(['id', 'action'])) {
        $this->response($this->json(['error' => 'Missing id or action']), 400);
    }

    $id = (int)$this->_request['id'];
    $action = $this->_request['action'];

    try {
        $message = new \App\Message($id);
        $result = false;

        switch ($action) {
            case 'flag':
                $result = $message->flag();
                $msg = 'Message flagged';
                break;
            case 'delete':
                $result = $message->deleteRow();
                $msg = 'Message deleted';
                break;
            default:
                throw new \Exception('Invalid action');
        }

        if ($result) {
            $this->response($this->json(['message' => $msg]), 200);
        } else {
            $this->response($this->json(['error' => 'Action failed']), 500);
        }

    } catch (\Exception $e) {
        $this->response($this->json(['error' => $e->getMessage()]), 404);
    }
};
