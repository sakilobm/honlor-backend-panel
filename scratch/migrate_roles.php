<?php
require 'htdocs/libs/load.php';

try {
    $db = \Aether\Database::getConnection();
    
    // Add slug column
    echo "Adding slug column...\n";
    $db->query("ALTER TABLE `roles` ADD `slug` VARCHAR(64) NULL AFTER `name` ");
    
    // Backfill slugs
    echo "Backfilling slugs...\n";
    $roles = $db->query("SELECT id, name FROM roles")->fetchAll();
    foreach($roles as $r) {
        $slug = strtolower(str_replace(' ', '-', trim($r['name'])));
        $db->prepare("UPDATE roles SET slug = ? WHERE id = ?")->execute([$slug, $r['id']]);
        echo " - Assigned $slug to ID {$r['id']}\n";
    }
    
    // Enforce constraints
    echo "Enforcing constraints...\n";
    $db->query("ALTER TABLE `roles` MODIFY `slug` VARCHAR(64) NOT NULL");
    $db->query("ALTER TABLE `roles` ADD UNIQUE (`slug`)");
    
    echo "Migration Complete.\n";
} catch (Exception $e) {
    echo "Migration Failed: " . $e->getMessage() . "\n";
}
