<?php

namespace App;

use Aether\Database;
use PDO;

class Settings {
    /**
     * Get a setting value by key.
     */
    public static function get($key, $default = null) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT s_value FROM settings WHERE s_key = :key LIMIT 1");
        $stmt->execute(['key' => $key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['s_value'] : $default;
    }

    /**
     * Update or insert a setting.
     */
    public static function set($key, $value, $description = null) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO settings (s_key, s_value, s_description) 
                            VALUES (:key, :value, :desc) 
                            ON DUPLICATE KEY UPDATE s_value = :value, s_description = COALESCE(:desc, s_description)");
        return $stmt->execute([
            'key' => $key,
            'value' => $value,
            'desc' => $description
        ]);
    }

    /**
     * Get all settings as an associative array.
     */
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT s_key, s_value, s_description FROM settings");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['s_key']] = [
                'value' => $row['s_value'],
                'description' => $row['s_description']
            ];
        }
        return $settings;
    }
}
