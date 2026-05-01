<?php

namespace App;

use Aether\Database;
use PDO;
use Exception;

/**
 * Webhook Model
 * =============
 * Manages registered webhook endpoints, delivery logs, and health status.
 */
class Webhook
{
    /**
     * Ensure webhooks table exists (auto-migrate on first use).
     */
    public static function ensureTable(): void
    {
        $db = Database::getConnection();
        $db->exec("CREATE TABLE IF NOT EXISTS `webhooks` (
            `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name`       VARCHAR(120) NOT NULL,
            `url`        TEXT NOT NULL,
            `events`     TEXT COMMENT 'JSON array of event names',
            `secret`     VARCHAR(255) DEFAULT NULL,
            `status`     ENUM('healthy','failing','paused') DEFAULT 'healthy',
            `paused`     TINYINT(1) DEFAULT 0,
            `latency_ms` INT DEFAULT NULL,
            `last_ping`  DATETIME DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $db->exec("CREATE TABLE IF NOT EXISTS `webhook_errors` (
            `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `webhook_id` INT UNSIGNED NOT NULL,
            `http_code`  SMALLINT DEFAULT NULL,
            `message`    TEXT,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_webhook_id (`webhook_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public static function getAll(): array
    {
        self::ensureTable();
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM `webhooks` ORDER BY `id` DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $row['events']    = json_decode($row['events'] ?? '[]', true) ?: [];
            $row['paused']    = (bool)$row['paused'];
            $row['error_log'] = self::getErrors($row['id'], 5);
        }
        return $rows;
    }

    public static function create(string $name, string $url, array $events, string $secret = ''): int
    {
        self::ensureTable();
        $db   = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO `webhooks` (`name`,`url`,`events`,`secret`,`status`,`paused`) VALUES (?,?,?,?,'healthy',0)");
        $stmt->execute([$name, $url, json_encode($events), $secret ?: null]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        self::ensureTable();
        $db     = Database::getConnection();
        $fields = [];
        $values = [];
        $allowed = ['name','url','events','secret','status','paused','latency_ms','last_ping'];
        foreach ($data as $k => $v) {
            if (!in_array($k, $allowed)) continue;
            if ($k === 'events') $v = json_encode($v);
            $fields[] = "`$k` = ?";
            $values[] = $v;
        }
        if (empty($fields)) return false;
        $values[] = $id;
        $stmt = $db->prepare("UPDATE `webhooks` SET " . implode(',', $fields) . " WHERE `id` = ?");
        return $stmt->execute($values);
    }

    public static function delete(int $id): bool
    {
        self::ensureTable();
        $db = Database::getConnection();
        $db->prepare("DELETE FROM `webhook_errors` WHERE `webhook_id` = ?")->execute([$id]);
        $stmt = $db->prepare("DELETE FROM `webhooks` WHERE `id` = ?");
        return $stmt->execute([$id]);
    }

    public static function logError(int $id, int $code, string $message): void
    {
        self::ensureTable();
        $db = Database::getConnection();
        $db->prepare("INSERT INTO `webhook_errors` (`webhook_id`,`http_code`,`message`) VALUES (?,?,?)")
           ->execute([$id, $code, $message]);
        // Keep only last 20 errors per webhook
        $db->prepare("DELETE FROM `webhook_errors` WHERE `webhook_id` = ? AND `id` NOT IN (
            SELECT `id` FROM (SELECT `id` FROM `webhook_errors` WHERE `webhook_id` = ? ORDER BY `id` DESC LIMIT 20) t
        )")->execute([$id, $id]);
    }

    public static function getErrors(int $id, int $limit = 20): array
    {
        self::ensureTable();
        $db   = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM `webhook_errors` WHERE `webhook_id` = ? ORDER BY `id` DESC LIMIT ?");
        $stmt->execute([$id, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ping a webhook URL and record latency/status.
     */
    public static function ping(int $id): array
    {
        self::ensureTable();
        $db   = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM `webhooks` WHERE `id` = ?");
        $stmt->execute([$id]);
        $wh = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$wh) return ['ok' => false, 'error' => 'Not found'];

        $start = microtime(true);
        $ctx   = stream_context_create(['http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\nUser-Agent: HonlorAdmin-HealthCheck/1.0\r\n",
            'content' => json_encode(['event' => 'ping', 'timestamp' => time()]),
            'timeout' => 8,
            'ignore_errors' => true,
        ]]);

        $result = @file_get_contents($wh['url'], false, $ctx);
        $ms     = (int)round((microtime(true) - $start) * 1000);

        // Parse HTTP status from response headers
        $code = 0;
        if (!empty($http_response_header)) {
            preg_match('/HTTP\/\S+\s+(\d+)/', $http_response_header[0] ?? '', $m);
            $code = (int)($m[1] ?? 0);
        }

        $ok = ($result !== false && $code >= 200 && $code < 300);
        self::update($id, [
            'status'     => $ok ? 'healthy' : 'failing',
            'latency_ms' => $ok ? $ms : null,
            'last_ping'  => date('Y-m-d H:i:s'),
        ]);

        if (!$ok) {
            self::logError($id, $code ?: 0, $result !== false
                ? "HTTP $code — endpoint returned error"
                : "Connection refused or timeout after {$ms}ms");
        }

        return ['ok' => $ok, 'latency' => $ms, 'code' => $code];
    }
}
