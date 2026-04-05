<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../libs/includes/Session.class.php';

use Aether\Database;

// Initialize Database connection directly
$db = Database::getConnection();

// --- Truncate Existing Data (Optional/Safe depending on environment) ---
// $db->exec("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE auth; TRUNCATE profiles; TRUNCATE channels; TRUNCATE messages; TRUNCATE logs; TRUNCATE ads; SET FOREIGN_KEY_CHECKS = 1;");

echo "Seeding Users & Profiles...\n";
$users = [
    ['marcus', 'password123', 'marcus@honlor.io', 'Marcus', 'Aurelius'],
    ['elena', 'password123', 'elena.s@web3.com', 'Elena', 'Sorrows'],
    ['alex', 'password123', 'alex.dev@google.com', 'Alex', 'Rivera'],
    ['sarah', 'password123', 'sarah.j@outlook.com', 'Sarah', 'Jenks'],
    ['viktor', 'password123', 'viktor.volk@proton.me', 'Viktor', 'Volk'],
    ['lisa', 'password123', 'lisa.m@icloud.com', 'Lisa', 'Monaco']
];

foreach ($users as $u) {
    try {
        $hash = password_hash($u[1], PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO `auth` (`username`, `password`, `email`, `active`, `blocked`) VALUES (?, ?, ?, 1, 0) ON DUPLICATE KEY UPDATE id=id");
        $stmt->execute([$u[0], $hash, $u[2]]);
        $uid = $db->lastInsertId();
        
        if ($uid) {
            $pst = $db->prepare("INSERT INTO `profiles` (`id`, `firstname`, `lastname`, `avatar`) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE id=id");
            $avatar = "https://api.dicebear.com/7.x/avataaars/svg?seed=" . $u[0];
            $pst->execute([$uid, $u[3], $u[4], $avatar]);
        }
    } catch (Exception $e) {
        echo "Error seeding user {$u[0]}: " . $e->getMessage() . "\n";
    }
}

echo "Seeding Channels...\n";
$channels = [
    ['General Activity', 'public', 1240],
    ['Development Node', 'private', 12],
    ['Community Alpha', 'public', 450],
    ['Admin Protocol', 'private', 4]
];

foreach ($channels as $c) {
    $stmt = $db->prepare("INSERT INTO `channels` (`name`, `type`, `member_count`) VALUES (?, ?, ?)");
    $stmt->execute($c);
}

echo "Seeding Messages...\n";
$msg_stmt = $db->prepare("INSERT INTO `messages` (`channel_id`, `user_id`, `content`, `status`) VALUES (?, ?, ?, ?)");
$user_ids = $db->query("SELECT id FROM auth")->fetchAll(PDO::FETCH_COLUMN);
$chan_ids = $db->query("SELECT id FROM channels")->fetchAll(PDO::FETCH_COLUMN);

for ($i = 0; $i < 20; $i++) {
    $msg_stmt->execute([
        $chan_ids[array_rand($chan_ids)],
        $user_ids[array_rand($user_ids)],
        "This is a test broadcast packet at index " . ($i + 1),
        rand(0, 10) > 8 ? 'flagged' : 'normal'
    ]);
}

echo "Seeding Logs...\n";
$log_stmt = $db->prepare("INSERT INTO `logs` (`user_id`, `action`, `details`, `ip`, `level`) VALUES (?, ?, ?, ?, ?)");
foreach ($user_ids as $uid) {
    $log_stmt->execute([
        $uid,
        "User Logged In",
        "Successful authentication via WebUI",
        "192.168.1." . rand(1, 254),
        "info"
    ]);
}

echo "Seeding Ads...\n";
$ads = [
    ['Winter Global Sale', 'Social', 4500.00, 'Active', 124000, 4500, '2026-01-01', '2026-03-31'],
    ['Search Optimization V4', 'Search', 1200.00, 'Active', 15000, 1200, '2026-02-15', '2026-05-15'],
    ['Brand Awareness 2026', 'Display', 8500.00, 'Paused', 500000, 2400, '2026-01-10', '2026-12-31']
];

foreach ($ads as $ad) {
    $stmt = $db->prepare("INSERT INTO `ads` (`name`, `type`, `budget`, `status`, `impressions`, `clicks`, `start_date`, `end_date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute($ad);
}

echo "Database Seeded Successfully.\n";
