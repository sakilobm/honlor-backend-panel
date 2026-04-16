<?php

/**
 * API: users/authorize
 * ====================
 * Processes administrative authorization for identity handshakes.
 * Allows 'Approve' (role migration) or 'Reject' (handshake termination).
 */
$authorize = function() 
{
    if (!$this->isAuthenticated() || !\Aether\Session::isMaster()) {
        $this->response($this->json(['error' => 'Unauthorized Access']), 401);
    }

    if (!$this->paramsExists(['id', 'action'])) {
        $this->response($this->json(['error' => 'Missing authorization parameters']), 400);
    }

    $id = (int)$this->_request['id'];
    $action = $this->_request['action'];
    $role_id = isset($this->_request['role_id']) ? (int)$this->_request['role_id'] : 0;

    $user = new \Aether\User($id);
    if (!$user->id) {
        $this->response($this->json(['error' => 'Entity not found in vault']), 404);
    }

    $conn = \Aether\Database::getConnection();

    try {
        if ($action === 'approve') {
            // Default to 'Observer' if no role specified (Safe-by-default)
            if ($role_id === 0) {
                $sql_observer = "SELECT id FROM `roles` WHERE `slug` = 'observer' LIMIT 1";
                $role_id = (int)$conn->query($sql_observer)->fetchColumn();
            }

            $sql = "UPDATE `auth` SET `role_id` = ?, `request_pending` = 0 WHERE `id` = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$role_id, $id]);
            
            $this->response($this->json(['message' => 'Identity successfully migrated. Handshake complete.']), 200);
        } elseif ($action === 'reject') {
            // Clear the pending request flag without assigning a role
            $sql = "UPDATE `auth` SET `request_pending` = 0 WHERE `id` = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);

            $this->response($this->json(['message' => 'Identity Handshake terminated. User remains restricted.']), 200);
        } else {
            $this->response($this->json(['error' => 'Invalid authorization protocol']), 400);
        }
    } catch (Exception $e) {
        $this->response($this->json(['error' => $e->getMessage()]), 500);
    }
};
