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
     * Get latest messages with user and channel info.
     */
    public static function getLatest(int $limit = 50): array
    {
        $db = Database::getConnection();
        $sql = "SELECT m.*, a.username, c.name as channel_name 
                FROM `messages` m 
                LEFT JOIN `auth` a ON m.user_id = a.id 
                LEFT JOIN `channels` c ON m.channel_id = c.id 
                ORDER BY m.created_at DESC LIMIT ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Flag a message.
     */
    public function flag(): bool
    {
        return $this->setStatus('flagged');
    }

    /**
     * Delete a message (soft-delete status change).
     */
    public function deleteRow(): bool
    {
        return $this->setStatus('deleted');
    }
}
