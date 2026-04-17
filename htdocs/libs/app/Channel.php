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
    public string $table = 'channels';
    public $conn;

    public function __construct($id)
    {
        $this->id = (int)$id;
        $this->table = 'channels';
        $this->conn = Database::getConnection();

        $stmt = $this->conn->prepare("SELECT `name` FROM `channels` WHERE `id` = ? LIMIT 1");
        $stmt->execute([$this->id]);
        $row = $stmt->fetch();

        if ($row) {
            $this->name = $row['name'];
        } else {
            throw new \Exception("Channel ID {$id} not found.");
        }
    }

    /**
     * List all channels.
     */
    public static function listAll(): array
    {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM `channels` ORDER BY `name` ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new channel.
     */
    public static function create(string $name, string $type = 'public'): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO `channels` (`name`, `type`) VALUES (?, ?)");
        return $stmt->execute([$name, $type]);
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
