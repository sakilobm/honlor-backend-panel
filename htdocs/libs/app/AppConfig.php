<?php

namespace App;

use Aether\Database;
use Aether\Session;
use PDO;
use Exception;

/**
 * AppConfig Model
 * =====================================
 * Manages client app version configuration, update URLs, and mandatory upgrades.
 */
class AppConfig
{
    /**
     * Ensure the app_config table exists and has seed data.
     */
    public static function ensureTable(): void
    {
        try {
            $db = Database::getConnection();

            // Create Table if missing (MySQL syntax)
            $db->exec("CREATE TABLE IF NOT EXISTS `app_config` (
                `id` bigint NOT NULL AUTO_INCREMENT,
                `latest_version` varchar(255) NOT NULL,
                `is_mandatory` tinyint(1) NULL DEFAULT 0,
                `is_active` tinyint(1) NULL DEFAULT 0,
                `update_url` text NOT NULL,
                `description` text NULL,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;");

            // Add is_active column if it doesn't exist (migration for existing installs)
            try {
                $db->exec("ALTER TABLE `app_config` ADD COLUMN `is_active` tinyint(1) NULL DEFAULT 0 AFTER `is_mandatory`");
            } catch (Exception $e) {
                // Column already exists — ignore
            }

            // Seed if empty
            $stmt = $db->query("SELECT COUNT(*) as count FROM `app_config`");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ((int)($result['count'] ?? 0) === 0) {
                $db->exec("INSERT INTO `app_config` (`latest_version`, `is_mandatory`, `is_active`, `update_url`, `description`) VALUES
                ('v1.2.0', 1, 1, 'https://apps.apple.com/app/honlor', 'Critical security patch resolving core credential encryption flaws.'),
                ('v1.1.5', 0, 0, 'https://play.google.com/store/apps/honlor', 'Performance optimization for real-time telemetry streams.');");
            }
        } catch (Exception $e) {
            error_log("Failsafe Table Check Error inside AppConfig: " . $e->getMessage());
        }
    }

    /**
     * Retrieve all version configurations from the database.
     */
    public static function getAll(): array
    {
        self::ensureTable();
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM `app_config` ORDER BY `id` DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get the currently active version configuration.
     */
    public static function getActive(): ?array
    {
        self::ensureTable();
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM `app_config` WHERE `is_active` = 1 ORDER BY `id` DESC LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Set a specific config row as the active version.
     * Atomically clears all other rows' is_active flag first.
     */
    public static function setActive(int $id): bool
    {
        self::ensureTable();
        $db = Database::getConnection();

        // Verify target exists
        $target = self::get($id);
        if (!$target) return false;

        $db->beginTransaction();
        try {
            // Clear all active flags
            $db->exec("UPDATE `app_config` SET `is_active` = 0");
            // Set the target as active
            $stmt = $db->prepare("UPDATE `app_config` SET `is_active` = 1 WHERE `id` = ?");
            $stmt->execute([$id]);
            $db->commit();

            self::logAction("Set version {$target['latest_version']} (ID: {$id}) as the active build target");
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            error_log("AppConfig::setActive transaction failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch a single version configuration by ID.
     */
    public static function get(int $id): ?array
    {
        self::ensureTable();
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM `app_config` WHERE `id` = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Create a new version release configuration.
     * If $autoActivate is true, immediately marks this row as the active version.
     */
    public static function create(string $version, bool $isMandatory, string $url, ?string $description, bool $autoActivate = false): int
    {
        self::ensureTable();
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO `app_config` (`latest_version`, `is_mandatory`, `is_active`, `update_url`, `description`) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            trim($version),
            $isMandatory ? 1 : 0,
            0, // not active by default; caller calls setActive() separately if needed
            trim($url),
            $description ? trim($description) : null
        ]);
        $insertId = (int)$db->lastInsertId();

        // Audit Logging
        self::logAction("Published new app configuration version: {$version} (ID: {$insertId})");

        if ($autoActivate && $insertId > 0) {
            self::setActive($insertId);
        }

        return $insertId;
    }

    /**
     * Update an existing version release configuration.
     */
    public static function update(int $id, array $data): bool
    {
        self::ensureTable();
        $db = Database::getConnection();
        
        $fields = [];
        $values = [];
        $allowed = ['latest_version', 'is_mandatory', 'update_url', 'description'];
        
        foreach ($data as $k => $v) {
            if (!in_array($k, $allowed)) continue;
            
            if ($k === 'is_mandatory') {
                $v = $v ? 1 : 0;
            } elseif ($v !== null) {
                $v = trim($v);
            }
            
            $fields[] = "`$k` = ?";
            $values[] = $v;
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $stmt = $db->prepare("UPDATE `app_config` SET " . implode(',', $fields) . " WHERE `id` = ?");
        $success = $stmt->execute($values);

        if ($success) {
            $ver = $data['latest_version'] ?? '';
            $desc = $ver ? "for version {$ver}" : "(ID: {$id})";
            self::logAction("Updated app configuration metadata {$desc}");
        }

        return $success;
    }

    /**
     * Delete an existing version release configuration.
     */
    public static function delete(int $id): bool
    {
        self::ensureTable();
        $db = Database::getConnection();
        
        // Retrieve version before deleting for the log audit
        $config = self::get($id);
        $version = $config ? $config['latest_version'] : $id;
        
        $stmt = $db->prepare("DELETE FROM `app_config` WHERE `id` = ?");
        $success = $stmt->execute([$id]);

        if ($success) {
            self::logAction("Decommissioned app version config: {$version}");
        }

        return $success;
    }

    /**
     * Helper to write structured action trails into logs table.
     */
    private static function logAction(string $message): void
    {
        try {
            $user = Session::getUser();
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO logs (user_id, action, level, ip) VALUES (?, ?, 'info', ?)");
            $stmt->execute([
                $user ? $user->id : 0,
                $message,
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } catch (Exception $e) {
            error_log("Failed writing action log in AppConfig: " . $e->getMessage());
        }
    }
}
