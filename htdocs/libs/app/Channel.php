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
     * Delete this channel.
     */
    public function delete(): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM `channels` WHERE `id` = ? LIMIT 1");
        return $stmt->execute([$this->id]);
    }
}
