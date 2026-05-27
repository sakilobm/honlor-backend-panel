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
    $user = Session::getUser();
    $page = Session::getCurrentPageIdentifier();

    // --- ZERO-TRUST CLEARANCE GUARD ---
    // Block any user who hasn't been assigned a Security Cluster (role_id = 0)
    // Master Admins are exempt from this protocol
    if ($user && (int)$user->getRoleId() === 0 && !$user->isMaster()) {
        $page = 'clearance_pending';
    }

    // Access Control: Only Master Admin can access Roles Studio
    if ($page === 'roles' && !Session::isMaster()) {
        header('Location: /admin?page=dashboard');
        exit;
    }

    // --- DYNAMIC PERMISSION CONTROL ---
    $pagePermissionMap = [
        'users'             => ['users', 'view'],
        'messages'          => ['messages', 'view'],
        'channels'          => ['channels', 'view'],
        'ads'               => ['ads', 'view'],
        'reports'           => ['reports', 'view'],
        'deletion_requests' => ['deletion', 'view'],
        'analytics'         => ['analytics', 'view'],
        'policy_editor'     => ['policy', 'view'],
        'app_config'        => ['settings', 'view'],
        'settings'          => ['settings', 'view'],
        'logs'              => ['logs', 'view'],
    ];

    if (isset($pagePermissionMap[$page])) {
        list($resource, $action) = $pagePermissionMap[$page];
        if (!Session::hasPermission($resource, $action)) {
            if (isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Permission Denied']);
                exit;
            } else {
                header('Location: ' . Session::url('admin?page=dashboard'));
                exit;
            }
        }
    }

    // If AJAX request, render only the partial template
    if (isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
        Session::loadTemplate('admin/' . $page);
    } else {
        if ($page === 'clearance_pending') {
            // Render the isolated restricted layout (Bulletproof Gate)
            Session::loadTemplate('admin/_restricted_gate');
        } else {
            // Render the full admin layout (Authorized Dashboard)
            Session::renderPageOfAdmin();
        }
    }

} else {
    header('Location: /login');
    exit;
}
