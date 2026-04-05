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

        $sql = "SELECT `id`, `username`, `email` FROM `auth`
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
        $sql = "SELECT a.id, a.username, a.email, a.active, a.blocked, a.created_at, 
                       p.firstname, p.lastname, p.bio, p.avatar
                FROM `auth` a
                LEFT JOIN `profiles` p ON a.id = p.id";
        
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

    /**
     * Toggle active status.
     */
    public function toggleActive(): bool
    {
        $current = (int)$this->getActive();
        return $this->setActive($current === 1 ? 0 : 1);
    }
}
