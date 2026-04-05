<?php

/**
 * API: users/update_profile
 * ========================
 * Updates user profile details and authentication data.
 */

use Aether\User;

${basename(__FILE__, '.php')} = function() 
{
    if (!$this->isAuthenticated()) {
        $this->response($this->json(['error' => 'Unauthorized']), 401);
    }

    if (!$this->paramsExists(['id'])) {
        $this->response($this->json(['error' => 'Missing user ID']), 400);
    }

    $id = (int)$this->_request['id'];

    try {
        $user = new User($id);
        $success = $user->updateProfile($this->_request);

        if ($success) {
            $this->response($this->json(['message' => 'Profile updated successfully.']), 200);
        } else {
            throw new \Exception("Failed to update profile database records.");
        }

    } catch (\Exception $e) {
        $this->response($this->json(['error' => $e->getMessage()]), 500);
    }
};
