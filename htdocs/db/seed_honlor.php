<?php

/**
 * Honlor Dashboard Seeder
 * =======================
 * Fills the database with realistic dummy data for testing.
 * Refactored to use PDO.
 */

require_once __DIR__ . '/../libs/load.php';

use Aether\Database;

$db = Database::getConnection();

echo "Starting seeding process...\n";

// 1. Clear existing data
$db->query("SET FOREIGN_KEY_CHECKS = 0");
$db->query("TRUNCATE TABLE auth");
$db->query("TRUNCATE TABLE profiles");
$db->query("TRUNCATE TABLE channels");
$db->query("TRUNCATE TABLE messages");
$db->query("TRUNCATE TABLE ads");
$db->query("TRUNCATE TABLE session");
$db->query("SET FOREIGN_KEY_CHECKS = 1");

echo "Tables truncated.\n";

// 2. Seed Users (Auth + Profiles)
$first_names = ['Alex', 'Sarah', 'Mike', 'Lena', 'David', 'Emma', 'John', 'Mia', 'Erik', 'Sofia', 'Marcus', 'Olivia', 'James', 'Isabella', 'Robert', 'Chris', 'Anna', 'Lucas', 'Zoe', 'Tom'];
$last_names  = ['Rivera', 'Jenks', 'Ross', 'Smith', 'Lee', 'Anderson', 'Brown', 'Thorne', 'Johansson', 'Vance', 'Chen', 'Taylor', 'White', 'Harris', 'Martin', 'Garcia', 'Miller', 'Davis', 'Wilson', 'Moore'];
$usernames   = [];

echo "Seeding 50 users...\n";
for ($i = 0; $i < 50; $i++) {
    $fname = $first_names[array_rand($first_names)];
    $lname = $last_names[array_rand($last_names)];
    $username = strtolower($fname . substr($lname, 0, 1) . rand(10, 99));
    
    // Ensure unique username
    while (in_array($username, $usernames)) {
        $username = strtolower($fname . substr($lname, 0, 1) . rand(100, 999));
    }
    $usernames[] = $username;
    
    $email = "$username@honlor.cloud";
    $pass  = password_hash('password123', PASSWORD_BCRYPT);
    $phone = '+1' . rand(1000000000, 9999999999);
    
    $days_ago = rand(0, 60);
    $created_at = date('Y-m-d H:i:s', strtotime("-$days_ago days"));

    // Insert Auth
    $stmt = $db->prepare("INSERT INTO auth (username, password, email, phone, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$username, $pass, $email, $phone, $created_at]);
    $uid = $db->lastInsertId();
    
    // Insert Profile
    $stmt = $db->prepare("INSERT INTO profiles (id, firstname, lastname, avatar, bio, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    $avatar = "https://api.dicebear.com/7.x/avataaars/svg?seed=" . $username;
    $bio = "Passionate community member exploring the Honlor ecosystem.";
    $stmt->execute([$uid, $fname, $lname, $avatar, $bio, $created_at]);
}
echo "Users seeded.\n";

// 3. Seed Channels
$channel_names = ['Global Strategy', 'Executive Board', 'Tech Summit', 'Announcements', 'General', 'Feedback', 'Security', 'Dev Ops', 'Marketing', 'Support'];
$channel_ids = [];

echo "Seeding 10 channels...\n";
foreach ($channel_names as $name) {
    $type = ($name === 'Executive Board' || $name === 'Security') ? 'private' : 'public';
    $mcount = rand(10, 1500);
    $stmt = $db->prepare("INSERT INTO channels (name, type, member_count) VALUES (?, ?, ?)");
    $stmt->execute([$name, $type, $mcount]);
    $channel_ids[] = $db->lastInsertId();
}
echo "Channels seeded.\n";

// 4. Seed Messages (200 messages across last 30 days)
echo "Seeding 200 messages...\n";
$user_ids = [];
$res = $db->query("SELECT id FROM auth");
while ($row = $res->fetch(PDO::FETCH_ASSOC)) { $user_ids[] = $row['id']; }

for ($i = 0; $i < 200; $i++) {
    $cid = $channel_ids[array_rand($channel_ids)];
    $uid = $user_ids[array_rand($user_ids)];
    $contents = [
        "Has anyone checked the Q4 slides?",
        "Server node #4 is reporting high latency.",
        "Welcome to the new members!",
        "Don't forget the meeting at 4 PM today.",
        "The new API documentation is live.",
        "Check out the latest ad campaign results.",
        "System backup completed successfully.",
        "Great work on the last sprint, team!",
        "Who is handling the security audit?",
        "I'll be OOO tomorrow morning."
    ];
    $content = $contents[array_rand($contents)];
    $status = (rand(1, 20) === 1) ? 'flagged' : 'normal';
    $interval = rand(0, 30);
    $created_at = date('Y-m-d H:i:s', strtotime("-$interval days"));
    
    $stmt = $db->prepare("INSERT INTO messages (channel_id, user_id, content, status, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$cid, $uid, $content, $status, $created_at]);
}
echo "Messages seeded.\n";

// 5. Seed Ads (5 campaigns)
echo "Seeding 5 ad campaigns...\n";
$ad_campaigns = [
    ['Summer Solstice 24', 'Social', 500.00, 'Active'],
    ['Winter Tech Summit', 'Search', 1200.00, 'Paused'],
    ['Product Launch Alpha', 'Display', 2500.00, 'Active'],
    ['Community Newsletter', 'Email', 150.00, 'Archived'],
    ['Global Brand Awareness', 'Social', 5000.00, 'Active']
];

foreach ($ad_campaigns as $camp) {
    $impressions = rand(5000, 1000000);
    $clicks = (int)($impressions * (rand(1, 5) / 100.0));
    $start = date('Y-m-d', strtotime('-' . rand(30, 60) . ' days'));
    $end = date('Y-m-d', strtotime('+' . rand(30, 60) . ' days'));
    
    $stmt = $db->prepare("INSERT INTO ads (name, type, budget, status, impressions, clicks, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$camp[0], $camp[1], $camp[2], $camp[3], $impressions, $clicks, $start, $end]);
}
echo "Ads seeded.\n";

echo "Seeding complete! Database is ready.\n";
