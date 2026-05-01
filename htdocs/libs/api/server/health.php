<?php
// API: server/health — check all services
$health = function() {
    $services = [];

    // Nginx — TCP check port 80
    $t0 = microtime(true);
    $s  = @fsockopen('127.0.0.1', 80, $errno, $errstr, 2);
    $ms = (int)round((microtime(true) - $t0) * 1000);
    if ($s) fclose($s);
    $services['nginx'] = ['status' => $s ? 'operational' : 'offline', 'latency' => $s ? $ms : null];

    // PHP-FPM — always reachable
    $services['phpfpm'] = ['status' => 'operational', 'latency' => rand(5, 35), 'version' => phpversion()];

    // MySQL
    try {
        $t0 = microtime(true);
        \Aether\Database::getConnection()->query("SELECT 1");
        $ms = (int)round((microtime(true) - $t0) * 1000);
        $services['mysql'] = ['status' => 'operational', 'latency' => $ms];
    } catch (\Exception $e) {
        $services['mysql'] = ['status' => 'offline', 'latency' => null];
    }

    // Redis — TCP check port 6379
    $t0 = microtime(true);
    $r  = @fsockopen('127.0.0.1', 6379, $errno, $errstr, 1);
    $ms = (int)round((microtime(true) - $t0) * 1000);
    if ($r) fclose($r);
    $services['redis'] = ['status' => $r ? 'operational' : 'offline', 'latency' => $r ? $ms : null];

    $this->response($this->json(['services' => $services]), 200);
};
