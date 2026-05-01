<?php
// API: server/metrics — real system metrics
$metrics = function() {
    $load = sys_getloadavg();
    $cpus = (int)(shell_exec('nproc') ?: 1);
    $cpu  = round($load[0] * 100 / $cpus, 1);

    $mem = @file_get_contents('/proc/meminfo');
    $mt = $ma = 0;
    if ($mem) {
        preg_match('/MemTotal:\s+(\d+)/', $mem, $m1); $mt = (int)($m1[1] ?? 0);
        preg_match('/MemAvailable:\s+(\d+)/', $mem, $m2); $ma = (int)($m2[1] ?? 0);
    }
    $ramPct = $mt ? round((($mt - $ma) / $mt) * 100, 1) : 0;

    $diskTotal = disk_total_space('/');
    $diskFree  = disk_free_space('/');
    $diskPct   = $diskTotal ? round((($diskTotal - $diskFree) / $diskTotal) * 100, 1) : 0;

    $this->response($this->json([
        'cpu'  => ['percent' => min($cpu, 100), 'load_1' => $load[0], 'load_5' => $load[1]],
        'ram'  => ['used_pct' => $ramPct, 'total_gb' => round($mt/1024/1024, 1), 'used_gb' => round(($mt-$ma)/1024/1024, 1)],
        'disk' => ['used_pct' => $diskPct, 'total_gb' => round($diskTotal/1024/1024/1024, 1), 'used_gb' => round(($diskTotal-$diskFree)/1024/1024/1024, 1)],
        'php_version' => phpversion(),
        'server_time' => date('Y-m-d H:i:s T'),
        'os'  => php_uname('s') . ' ' . php_uname('r'),
    ]), 200);
};
