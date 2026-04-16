<?php
require 'htdocs/libs/load.php';

try {
    $db = \Aether\Database::getConnection();
    $exists = $db->query("SELECT id FROM roles WHERE slug = 'observer' ")->fetch();
    if (!$exists) {
        \App\Role::create('Observer', [
            'users' => ['view'],
            'messages' => ['view'],
            'ads' => ['view'],
            'channels' => ['view'],
            'analytics' => ['view'],
            'reports' => ['view']
        ]);
        echo "Observer Protocol established.\n";
    } else {
        echo "Observer Protocol already active.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
