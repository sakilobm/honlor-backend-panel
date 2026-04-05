<?php

namespace App;

use Aether\Database;
use PDO;

/**
 * DashboardStats Model
 * ====================
 * Metrics for the Admin Overview.
 */
class DashboardStats
{
    /** @return int */
    public static function getTotalUsers(): int
    {
        $db = Database::getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM `auth`")->fetchColumn();
    }

    /** @return int */
    public static function getMessagesToday(): int
    {
        $db = Database::getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM `messages` WHERE DATE(`created_at`) = CURDATE()")->fetchColumn();
    }

    /** @return int */
    public static function getActiveChannels(): int
    {
        $db = Database::getConnection();
        return (int)$db->query("SELECT COUNT(*) FROM `channels` WHERE `type` = 'public'")->fetchColumn();
    }

    /**
     * Get user growth data for Chart.js.
     *
     * @param int $days Range (7 or 30 days)
     * @return array {labels: [], data: []}
     */
    public static function getUserGrowth(int $days = 7): array
    {
        $db = Database::getConnection();
        $sql = "SELECT DATE(`created_at`) as date, COUNT(*) as count 
                FROM `auth` 
                WHERE `created_at` >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(`created_at`) 
                ORDER BY DATE(`created_at`) ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$days]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $data   = [];

        // Fill gaps in dates
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('M d', strtotime($date));
            
            $found = false;
            foreach ($rows as $row) {
                if ($row['date'] === $date) {
                    $data[] = (int)$row['count'];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $data[] = 0;
            }
        }

        return [
            'labels' => $labels,
            'data'   => $data
        ];
    }

    /**
     * Get total revenue (mocked for now).
     * @return float
     */
    public static function getTotalRevenue(): float
    {
        // In a real app, this would query a 'payments' or 'orders' table.
        return 125430.50;
    }

    /**
     * Get recent system/admin activity.
     * @param int $limit
     * @return array
     */
    public static function getRecentActivity(int $limit = 5): array
    {
        $db = Database::getConnection();
        $sql = "SELECT l.*, a.username 
                FROM `logs` l 
                LEFT JOIN `auth` a ON l.user_id = a.id 
                ORDER BY l.created_at DESC LIMIT ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get current system infrastructure health.
     * @return array
     */
    public static function getSystemHealth(): array
    {
        // Mocking system metrics for the dashboard.
        return [
            'cpu_usage'    => rand(15, 45) . '%',
            'memory_usage' => rand(30, 60) . '%',
            'disk_space'   => '78%',
            'uptime'       => '14d 6h 22m',
            'status'       => 'Operational'
        ];
    }
}
