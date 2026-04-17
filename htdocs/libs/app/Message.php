<?php

namespace App;

use Aether\Database;
use Aether\Traits\SQLGetterSetter;
use PDO;

/**
 * Message Model
 * =============
 * Chat Moderation.
 */
class Message
{
    use SQLGetterSetter;

    public int $id;
    public string $table = 'messages';
    public $conn;

    public function __construct($id)
    {
        $this->id = (int)$id;
        $this->table = 'messages';
        $this->conn = Database::getConnection();

        $stmt = $this->conn->prepare("SELECT `id` FROM `messages` WHERE `id` = ? LIMIT 1");
        $stmt->execute([$this->id]);
        if (!$stmt->fetch()) {
            throw new \Exception("Message ID {$id} not found.");
        }
    }

    /**
     * Get filtered messages with user and channel info.
     * @param string $status Filter by status (active, flagged, deleted)
     * @param int $limit
     */
    public static function getFiltered(string $status = 'all', int $limit = 50, string $filter = ''): array
    {
        $db = Database::getConnection();
        $where = [];
        $params = [];

        if ($status !== 'all') {
            $where[] = "m.status = ?";
            $params[] = $status;
        } else {
            // By default, don't show deleted in 'all' view
            $where[] = "m.status != 'deleted'";
        }

        if (!empty($filter)) {
            $where[] = "(m.content LIKE ? OR a.username LIKE ?)";
            $params[] = "%$filter%";
            $params[] = "%$filter%";
        }

        $whereSql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";
        $params[] = $limit;

        $sql = "SELECT m.*, a.username, c.name as channel_name 
                FROM `messages` m 
                LEFT JOIN `auth` a ON m.user_id = a.id 
                LEFT JOIN `channels` c ON m.channel_id = c.id 
                $whereSql
                ORDER BY m.created_at DESC LIMIT ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get real-time ingress velocity (messages in last 60s / 60).
     */
    public static function getVelocity(): float
    {
        $db = Database::getConnection();
        $count = (int)$db->query("SELECT COUNT(*) FROM `messages` WHERE `created_at` >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)")->fetchColumn();
        return round($count / 60, 1);
    }

    /**
     * Get count of flagged messages.
     */
    public static function getFlaggedCount(): int
    {
        $db = Database::getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM `messages` WHERE `status` = 'flagged'")->fetchColumn();
    }

    /**
     * Flag a message.
     */
    public function flag(): bool
    {
        return $this->setStatus('flagged');
    }

    /**
     * Unflag a message (restore to active).
     */
    public function unflag(): bool
    {
        return $this->setStatus('active');
    }

    /**
     * Delete a message (soft-delete status change).
     */
    public function deleteRow(): bool
    {
        return $this->setStatus('deleted');
    }
}
