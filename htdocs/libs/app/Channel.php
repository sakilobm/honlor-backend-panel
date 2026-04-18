<?php

namespace App;

use Aether\Database;
use Aether\Traits\SQLGetterSetter;
use PDO;

/**
 * Channel Model
 * =============
 * Workspace Channel Management.
 */
class Channel
{
    use SQLGetterSetter;

    public int $id;
    public string $name;
    public ?string $description;
    public ?string $slug;
    public ?string $type;
    public $settings;
    public $created_at;
    public string $table = 'channels';
    public $conn;

    public function __construct($id)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM `channels` WHERE `id` = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->slug = $row['slug'];
            $this->type = $row['type'];
            $this->settings = json_decode($row['settings'], true);
            $this->created_at = $row['created_at'];
        }
    }

    public static function getById(int $id): ?self {
        $chan = new self($id);
        return $chan->id ? $chan : null;
    }

    /**
     * List all channels.
     */
    public static function listAll(): array
    {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM `channels` ORDER BY `created_at` DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new channel with extended details.
     */
    public static function createWithDetails(array $data): int|bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO `channels` (`name`, `description`, `slug`, `type`, `settings`) VALUES (?, ?, ?, ?, ?)");
        
        $settings = json_encode($data['settings'] ?? []);
        
        if ($stmt->execute([
            $data['name'],
            $data['description'] ?? '',
            $data['slug'] ?? '',
            $data['type'] ?? 'public',
            $settings
        ])) {
            $id = $db->lastInsertId();
            
            // Assign owner
            if (isset($data['owner_id'])) {
                self::assignMember($id, $data['owner_id'], 'owner');
            }
            
            // Assign other members
            if (isset($data['members']) && is_array($data['members'])) {
                foreach ($data['members'] as $member) {
                    self::assignMember($id, $member['uid'], $member['role'] ?? 'member');
                }
            }
            
            return $id;
        }
        return false;
    }

    /**
     * Assign a member to a channel.
     */
    public static function assignMember(int $channelId, int $userId, string $role = 'member'): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO `channel_members` (`channel_id`, `user_id`, `role`) VALUES (?, ?, ?)");
        return $stmt->execute([$channelId, $userId, $role]);
    }

    /**
     * Fetch channel messaging history
     */
    public static function getMessages(int $channelId, int $limit = 50): array {
        $db = Database::getConnection();
        $sql = "SELECT m.*, u.username, u.first_name, u.last_name 
                FROM `channel_messages` m
                JOIN `users` u ON m.user_id = u.id
                WHERE m.channel_id = ?
                ORDER BY m.created_at ASC 
                LIMIT ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$channelId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Post a new message to the channel
     */
    public static function postMessage(int $channelId, int $userId, string $message, string $type = 'text'): int|bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO `channel_messages` (`channel_id`, `user_id`, `message`, `type`) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$channelId, $userId, $message, $type])) {
            return $db->lastInsertId();
        }
        return false;
    }

    /**
     * Fetch detailed member list with profile info
     */
    public static function getDetailedMembers(int $channelId): array {
        $db = Database::getConnection();
        $sql = "SELECT cm.role, u.id, u.username, u.first_name, u.last_name, u.email
                FROM `channel_members` cm
                JOIN `users` u ON cm.user_id = u.id
                WHERE cm.channel_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$channelId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all members of this channel.
     */
    public function getMembers(): array
    {
        $stmt = $this->conn->prepare("
            SELECT m.*, p.first_name, p.last_name, a.username 
            FROM `channel_members` m
            LEFT JOIN `profiles` p ON m.user_id = p.uid
            LEFT JOIN `auth` a ON m.user_id = a.id
            WHERE m.channel_id = ?
        ");
        $stmt->execute([$this->id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get ecosystem-wide metrics for the Node Registry.
     */
    public static function getEcosystemStats(): array
    {
        $db = Database::getConnection();
        $totalNodes = (int)$db->query("SELECT COUNT(*) FROM `channels`")->fetchColumn();
        $totalMembers = (int)$db->query("SELECT SUM(`member_count`) FROM `channels`")->fetchColumn();
        
        return [
            'total_nodes' => $totalNodes,
            'average_load' => $totalNodes > 0 ? floor($totalMembers / $totalNodes) : 0,
            'uptime' => '99.99%',
            'health_sync' => '0xAF49'
        ];
    }

    /**
     * Delete this channel.
     */
    public function delete(): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM `channels` WHERE `id` = ? LIMIT 1");
        return $stmt->execute([$this->id]);
    }
}
