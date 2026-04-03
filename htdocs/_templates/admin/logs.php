<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Server Health</h1>
            <p class="text-muted-foreground mt-1" style="color: var(--text-muted);">Real-time monitoring and system event logs.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 px-4 py-2 rounded-2xl border" style="border-color: var(--border-color); background: var(--glass-bg);">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-xs font-bold uppercase tracking-wider">System Online</span>
            </div>
            <button onclick="location.reload()" class="p-2.5 rounded-2xl hover:bg-white/5 transition-all border" style="border-color: var(--border-color);">
                <i class="ph-bold ph-arrow-clockwise text-lg"></i>
            </button>
        </div>
    </div>

    <!-- Top Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="stat-card">
            <div class="flex items-start justify-between mb-4">
                <div class="p-3 rounded-2xl bg-blue-500/10 text-blue-500">
                    <i class="ph-bold ph-clock text-2xl"></i>
                </div>
                <span class="text-[10px] font-bold uppercase text-blue-500/60">Uptime</span>
            </div>
            <h3 class="text-2xl font-bold">14d 6h 32m</h3>
            <p class="text-xs mt-1" style="color: var(--text-muted);">Since last deployment</p>
        </div>
        
        <div class="stat-card">
            <div class="flex items-start justify-between mb-4">
                <div class="p-3 rounded-2xl bg-purple-500/10 text-purple-500">
                    <i class="ph-bold ph-cpu text-2xl"></i>
                </div>
                <span class="text-[10px] font-bold uppercase text-purple-500/60">PHP Version</span>
            </div>
            <h3 class="text-2xl font-bold"><?= phpversion() ?></h3>
            <p class="text-xs mt-1" style="color: var(--text-muted);">Optimized Engine</p>
        </div>

        <div class="stat-card">
            <div class="flex items-start justify-between mb-4">
                <div class="p-3 rounded-2xl bg-orange-500/10 text-orange-500">
                    <i class="ph-bold ph-database text-2xl"></i>
                </div>
                <span class="text-[10px] font-bold uppercase text-orange-500/60">Database</span>
            </div>
            <h3 class="text-2xl font-bold">MySQL 8.0</h3>
            <p class="text-xs mt-1" style="color: var(--text-muted);">Active Connection</p>
        </div>

        <div class="stat-card">
            <div class="flex items-start justify-between mb-4">
                <div class="p-3 rounded-2xl bg-green-500/10 text-green-500">
                    <i class="ph-bold ph-hard-drives text-2xl"></i>
                </div>
                <span class="text-[10px] font-bold uppercase text-green-500/60">Storage</span>
            </div>
            <h3 class="text-2xl font-bold">64.2 GB</h3>
            <p class="text-xs mt-1" style="color: var(--text-muted);">82% available</p>
        </div>
    </div>

    <!-- Resource Monitors -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="p-8 rounded-[2.5rem] border overflow-hidden relative" style="background-color: var(--surface); border-color: var(--border-color);">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold flex items-center gap-3">
                        <i class="ph-bold ph-activity text-primary"></i>
                        Resource Usage
                    </h3>
                    <div class="flex items-center gap-4 text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-muted);">
                        <span class="flex items-center gap-1.5"><div class="w-1.5 h-1.5 rounded-full bg-primary"></div> CPU</span>
                        <span class="flex items-center gap-1.5"><div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div> RAM</span>
                    </div>
                </div>

                <div class="space-y-8">
                    <!-- CPU usage bar -->
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-semibold">Central Processing Unit (CPU)</span>
                            <span class="text-sm font-bold text-primary">12%</span>
                        </div>
                        <div class="h-3 w-full bg-white/5 rounded-full overflow-hidden border border-white/5">
                            <div class="h-full bg-gradient-to-r from-primary to-blue-500 animate-[pulse_3s_infinite]" style="width: 12%"></div>
                        </div>
                    </div>

                    <!-- RAM usage bar -->
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-semibold">Memory Utilization (RAM)</span>
                            <span class="text-sm font-bold text-purple-500">2.4GB / 8GB</span>
                        </div>
                        <div class="h-3 w-full bg-white/5 rounded-full overflow-hidden border border-white/5">
                            <div class="h-full bg-gradient-to-r from-purple-500 to-pink-500" style="width: 30%"></div>
                        </div>
                    </div>

                    <!-- Threads usage bar -->
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-semibold">Max Execution Threads</span>
                            <span class="text-sm font-bold text-orange-500">Active (42/120)</span>
                        </div>
                        <div class="h-3 w-full bg-white/5 rounded-full overflow-hidden border border-white/5">
                            <div class="h-full bg-gradient-to-r from-orange-500 to-yellow-500" style="width: 35%"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Decorative background elements -->
                <div class="absolute -bottom-12 -right-12 w-64 h-64 bg-primary/5 rounded-full blur-3xl -z-10"></div>
            </div>

            <!-- Recent System Log -->
            <div class="p-8 rounded-[2.5rem] border" style="background-color: var(--surface); border-color: var(--border-color);">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold">Live System Log</h3>
                    <button class="text-xs font-bold uppercase tracking-widest text-primary hover:underline">Clear Logs</button>
                </div>
                <div class="space-y-4 max-h-[400px] overflow-y-auto pr-4 custom-scrollbar">
                    <?php
                    $logs = [
                        ['time' => '14:23:01', 'type' => 'INFO', 'msg' => 'Admin session extended for user #4122'],
                        ['time' => '14:20:15', 'type' => 'WARN', 'msg' => 'Multiple failed login attempts from 192.168.1.104'],
                        ['time' => '14:15:22', 'type' => 'SUCCESS', 'msg' => 'Database backup routine completed'],
                        ['time' => '14:10:05', 'type' => 'INFO', 'msg' => 'Template engine cache cleared'],
                        ['time' => '14:05:44', 'type' => 'INFO', 'msg' => 'New user registered: sarah_dev'],
                        ['time' => '13:58:12', 'type' => 'CRITICAL', 'msg' => 'API endpoint /v1/channels returned 500 error'],
                        ['time' => '13:45:30', 'type' => 'SUCCESS', 'msg' => 'SSL Certificate renewed successfully'],
                    ];

                    foreach ($logs as $log):
                        $badge = 'badge-neutral';
                        if ($log['type'] == 'SUCCESS') $badge = 'badge-success';
                        if ($log['type'] == 'WARN') $badge = 'badge-warning';
                        if ($log['type'] == 'CRITICAL') $badge = 'badge-danger';
                    ?>
                    <div class="flex items-center gap-4 py-3 border-b border-white/5 last:border-0 hover:bg-white/5 transition-colors px-2 rounded-xl">
                        <span class="text-xs font-mono" style="color: var(--text-muted);"><?= $log['time'] ?></span>
                        <span class="<?= $badge ?>"><?= $log['type'] ?></span>
                        <span class="text-sm font-medium flex-grow"><?= $log['msg'] ?></span>
                        <i class="ph ph-caret-right text-gray-600"></i>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="p-8 rounded-[2.5rem] border bg-gradient-to-br from-primary/10 to-transparent" style="border-color: var(--border-color);">
                <h3 class="text-lg font-bold mb-6">Server Environment</h3>
                <div class="space-y-5">
                    <div class="flex justify-between items-center group">
                        <span class="text-sm font-medium flex items-center gap-2" style="color: var(--text-muted);">
                            <i class="ph ph-linux-logo text-lg group-hover:text-primary transition-colors"></i> OS
                        </span>
                        <span class="text-sm font-bold">Ubuntu 22.04 LTS</span>
                    </div>
                    <div class="flex justify-between items-center group">
                        <span class="text-sm font-medium flex items-center gap-2" style="color: var(--text-muted);">
                            <i class="ph ph-globe text-lg group-hover:text-primary transition-colors"></i> Server
                        </span>
                        <span class="text-sm font-bold">Nginx 1.24.0</span>
                    </div>
                    <div class="flex justify-between items-center group">
                        <span class="text-sm font-medium flex items-center gap-2" style="color: var(--text-muted);">
                            <i class="ph ph-brackets-curly text-lg group-hover:text-primary transition-colors"></i> Engine
                        </span>
                        <span class="text-sm font-bold">Zend Engine v4.2</span>
                    </div>
                    <div class="flex justify-between items-center group">
                        <span class="text-sm font-medium flex items-center gap-2" style="color: var(--text-muted);">
                            <i class="ph ph-shield-check text-lg group-hover:text-primary transition-colors"></i> Security
                        </span>
                        <span class="text-xs font-bold px-2 py-1 bg-green-500/20 text-green-500 rounded-lg">Firewall Active</span>
                    </div>
                </div>
            </div>

            <div class="p-8 rounded-[2.5rem] border" style="background-color: var(--surface); border-color: var(--border-color);">
                <h3 class="text-lg font-bold mb-6 italic">Network Traffic</h3>
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500">
                            <i class="ph-bold ph-arrow-up"></i>
                        </div>
                        <div class="flex-grow">
                            <p class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-muted);">Upload Speed</p>
                            <p class="text-lg font-bold">12.4 Mbps</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                            <i class="ph-bold ph-arrow-down"></i>
                        </div>
                        <div class="flex-grow">
                            <p class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-muted);">Download Speed</p>
                            <p class="text-lg font-bold">48.9 Mbps</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Action -->
            <button class="w-full p-6 rounded-[2rem] bg-primary text-white font-bold flex items-center justify-center gap-3 shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">
                <i class="ph ph-terminal text-xl"></i>
                Open System Terminal
            </button>
        </div>
    </div>
</div>
