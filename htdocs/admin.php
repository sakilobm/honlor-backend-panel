<?php

/**
 * Advanced Admin Entry Point
 */

require_once 'libs/load.php';

use Aether\Session;
use Aether\UserSession;

// Handle logout
if (isset($_GET['logout']) && Session::isset('session_token')) {
    try {
        $sess = new UserSession(Session::get('session_token'));
        $sess->removeSession();
    } catch (Exception $e) {}
    Session::unset();
    Session::destroy();
    header('Location: /');
    exit;
}

// Ensure Login gate
if (Session::isAuthenticated()) {
    // If AJAX request, render only the partial template
    if (isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
        Session::loadTemplate('admin/' . Session::getCurrentPageIdentifier());
    } else {
        // Render the full admin layout (Template Inheritance)
        Session::renderPageOfAdmin();
    }
} else {
    header('Location: /login');
    exit;
}
