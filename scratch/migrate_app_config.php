<?php

require_once __DIR__ . '/../htdocs/libs/load.php';

use Aether\Database;

try {
    $db = Database::getConnection();
    
    // Create the table
    $createTableSql = "
    CREATE TABLE IF NOT EXISTS `app_config` (
      `id` bigint NOT NULL AUTO_INCREMENT,
      `latest_version` varchar(255) NOT NULL,
      `is_mandatory` tinyint(1) NULL DEFAULT 0,
      `update_url` text NOT NULL,
      `description` text NULL,
      `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
    ";
    
    $db->exec($createTableSql);
    echo "Table 'app_config' created successfully or already exists.\n";
    
    // Check if table is empty, if so, seed it
    $count = $db->query("SELECT COUNT(*) as count FROM `app_config`")->fetch()['count'];
    if ($count == 0) {
        $seedSql = "
        INSERT INTO `app_config` (`latest_version`, `is_mandatory`, `update_url`, `description`) VALUES
        ('v1.2.0', 1, 'https://apps.apple.com/app/honlor', 'Critical security patch resolving core credential encryption flaws.'),
        ('v1.1.5', 0, 'https://play.google.com/store/apps/honlor', 'Performance optimization for real-time telemetry streams.');
        ";
        $db->exec($seedSql);
        echo "Table 'app_config' seeded with initial records.\n";
    } else {
        echo "Table 'app_config' already has data ($count rows). Skipping seed.\n";
    }
} catch (Exception $e) {
    echo "Error running app_config migration: " . $e->getMessage() . "\n";
    exit(1);
}
