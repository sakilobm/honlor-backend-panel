<?php

namespace Aether;

use Aether\Traits\SQLGetterSetter;
use Exception;

/**
 * User Class
 * ==========
 * PSR-4 Namespace: Aether\User
 * Refactored to use PDO and Transactions.
 */
class User
{
    use SQLGetterSetter;

    public int $id;
    public string $username;
    public string $email;
    public string $table = 'auth';
    public $conn;
    private ?\App\Role $role = null;

    /**
     * Signup a new user.
     */
    public static function signup(string $username, string $password, string $email, string $phone): bool
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        $conn = Database::getConnection();

        try {
            $conn->beginTransaction();

            // 1. Insert into auth
            $stmt = $conn->prepare(
                "INSERT INTO `auth` (`username`, `password`, `email`, `phone`, `active`) VALUES (?, ?, ?, ?, 1)"
            );
            $stmt->execute([$username, $hash, $email, $phone]);

            $id = $conn->lastInsertId();

            // 2. Insert default profile
            $stmt = $conn->prepare(
                "INSERT INTO `profiles` (`id`, `firstname`, `lastname`, `created_at`) VALUES (?, ?, ?, NOW())"
            );
            $stmt->execute([$id, $username, '']); // Use username as default firstname

            $conn->commit();
            return true;
        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log('User::signup() error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Basic login check.
     *
     * @param string $user     Username or Email
     * @param string $password Password
     * @return string|false    Returns username if success, false otherwise
     */
    public static function login(string $user, string $password)
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT `username`, `password`, `active`, `blocked` FROM `auth`
             WHERE `username` = ? OR `email` = ? LIMIT 1"
        );
        $stmt->execute([$user, $user]);
        $row = $stmt->fetch();

        if ($row) {
            if ((int)$row['blocked'] === 1) {
                return false;
            }
            if ((int)$row['active'] === 0) {
                return false;
            }
            if (password_verify($password, $row['password'])) {
                return $row['username'];
            }
        }

        return false;
    }

    /**
     * User Constructor.
     *
     * @param mixed $identifier ID, Username or Email
     */
    public function __construct($identifier)
    {
        $this->table = 'auth';
        $this->conn  = Database::getConnection();

        $sql = "SELECT `id`, `username`, `email`, `role_id`, `is_master` FROM `auth`
                WHERE `username` = ? OR `email` = ? OR `id` = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $id = is_numeric($identifier) ? (int)$identifier : -1;
        $stmt->execute([$identifier, $identifier, $id]);
        $row = $stmt->fetch();

        if (!$row) {
            throw new Exception("User '{$identifier}' does not exist.");
        }

        $this->id       = (int)$row['id'];
        $this->username = $row['username'];
        $this->email    = $row['email'];
    }

    /**
     * Fetch all users with pagination and filtering.
     */
    public static function listAll(int $limit = 10, int $offset = 0, string $filter = ''): array
    {
        $db = Database::getConnection();
        $sql = "SELECT a.id, a.username, a.email, a.active, a.blocked, a.created_at, a.role_id, a.is_master,
                       p.firstname, p.lastname, p.bio, p.avatar, r.name as role_name
                FROM `auth` a
                LEFT JOIN `profiles` p ON a.id = p.id
                LEFT JOIN `roles` r ON a.role_id = r.id";
        
        if (!empty($filter)) {
            $sql .= " WHERE a.username LIKE ? OR a.email LIKE ? OR p.firstname LIKE ? OR p.lastname LIKE ?";
        }
        
        $sql .= " ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $db->prepare($sql);
        
        $params = [];
        if (!empty($filter)) {
            $f = "%$filter%";
            $params = [$f, $f, $f, $f];
        }
        $params[] = $limit;
        $params[] = $offset;

        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get total count of users for pagination.
     */
    public static function getTotalCount(string $filter = ''): int
    {
        $db = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM `auth` a LEFT JOIN `profiles` p ON a.id = p.id";
        
        $params = [];
        if (!empty($filter)) {
            $sql .= " WHERE a.username LIKE ? OR a.email LIKE ? OR p.firstname LIKE ? OR p.lastname LIKE ?";
            $f = "%$filter%";
            $params = [$f, $f, $f, $f];
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Toggle blocked status.
     */
    public function toggleBlock(): bool
    {
        $current = (int)$this->getBlocked();
        return $this->setBlocked($current === 1 ? 0 : 1);
    }

    public function toggleActive(): bool
    {
        $current = (int)$this->getActive();
        return $this->setActive($current === 1 ? 0 : 1);
    }

    /**
     * Update user profile and email.
     */
    public function updateProfile(array $data): bool
    {
        $conn = Database::getConnection();
        try {
            if (!$conn->inTransaction()) {
                $conn->beginTransaction();
            }

            // 1. Update auth table (email)
            if (isset($data['email'])) {
                $stmt = $conn->prepare("UPDATE `auth` SET `email` = ? WHERE `id` = ?");
                $stmt->execute([$data['email'], $this->id]);
            }

            // 2. Update profiles table
            $fields = ['firstname', 'lastname', 'bio', 'avatar'];
            $updates = [];
            $params = [];
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "`$field` = ?";
                    $params[] = $data[$field];
                }
            }

            if (!empty($updates)) {
                $params[] = $this->id;
                $sql = "UPDATE `profiles` SET " . implode(', ', $updates) . " WHERE `id` = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute($params);
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            if ($conn->inTransaction()) $conn->rollBack();
            error_log("User::updateProfile() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user is the Master Admin with absolute power.
     */
    public function isMaster(): bool
    {
        return (int)$this->getIsMaster() === 1;
    }

    /**
     * Get the role associated with this user.
     */
    public function getRole(): ?\App\Role
    {
        if ($this->role) {
            return $this->role;
        }

        $roleId = (int)$this->getRoleId();
        if ($roleId > 0) {
            try {
                $this->role = new \App\Role($roleId);
                return $this->role;
            } catch (Exception $e) {
                error_log("User::getRole() error: " . $e->getMessage());
            }
        }
        return null;
    }

    /* ─── Profile Getters (Mapping to 'profiles' table) ─── */

    public function getFirstname(): string|false
    {
        return $this->_get_profile_data('firstname');
    }

    public function getLastname(): string|false
    {
        return $this->_get_profile_data('lastname');
    }

    public function getBio(): string|false
    {
        return $this->_get_profile_data('bio');
    }

    public function getAvatar(): string|false
    {
        return $this->_get_profile_data('avatar');
    }

    private function _get_profile_data(string $var)
    {
        $stmt = $this->conn->prepare("SELECT `{$var}` FROM `profiles` WHERE `id` = ? LIMIT 1");
        $stmt->execute([$this->id]);
        $row = $stmt->fetch();
        return $row ? $row[$var] : false;
    }
}

