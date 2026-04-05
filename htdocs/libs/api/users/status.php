<?php

/**
 * API: users/status
 * =================
 */
$status = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!$this->paramsExists(['id', 'action'])) {
        $this->response($this->json(['error' => 'Missing id or action']), 400);
    }

    $id     = (int)$this->_request['id'];
    $action = $this->_request['action'];

    try {
        $user = new \Aether\User($id);
        $result = false;

        switch ($action) {
            case 'toggle_block':
                $result = $user->toggleBlock();
                $msg = $user->getBlocked() ? 'User blocked' : 'User unblocked';
                break;
            case 'toggle_active':
                $result = $user->toggleActive();
                $msg = $user->getActive() ? 'User activated' : 'User deactivated';
                break;
            default:
                throw new \Exception('Invalid action');
        }

        if ($result) {
            $this->response($this->json(['message' => $msg]), 200);
        } else {
            $this->response($this->json(['error' => 'Update failed']), 500);
        }

    } catch (\Exception $e) {
        $this->response($this->json(['error' => $e->getMessage()]), 404);
    }
};
