<?php

namespace App;

use Aether\Database;
use PDO;
use Exception;

/**
 * Compliance Model
 * ================
 * PSR-4 Namespace: App\Compliance
 * Incident and Safety management model.
 */
class Compliance
{
    /**
     * Get active incident queue.
     */
    public static function getIncidentQueue(): array
    {
        $db = Database::getConnection();
        $sql = "SELECT r.*, 
                       a1.username as reporter_name, 
                       a2.username as target_name 
                FROM `reports` r
                LEFT JOIN `auth` a1 ON r.reporter_id = a1.id
                LEFT JOIN `auth` a2 ON r.target_id = a2.id
                WHERE r.status = 'Active'
                ORDER BY r.created_at DESC";
                
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent resolution history.
     */
    public static function getResolutionHistory(): array
    {
        $db = Database::getConnection();
        $sql = "SELECT r.*, 
                       a1.username as reporter_name, 
                       a2.username as target_name 
                FROM `reports` r
                LEFT JOIN `auth` a1 ON r.reporter_id = a1.id
                LEFT JOIN `auth` a2 ON r.target_id = a2.id
                WHERE r.status != 'Active'
                ORDER BY r.created_at DESC
                LIMIT 10";
                
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get safety telemetry metrics.
     */
    public static function getSafetyTelemetry(): array
    {
        $db = Database::getConnection();
        
        $total = (int)$db->query("SELECT COUNT(*) FROM `reports`")->fetchColumn();
        $resolved = (int)$db->query("SELECT COUNT(*) FROM `reports` WHERE `status` = 'Resolved'")->fetchColumn();
        $active = (int)$db->query("SELECT COUNT(*) FROM `reports` WHERE `status` = 'Active'")->fetchColumn();
        
        $velocity = $total > 0 ? ($resolved / $total) * 100 : 0;
        
        return [
            'total_incidents' => $total,
            'active_queue'    => $active,
            'resolved_ledger' => $resolved,
            'velocity'        => round($velocity, 1)
        ];
    }

    /**
     * Execute status action on an incident.
     */
    public static function updateIncidentStatus(int $id, string $status): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE `reports` SET `status` = ? WHERE `id` = ?");
        return $stmt->execute([$status, $id]);
    }
}
