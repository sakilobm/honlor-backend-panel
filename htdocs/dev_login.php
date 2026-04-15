<?php
require_once 'libs/load.php';
use Aether\Session;
use Aether\User;
use Aether\Database;

// This is a temporary script for verification.
$uid = 51; // sakil
$conn = Database::getConnection();
$ip = '127.0.0.1';
$agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36';
$token = md5(random_int(0, 9999999) . $ip . $agent . time());

$sql = "INSERT INTO `session` (`uid`, `token`, `login_time`, `ip`, `user_agent`, `active`)
        VALUES (?, ?, NOW(), ?, ?, 1)";
$stmt = $conn->prepare($sql);
if ($stmt->execute([$uid, $token, $ip, $agent])) {
    Session::start();
    Session::set('session_token', $token);
    echo "Authenticated as Sakil. Token: $token. <a href='/admin?page=roles'>Go to Role Studio</a>";
} else {
    echo "Failed to create session.";
}
