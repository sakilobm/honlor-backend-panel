<?php

namespace App;

use Aether\Database;
use Aether\Traits\SQLGetterSetter;
use Exception;

/**
 * Role Class
 * ==========
 * Manages administrative roles and granular permissions.
 */
class Role
{
    use SQLGetterSetter;

    public string $id;
    public string $name;
    public string $slug;
    public string $table = 'roles';
    public $conn;
    protected array $permissions = [];

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->conn = Database::getConnection();

        $stmt = $this->conn->prepare("SELECT * FROM `roles` WHERE `id` = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            throw new Exception("Role ID {$id} not found.");
        }

        $this->name = $row['name'];
        $this->slug = $row['slug'];
        $this->permissions = json_decode($row['permissions'], true) ?? [];
    }

    /**
     * Check if this role has a specific permission.
     * 
     * @param string $resource The resource (e.g., 'users', 'ads')
     * @param string $action The action (e.g., 'view', 'manage', 'delete')
     * @return bool
     */
    public function can(string $resource, string $action = 'view'): bool
    {
        // Absolute override for Master Super Admins
        if (isset($this->permissions['all']) && $this->permissions['all'] === true) {
            return true;
        }

        if (!isset($this->permissions[$resource])) {
            return false;
        }

        $allowedActions = $this->permissions[$resource];

        // If 'manage' is allowed, all sub-actions are typically allowed
        if (is_array($allowedActions)) {
            return in_array($action, $allowedActions) || in_array('manage', $allowedActions);
        }

        // Handle boolean access
        return (bool)$allowedActions;
    }

    /**
     * Fetch all roles.
     */
    public static function getAll(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT r.*, COUNT(a.id) as member_count 
            FROM `roles` r
            LEFT JOIN `auth` a ON a.role_id = r.id
            GROUP BY r.id
            ORDER BY r.name ASC
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Create a new role.
     */
    public static function create(string $name, array $permissions): int
    {
        $db = Database::getConnection();
        $slug = strtolower(str_replace(' ', '-', trim($name)));
        $permsJson = json_encode($permissions);
        $stmt = $db->prepare("INSERT INTO `roles` (`name`, `slug`, `permissions`) VALUES (?, ?, ?)");
        $stmt->execute([$name, $slug, $permsJson]);
        return (int)$db->lastInsertId();
    }
    
    /**
     * Update role name and regenerate slug.
     */
    public function setName(string $name): bool
    {
        $slug = strtolower(str_replace(' ', '-', trim($name)));
        $stmt = $this->conn->prepare("UPDATE `roles` SET `name` = ?, `slug` = ? WHERE `id` = ?");
        $success = $stmt->execute([$name, $slug, $this->id]);
        if ($success) {
            $this->name = $name;
            $this->slug = $slug;
        }
        return $success;
    }

    /**
     * Update permissions for this role.
     */
    public function updatePermissions(array $permissions): bool
    {
        $permsJson = json_encode($permissions);
        $stmt = $this->conn->prepare("UPDATE `roles` SET `permissions` = ? WHERE `id` = ?");
        $success = $stmt->execute([$permsJson, $this->id]);
        if ($success) {
            $this->permissions = $permissions;
        }
        return $success;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }
}
