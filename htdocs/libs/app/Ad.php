<?php

namespace App;

use Aether\Database;
use Aether\Traits\SQLGetterSetter;
use PDO;
use Exception;

/**
 * Ad Model
 * ========
 * PSR-4 Namespace: App\Ad
 * Campaign management model.
 */
class Ad
{
    use SQLGetterSetter;

    public int $id;
    public string $table = 'ads';
    public $conn;

    /**
     * Create a new ad campaign.
     *
     * @param string $name
     * @param string $type
     * @param float  $budget
     * @param string $startDate
     * @param string $endDate
     * @return int|bool Last insert id or false
     */
    public static function createAd(string $name, string $type, float $budget, string $startDate, string $endDate)
    {
        $db = Database::getConnection();
        $sql = "INSERT INTO `ads` (`name`, `type`, `budget`, `status`, `start_date`, `end_date`) 
                VALUES (?, ?, ?, 'Active', ?, ?)";
        $stmt = $db->prepare($sql);
        
        try {
            if ($stmt->execute([$name, $type, $budget, $startDate, $endDate])) {
                return $db->lastInsertId();
            }
        } catch (Exception $e) {
            error_log("Ad::createAd() failed: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Get all campaigns.
     *
     * @return array
     */
    public static function getAllAds(): array
    {
        $db = Database::getConnection();
        $res = $db->query("SELECT * FROM `ads` ORDER BY `created_at` DESC");
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get active ads count.
     *
     * @return int
     */
    public static function getActiveCount(): int
    {
        $db = Database::getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM `ads` WHERE `status` = 'Active'")->fetchColumn();
    }

    /**
     * Ad Constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id    = $id;
        $this->table = 'ads';
        $this->conn  = Database::getConnection();
    }

    /**
     * Toggle campaign status.
     *
     * @return bool
     */
    public function toggleStatus(): bool
    {
        $current = $this->getStatus();
        $new = ($current === 'Active') ? 'Paused' : 'Active';
        return $this->setStatus($new);
    }

    /**
     * Create a new ad campaign.
     */
    public static function create(array $data): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO ads (name, type, budget, start_date, end_date, ad_code) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['type'],
            $data['budget'],
            $data['start_date'],
            $data['end_date'],
            $data['ad_code'] ?? ''
        ]);
    }

    /**
     * Update an existing ad campaign.
     */
    public static function update(int $id, array $data): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE ads SET name = ?, type = ?, budget = ?, start_date = ?, end_date = ?, ad_code = ? WHERE id = ?");
        return $stmt->execute([
            $data['name'],
            $data['type'],
            $data['budget'],
            $data['start_date'],
            $data['end_date'],
            $data['ad_code'] ?? '',
            $id
        ]);
    }

    /**
     * Delete an ad campaign.
     */
    public static function delete(int $id): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM ads WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
