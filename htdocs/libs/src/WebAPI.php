<?php

namespace Aether;

use Exception;
use Dotenv\Dotenv;

/**
 * WebAPI Bootstrap Class
 * =====================
 * PSR-4 Namespace: Aether\WebAPI
 */
class WebAPI
{
    /**
     * WebAPI Constructor.
     * Initializes configuration, .env, and session parameters.
     */
    public function __construct()
    {
        global $__site_config;

        $configPath = $this->resolveConfigPath();

        // 1. Load config.json file (Legacy support)
        if ($configPath && file_exists($configPath)) {
            $__site_config = file_get_contents($configPath);
        }

        // 2. Initialize Dotenv (.env validation layer)
        $dotenvRoot = HTDOCS_ROOT . '/..'; // Root project dir (where .env lives)
        if (file_exists($dotenvRoot . '/.env')) {
            $dotenv = Dotenv::createImmutable($dotenvRoot);
            $dotenv->load();

            // Strict Validation Layer: Ensure critical DB credentials exist
            $dotenv->required(['DB_HOST', 'DB_USER', 'DB_NAME'])->notEmpty();
        }

        // Establish DB connection early
        Database::getConnection();

        // --- Secure Session Cookie Configuration ---
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        $cookieParams = session_get_cookie_params();
        
        session_set_cookie_params([
            'lifetime' => $cookieParams['lifetime'],
            'path'     => $cookieParams['path'],
            'domain'   => $cookieParams['domain'],
            'secure'   => $isSecure, // Dynamic based on actual connection
            'httponly' => true,      // Prevent XSS session theft
            'samesite' => 'Lax'       // Balance between security and CSRF protection
        ]);
    }

    /**
     * Start the session and authorize if token exists.
     */
    public function initiateSession(): void
    {
        Session::start();

        if (Session::isset('session_token')) {
            try {
                Session::$usersession = UserSession::authorize(Session::get('session_token'));
            } catch (Exception $e) {
                // If authorization fails, clear the invalid token
                Session::delete('session_token');
            }
        }
    }

    /**
     * Resolves the path to config.json.
     */
    private function resolveConfigPath(): ?string
    {
        $candidates = [
            HTDOCS_ROOT . '/../project/config.json',
            HTDOCS_ROOT . '/../config.json',
            HTDOCS_ROOT . '/config.json',
        ];

        foreach ($candidates as $candidate) {
            $real = realpath($candidate);
            if ($real && file_exists($real)) {
                return $real;
            }
        }

        return null;
    }
}
