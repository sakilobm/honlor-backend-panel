<?php
require 'htdocs/libs/load.php';

try {
    $db = \Aether\Database::getConnection();
    echo "Adding request_pending column to auth table...\n";
    $db->query("ALTER TABLE `auth` ADD `request_pending` TINYINT(1) DEFAULT 0 AFTER `role_id` ");
    echo "Migration Complete.\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists. Skipping.\n";
    } else {
        echo "Migration Failed: " . $e->getMessage() . "\n";
    }
}
