<?php

require_once __DIR__ . '/libs/load.php';

use Aether\Database;

try {
    $db = Database::getConnection();
    $sql = file_get_contents(__DIR__ . '/db/schema.sql');
    
    // Split SQL into individual statements
    // This is a simple split, for complex SQL it might need a more robust parser
    // But schema.sql looks simple enough.
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $db->exec($statement);
        }
    }
    
    echo "Database schema updated successfully.\n";
} catch (Exception $e) {
    echo "Error updating database schema: " . $e->getMessage() . "\n";
    exit(1);
}
