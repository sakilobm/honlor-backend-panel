<div id="section-logs" class="section space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black tracking-tighter mb-2 uppercase">Command <span class="gradient-text">Center</span></h1>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Real-time telemetry and global node latency orchestration.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-3 px-6 py-3 rounded-2xl border backdrop-blur-xl shadow-xl shadow-green-500/5 group transition-all" style="border-color: var(--border-color); background: var(--surface-glass);">
                <div class="w-3 h-3 rounded-full bg-green-500 animate-pulse shadow-[0_0_15px_#22c55e]"></div>
                <span class="text-[10px] font-black uppercase tracking-[0.2em]">Cluster Online</span>
            </div>
            <button onclick="AdminApp.initLogs()" class="p-3.5 rounded-2xl bg-white/5 hover:bg-primary hover:text-white transition-all border border-white/5 active:scale-90" title="Flush & Refresh">
                <i class="ph-bold ph-arrow-clockwise text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-8 border-b border-[var(--border-color)]" id="logs-tabs">
        <button class="tab-btn active uppercase tracking-[0.2em] text-[10px] font-black whitespace-nowrap" data-tab="telemetry" onclick="AdminApp.switchTab('logs','telemetry')">
            Live Telemetry <div class="tab-underline"></div>
        </button>
        <button class="tab-btn uppercase tracking-[0.2em] text-[10px] font-black whitespace-nowrap" data-tab="server-controls" onclick="AdminApp.switchTab('logs','server-controls'); AdminApp.initServerControls();">
            Server Health &amp; Throttles <div class="tab-underline"></div>
        </button>
        <button class="tab-btn uppercase tracking-[0.2em] text-[10px] font-black whitespace-nowrap" data-tab="webhooks" onclick="AdminApp.switchTab('logs','webhooks'); AdminApp.initWebhookHub();">
            <span class="flex items-center gap-2">Webhook Hub <span id="webhook-error-badge" class="hidden w-2 h-2 rounded-full bg-red-500 animate-pulse"></span></span>
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab: Live Telemetry (all existing content wrapped) -->
    <div id="tab-content-telemetry" class="tab-content space-y-8 animate-in fade-in duration-700">

    <!-- Stats Grid (High-Fidelity) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="stat-card group relative overflow-hidden">
            <div class="flex justify-between items-start mb-8">
                <div class="w-12 h-12 bg-blue-500/20 rounded-[1.25rem] flex items-center justify-center text-blue-400 border border-blue-400/20 transition-all group-hover:bg-blue-400 group-hover:text-white">
                    <i class="ph-bold ph-clock text-2xl"></i>
                </div>
                <span class="badge-neutral border-none uppercase tracking-widest text-[9px]">Uptime</span>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">System Stability</p>
            <h4 class="text-3xl font-black tracking-tighter">14d 06h 42m</h4>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-400/30 to-transparent"></div>
        </div>
        
        <div class="stat-card group relative overflow-hidden">
            <div class="flex justify-between items-start mb-8">
                <div class="w-12 h-12 bg-purple-500/20 rounded-[1.25rem] flex items-center justify-center text-purple-400 border border-purple-400/20 transition-all group-hover:bg-purple-400 group-hover:text-white">
                    <i class="ph-bold ph-cpu text-2xl"></i>
                </div>
                <span class="badge-neutral border-none uppercase tracking-widest text-[9px]">Runtime</span>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">PHP Protocol</p>
            <h4 class="text-3xl font-black tracking-tighter"><?= phpversion() ?></h4>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-purple-400/30 to-transparent"></div>
        </div>

        <div class="stat-card group relative overflow-hidden">
            <div class="flex justify-between items-start mb-8">
                <div class="w-12 h-12 bg-orange-500/20 rounded-[1.25rem] flex items-center justify-center text-orange-400 border border-orange-400/20 transition-all group-hover:bg-orange-400 group-hover:text-white">
                    <i class="ph-bold ph-database text-2xl"></i>
                </div>
                <span class="badge-neutral border-none uppercase tracking-widest text-[9px]">Health</span>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">DB Orchestration</p>
            <h4 class="text-3xl font-black tracking-tighter">MySQL 8.0</h4>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-orange-400/30 to-transparent"></div>
        </div>

        <div class="stat-card group relative overflow-hidden">
            <div class="flex justify-between items-start mb-8">
                <div class="w-12 h-12 bg-green-500/20 rounded-[1.25rem] flex items-center justify-center text-green-400 border border-green-400/20 transition-all group-hover:bg-green-400 group-hover:text-white">
                    <i class="ph-bold ph-hard-drives text-2xl"></i>
                </div>
                <span class="badge-neutral border-none uppercase tracking-widest text-[9px]">Resource</span>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Persistent Storage</p>
            <h4 class="text-2xl font-black tracking-tighter">64.2 / 250 GB</h4>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-green-400/30 to-transparent"></div>
        </div>
    </div>

    <!-- Monitoring Grids -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <!-- Glowing Resource Monitor -->
            <div class="glass-card !p-8 relative overflow-hidden group">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h3 class="text-xl font-black uppercase tracking-tight">Active <span class="gradient-text">Telemetry</span></h3>
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-50">Global Node Resource Distribution</p>
                    </div>
                    <div class="p-1 rounded-2xl flex border bg-opacity-20 backdrop-blur-md" style="background-color: var(--glass-bg); border-color: var(--border-color);">
                        <button class="px-6 py-2 bg-primary text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-[0_0_20px_rgba(124,106,255,0.4)] transition-all">Pulse</button>
                    </div>
                </div>

                <div class="space-y-10">
                    <!-- CPU usage bar -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <span class="text-xs font-black uppercase tracking-widest opacity-80">CPU Throughput</span>
                            <span class="text-lg font-black text-primary tracking-tighter" id="monitor-cpu-text">12%</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full overflow-hidden border" style="background-color: var(--glass-bg); border-color: var(--border-color);">
                            <div id="monitor-cpu-bar" class="h-full bg-gradient-to-r from-primary to-blue-500 shadow-[0_0_15px_#7c6aff] transition-all duration-1000 ease-out" style="width: 12%"></div>
                        </div>
                    </div>

                    <!-- RAM usage bar -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <span class="text-xs font-black uppercase tracking-widest opacity-80">Memory (RAM) Allocation</span>
                            <span class="text-lg font-black text-purple-400 tracking-tighter" id="monitor-ram-text">2.4 GB</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full overflow-hidden border" style="background-color: var(--glass-bg); border-color: var(--border-color);">
                            <div id="monitor-ram-bar" class="h-full bg-gradient-to-r from-purple-500 to-pink-500 shadow-[0_0_15px_#a855f7] transition-all duration-1000 ease-out" style="width: 30%"></div>
                        </div>
                    </div>

                    <!-- Connections usage bar -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <span class="text-xs font-black uppercase tracking-widest opacity-80">Active Concurrent Streams</span>
                            <span class="text-lg font-black text-orange-400 tracking-tighter" id="monitor-threads-text">42 Active</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full overflow-hidden border" style="background-color: var(--glass-bg); border-color: var(--border-color);">
                            <div id="monitor-threads-bar" class="h-full bg-gradient-to-r from-orange-500 to-yellow-500 shadow-[0_0_15px_#f97316] transition-all duration-1000 ease-out" style="width: 35%"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Decorative background elements -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/5 rounded-full blur-[100px] -z-10 group-hover:bg-primary/10 transition-all"></div>
            </div>

            <!-- Event Ledger (Live Logs) -->
            <div class="glass-card !p-0 overflow-hidden">
                <div class="p-8 border-b flex justify-between items-center bg-white/5" style="border-color: var(--border-color);">
                    <div class="flex items-center gap-4">
                        <div class="w-2 h-2 bg-primary rounded-full animate-pulse shadow-[0_0_10px_#7c6aff]"></div>
                        <h3 class="text-xl font-black uppercase tracking-tight">System <span class="gradient-text">Ledger</span></h3>
                    </div>
                    <button onclick="AdminApp.loadLogList()" class="btn-secondary !py-2.5 !px-5 !rounded-xl text-[10px] font-black uppercase tracking-widest">
                        Refresh Sync
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5" style="background-color: var(--glass-bg);">
                                <th class="py-6 px-8">Audit ID</th>
                                <th class="py-6 px-8">Process Context</th>
                                <th class="py-6 px-8">Actor</th>
                                <th class="py-6 px-8">Node Interface</th>
                                <th class="py-6 px-8 text-right">Synchronization</th>
                            </tr>
                        </thead>
                        <tbody id="logs-table-body" class="divide-y divide-white/5">
                            <!-- Logs dynamically synchronized by JS -->
                            <tr><td colspan="5" class="p-20 text-center"><p class="animate-pulse font-black text-[10px] uppercase tracking-widest opacity-40">Awaiting Packets...</p></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Command Group -->
        <div class="space-y-8">
            <div class="glass-card !p-8 border-primary/20 bg-gradient-to-br from-primary/10 to-transparent">
                <h3 class="text-xl font-black uppercase tracking-tight mb-8">Infrastucture</h3>
                <div class="space-y-6">
                    <div class="flex justify-between items-center group">
                        <span class="text-xs font-black uppercase tracking-widest flex items-center gap-3 opacity-60 group-hover:opacity-100 transition-all">
                            <i class="ph ph-linux-logo text-xl text-primary"></i> Operating System
                        </span>
                        <span class="text-xs font-black tracking-widest">Ubuntu 22.04 LTS</span>
                    </div>
                    <div class="flex justify-between items-center group">
                        <span class="text-xs font-black uppercase tracking-widest flex items-center gap-3 opacity-60 group-hover:opacity-100 transition-all">
                            <i class="ph ph-globe text-xl text-blue-400"></i> Global Interface
                        </span>
                        <span class="text-xs font-black tracking-widest">Nginx 1.24.4</span>
                    </div>
                    <div class="flex justify-between items-center group">
                        <span class="text-xs font-black uppercase tracking-widest flex items-center gap-3 opacity-60 group-hover:opacity-100 transition-all">
                            <i class="ph ph-brackets-curly text-xl text-purple-400"></i> Protocol Engine
                        </span>
                        <span class="text-xs font-black tracking-widest">Zend 4.2.0</span>
                    </div>
                    <div class="flex justify-between items-center group">
                        <span class="text-xs font-black uppercase tracking-widest flex items-center gap-3 opacity-60 group-hover:opacity-100 transition-all">
                            <i class="ph ph-shield-check text-xl text-green-400"></i> Shield Status
                        </span>
                        <span class="px-3 py-1 bg-green-500/10 text-green-500 text-[9px] font-black uppercase rounded-lg border border-green-500/20">Operational</span>
                    </div>
                </div>
            </div>

            <!-- I/O Throughput Tracker -->
            <div class="glass-card !p-8">
                <h3 class="text-xl font-black uppercase tracking-tight mb-8">Packet <span class="gradient-text">Flow</span></h3>
                <div class="space-y-8">
                    <div class="flex items-center gap-5 group">
                        <div class="w-12 h-12 rounded-2xl bg-orange-500/20 flex items-center justify-center text-orange-400 border border-orange-500/20 group-hover:bg-orange-500 group-hover:text-white transition-all scale-100 group-hover:scale-110">
                            <i class="ph-bold ph-arrow-up-right text-xl"></i>
                        </div>
                        <div class="flex-grow">
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] mb-1 opacity-50">Ingress Streams</p>
                            <p class="text-2xl font-black tracking-tighter" id="monitor-ingress-text">12.4 Mbps</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-5 group">
                        <div class="w-12 h-12 rounded-2xl bg-blue-500/20 flex items-center justify-center text-blue-400 border border-blue-400/20 group-hover:bg-blue-400 group-hover:text-white transition-all scale-100 group-hover:scale-110">
                            <i class="ph-bold ph-arrow-down-left text-xl"></i>
                        </div>
                        <div class="flex-grow">
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] mb-1 opacity-50">Egress Streams</p>
                            <p class="text-2xl font-black tracking-tighter" id="monitor-egress-text">48.9 Mbps</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Theme-Aware Terminal Shortcut (Premium Capsule) -->
            <button onclick="AdminApp.openTerminal()" 
                    class="w-full relative group overflow-hidden p-6 rounded-full transition-all border shadow-2xl"
                    style="background-color: var(--surface); border-color: var(--border-color); color: var(--text-main);">
                <div class="flex items-center justify-center gap-5 relative z-10">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-primary border transition-all"
                         style="background-color: var(--glass-bg); border-color: var(--border-color);">
                        <i class="ph-bold ph-terminal-window text-xl"></i>
                    </div>
                    <span class="text-[11px] font-black uppercase tracking-[0.3em]">Initialize Kernel CLI</span>
                </div>
                <!-- Inner Glow for Premium feel -->
                <div class="absolute inset-0 bg-gradient-to-r from-primary/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                <div class="absolute bottom-0 left-0 w-0 h-1 bg-primary group-hover:w-full transition-all duration-700 shadow-[0_0_15px_rgba(124,106,255,0.5)]"></div>
            </button>
        </div>
    </div><!-- /monitoring-grid -->
    </div><!-- /tab-content-telemetry -->

    <!-- Tab: Server Health & Throttles -->
    <div id="tab-content-server-controls" class="tab-content hidden space-y-8 animate-in fade-in duration-700">

        <!-- Service Health Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6" id="service-health-grid">
            <!-- Nginx -->
            <div class="stat-card !p-6 border-emerald-400/20 group relative overflow-hidden">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-400/10 flex items-center justify-center text-emerald-400 border border-emerald-400/20 group-hover:bg-emerald-400 group-hover:text-white transition-all">
                        <i class="ph-bold ph-globe text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest opacity-40">Web Server</p>
                        <p class="text-xs font-black uppercase tracking-tight">Nginx</p>
                    </div>
                </div>
                <span class="svc-status-badge px-3 py-1 rounded-xl text-[8px] font-black uppercase tracking-widest border bg-emerald-500/10 text-emerald-400 border-emerald-500/20" id="svc-nginx-badge">Checking...</span>
                <p class="text-[9px] font-bold opacity-30 mt-3 uppercase tracking-widest" id="svc-nginx-latency">—</p>
                <div class="absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-emerald-400/30 to-transparent"></div>
            </div>
            <!-- PHP-FPM -->
            <div class="stat-card !p-6 border-blue-400/20 group relative overflow-hidden">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-2xl bg-blue-400/10 flex items-center justify-center text-blue-400 border border-blue-400/20 group-hover:bg-blue-400 group-hover:text-white transition-all">
                        <i class="ph-bold ph-brackets-curly text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest opacity-40">Runtime</p>
                        <p class="text-xs font-black uppercase tracking-tight">PHP-FPM</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-xl text-[8px] font-black uppercase tracking-widest border bg-emerald-500/10 text-emerald-400 border-emerald-500/20" id="svc-phpfpm-badge">Checking...</span>
                <p class="text-[9px] font-bold opacity-30 mt-3 uppercase tracking-widest" id="svc-phpfpm-latency">—</p>
                <div class="absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-blue-400/30 to-transparent"></div>
            </div>
            <!-- MySQL -->
            <div class="stat-card !p-6 border-orange-400/20 group relative overflow-hidden">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-2xl bg-orange-400/10 flex items-center justify-center text-orange-400 border border-orange-400/20 group-hover:bg-orange-400 group-hover:text-white transition-all">
                        <i class="ph-bold ph-database text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest opacity-40">Database</p>
                        <p class="text-xs font-black uppercase tracking-tight">MySQL</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-xl text-[8px] font-black uppercase tracking-widest border bg-emerald-500/10 text-emerald-400 border-emerald-500/20" id="svc-mysql-badge">Checking...</span>
                <p class="text-[9px] font-bold opacity-30 mt-3 uppercase tracking-widest" id="svc-mysql-latency">—</p>
                <div class="absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-orange-400/30 to-transparent"></div>
            </div>
            <!-- Redis -->
            <div class="stat-card !p-6 border-red-400/20 group relative overflow-hidden">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-2xl bg-red-400/10 flex items-center justify-center text-red-400 border border-red-400/20 group-hover:bg-red-400 group-hover:text-white transition-all">
                        <i class="ph-bold ph-lightning text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest opacity-40">Cache Layer</p>
                        <p class="text-xs font-black uppercase tracking-tight">Redis</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-xl text-[8px] font-black uppercase tracking-widest border bg-amber-500/10 text-amber-400 border-amber-500/20" id="svc-redis-badge">Checking...</span>
                <p class="text-[9px] font-bold opacity-30 mt-3 uppercase tracking-widest" id="svc-redis-latency">—</p>
                <div class="absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-red-400/30 to-transparent"></div>
            </div>
        </div>

        <!-- Two-column layout: Throttle Controls + Live Meters -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Throttle Controls -->
            <div class="glass-card !p-8 space-y-8">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xl font-black uppercase tracking-tight">Server <span class="gradient-text">Throttles</span></h3>
                    <button onclick="AdminApp.applyServerThrottles()" class="btn-primary !px-6 !py-3 !rounded-2xl text-[9px] font-black uppercase tracking-widest">
                        <i class="ph-bold ph-floppy-disk"></i> Apply
                    </button>
                </div>

                <!-- Max Connections -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] font-black uppercase tracking-widest opacity-60 flex items-center gap-2"><i class="ph ph-plugs text-sm"></i> Max Connections</label>
                        <span class="text-sm font-black text-primary" id="throttle-connections-val">512</span>
                    </div>
                    <input type="range" id="throttle-connections" min="64" max="2048" step="64" value="512"
                           oninput="document.getElementById('throttle-connections-val').textContent=this.value"
                           class="w-full accent-primary h-1.5 rounded-full cursor-pointer">
                    <div class="flex justify-between text-[9px] font-black opacity-30 uppercase tracking-widest"><span>64</span><span>2048</span></div>
                </div>

                <!-- Request Rate Limit -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] font-black uppercase tracking-widest opacity-60 flex items-center gap-2"><i class="ph ph-gauge text-sm"></i> Rate Limit (req/min)</label>
                        <span class="text-sm font-black text-primary" id="throttle-ratelimit-val">300</span>
                    </div>
                    <input type="range" id="throttle-ratelimit" min="30" max="3000" step="30" value="300"
                           oninput="document.getElementById('throttle-ratelimit-val').textContent=this.value"
                           class="w-full accent-primary h-1.5 rounded-full cursor-pointer">
                    <div class="flex justify-between text-[9px] font-black opacity-30 uppercase tracking-widest"><span>30</span><span>3000</span></div>
                </div>

                <!-- Worker Threads -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] font-black uppercase tracking-widest opacity-60 flex items-center gap-2"><i class="ph ph-cpu text-sm"></i> Worker Threads</label>
                        <span class="text-sm font-black text-primary" id="throttle-workers-val">8</span>
                    </div>
                    <input type="range" id="throttle-workers" min="1" max="64" step="1" value="8"
                           oninput="document.getElementById('throttle-workers-val').textContent=this.value"
                           class="w-full accent-primary h-1.5 rounded-full cursor-pointer">
                    <div class="flex justify-between text-[9px] font-black opacity-30 uppercase tracking-widest"><span>1</span><span>64</span></div>
                </div>

                <!-- Request Timeout -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] font-black uppercase tracking-widest opacity-60 flex items-center gap-2"><i class="ph ph-timer text-sm"></i> Request Timeout (s)</label>
                        <span class="text-sm font-black text-primary" id="throttle-timeout-val">30</span>
                    </div>
                    <input type="range" id="throttle-timeout" min="5" max="300" step="5" value="30"
                           oninput="document.getElementById('throttle-timeout-val').textContent=this.value"
                           class="w-full accent-primary h-1.5 rounded-full cursor-pointer">
                    <div class="flex justify-between text-[9px] font-black opacity-30 uppercase tracking-widest"><span>5s</span><span>300s</span></div>
                </div>

                <!-- Body Size Limit -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] font-black uppercase tracking-widest opacity-60 flex items-center gap-2"><i class="ph ph-file-arrow-up text-sm"></i> Max Body Size (MB)</label>
                        <span class="text-sm font-black text-primary" id="throttle-bodysize-val">10</span>
                    </div>
                    <input type="range" id="throttle-bodysize" min="1" max="100" step="1" value="10"
                           oninput="document.getElementById('throttle-bodysize-val').textContent=this.value"
                           class="w-full accent-primary h-1.5 rounded-full cursor-pointer">
                    <div class="flex justify-between text-[9px] font-black opacity-30 uppercase tracking-widest"><span>1 MB</span><span>100 MB</span></div>
                </div>

                <!-- Quick Toggles -->
                <div class="space-y-4 pt-2 border-t border-[var(--border-color)]">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40 mb-4">Protocol Toggles</p>
                    <?php
                    $toggles = [
                        ['id'=>'tog-gzip','label'=>'Gzip Compression','sub'=>'Compress responses','color'=>'blue','default'=>true],
                        ['id'=>'tog-keepalive','label'=>'HTTP Keep-Alive','sub'=>'Persistent connections','color'=>'green','default'=>true],
                        ['id'=>'tog-ddos','label'=>'DDoS Shield','sub'=>'Auto-block flood patterns','color'=>'red','default'=>true],
                        ['id'=>'tog-cache','label'=>'Response Cache','sub'=>'Edge-layer caching','color'=>'purple','default'=>false],
                    ];
                    foreach ($toggles as $t): ?>
                    <div class="flex items-center justify-between p-4 rounded-2xl border border-[var(--border-color)] bg-[var(--surface-2)] hover:border-<?= $t['color'] ?>-400/30 transition-all group">
                        <div>
                            <p class="text-xs font-black uppercase tracking-tight"><?= $t['label'] ?></p>
                            <p class="text-[9px] font-bold opacity-30 uppercase tracking-widest mt-0.5"><?= $t['sub'] ?></p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="<?= $t['id'] ?>" class="sr-only peer" <?= $t['default'] ? 'checked' : '' ?>>
                            <div class="w-12 h-6 bg-white/10 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[3px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-<?= $t['color'] ?>-500 shadow-lg"></div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Live Resource Meters + Quick Actions -->
            <div class="space-y-8">
                <!-- Live meters card -->
                <div class="glass-card !p-8 space-y-8">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-black uppercase tracking-tight">Live <span class="gradient-text">Meters</span></h3>
                        <span class="flex items-center gap-2 text-[9px] font-black uppercase tracking-widest text-emerald-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live
                        </span>
                    </div>
                    <?php foreach([
                        ['id'=>'sc-cpu','label'=>'CPU Utilization','color'=>'from-primary to-blue-500','shadow'=>'shadow-[0_0_12px_#7c6aff]','val'=>'—'],
                        ['id'=>'sc-ram','label'=>'Memory Usage','color'=>'from-purple-500 to-pink-500','shadow'=>'shadow-[0_0_12px_#a855f7]','val'=>'—'],
                        ['id'=>'sc-disk','label'=>'Disk I/O','color'=>'from-amber-500 to-orange-500','shadow'=>'shadow-[0_0_12px_#f97316]','val'=>'—'],
                        ['id'=>'sc-net','label'=>'Network Throughput','color'=>'from-cyan-500 to-blue-400','shadow'=>'shadow-[0_0_12px_#06b6d4]','val'=>'—'],
                    ] as $m): ?>
                    <div class="space-y-2">
                        <div class="flex justify-between items-end">
                            <span class="text-[10px] font-black uppercase tracking-widest opacity-70"><?= $m['label'] ?></span>
                            <span class="text-sm font-black tracking-tighter" id="<?= $m['id'] ?>-text" style="color:var(--text-main)"><?= $m['val'] ?></span>
                        </div>
                        <div class="h-2 w-full rounded-full overflow-hidden" style="background-color:var(--glass-bg); border:1px solid var(--border-color);">
                            <div id="<?= $m['id'] ?>-bar" class="h-full bg-gradient-to-r <?= $m['color'] ?> <?= $m['shadow'] ?> transition-all duration-1000 ease-out rounded-full" style="width:0%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Quick Actions -->
                <div class="glass-card !p-8 space-y-4">
                    <h3 class="text-xl font-black uppercase tracking-tight mb-6">Quick <span class="gradient-text">Actions</span></h3>
                    <?php
                    $actions = [
                        ['icon'=>'ph-broom','label'=>'Flush OPCache','sub'=>'Clear PHP bytecode cache','color'=>'blue','fn'=>"AdminApp.serverAction('flush_opcache')"],
                        ['icon'=>'ph-trash','label'=>'Purge Response Cache','sub'=>'Clear edge-layer CDN cache','color'=>'amber','fn'=>"AdminApp.serverAction('purge_cache')"],
                        ['icon'=>'ph-arrow-clockwise','label'=>'Restart PHP-FPM','sub'=>'Graceful worker restart','color'=>'purple','fn'=>"AdminApp.serverAction('restart_phpfpm')"],
                        ['icon'=>'ph-shield-warning','label'=>'Trigger Health Check','sub'=>'Full stack diagnostic run','color'=>'emerald','fn'=>"AdminApp.initServerControls()"],
                    ];
                    foreach ($actions as $a): ?>
                    <button onclick="<?= $a['fn'] ?>" class="w-full flex items-center gap-5 p-5 rounded-2xl border border-[var(--border-color)] bg-[var(--surface-2)] hover:border-<?= $a['color'] ?>-400/40 hover:bg-<?= $a['color'] ?>-400/[0.04] transition-all group text-left">
                        <div class="w-11 h-11 rounded-2xl bg-<?= $a['color'] ?>-400/10 flex items-center justify-center text-<?= $a['color'] ?>-400 border border-<?= $a['color'] ?>-400/20 group-hover:scale-110 transition-transform shrink-0">
                            <i class="ph-bold <?= $a['icon'] ?> text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black uppercase tracking-tight"><?= $a['label'] ?></p>
                            <p class="text-[9px] font-bold opacity-30 uppercase tracking-widest mt-0.5"><?= $a['sub'] ?></p>
                        </div>
                        <i class="ph-bold ph-arrow-right ml-auto opacity-0 group-hover:opacity-60 transition-all text-sm"></i>
                    </button>
                    <?php endforeach; ?>
                </div>

                <!-- Uptime / Info block -->
                <div class="glass-card !p-8 border-primary/10 bg-gradient-to-br from-primary/[0.04] to-transparent space-y-5">
                    <h3 class="text-xl font-black uppercase tracking-tight">Node <span class="gradient-text">Info</span></h3>
                    <?php
                    $info = [
                        ['icon'=>'ph-linux-logo','label'=>'OS','val'=>php_uname('s').' '.php_uname('r'),'color'=>'text-primary'],
                        ['icon'=>'ph-brackets-curly','label'=>'PHP Version','val'=>phpversion(),'color'=>'text-blue-400'],
                        ['icon'=>'ph-database','label'=>'Database','val'=>'MySQL 8.0','color'=>'text-orange-400'],
                        ['icon'=>'ph-clock','label'=>'Server Time','val'=>date('Y-m-d H:i:s T'),'color'=>'text-emerald-400'],
                    ];
                    foreach ($info as $row): ?>
                    <div class="flex justify-between items-center group">
                        <span class="text-[10px] font-black uppercase tracking-widest opacity-50 flex items-center gap-2 group-hover:opacity-100 transition-all">
                            <i class="ph <?= $row['icon'] ?> text-base <?= $row['color'] ?>"></i> <?= $row['label'] ?>
                        </span>
                        <span class="text-[10px] font-black tracking-widest font-mono"><?= htmlspecialchars($row['val']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div><!-- /tab-content-server-controls -->

    <!-- Tab: Webhook Hub -->
    <div id="tab-content-webhooks" class="tab-content hidden space-y-8 animate-in fade-in duration-700">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h3 class="text-2xl font-black uppercase tracking-tight">Webhook <span class="gradient-text">Hub</span></h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40 mt-1">Monitor outbound hook health, inspect errors, and manage delivery pipelines</p>
            </div>
            <button onclick="AdminApp.openAddWebhookModal()" class="btn-primary !px-8 !py-4 !rounded-2xl shadow-xl shadow-primary/20 flex items-center gap-3">
                <i class="ph-bold ph-plus-circle text-lg"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Register Hook</span>
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="stat-card !p-6 border-emerald-400/20 bg-emerald-400/[0.04]">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2">Healthy</p>
                <p class="text-3xl font-black tracking-tighter text-emerald-400" id="wh-stat-healthy">—</p>
            </div>
            <div class="stat-card !p-6 border-red-400/20 bg-red-400/[0.04]">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2">Failing</p>
                <p class="text-3xl font-black tracking-tighter text-red-400" id="wh-stat-failing">—</p>
            </div>
            <div class="stat-card !p-6 border-amber-400/20 bg-amber-400/[0.04]">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2">Paused</p>
                <p class="text-3xl font-black tracking-tighter text-amber-400" id="wh-stat-paused">—</p>
            </div>
            <div class="stat-card !p-6 border-primary/20 bg-primary/[0.04]">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2">Total</p>
                <p class="text-3xl font-black tracking-tighter text-primary" id="wh-stat-total">—</p>
            </div>
        </div>

        <!-- Hook List -->
        <div class="stat-card !p-0 overflow-hidden" style="background-color:var(--glass-bg);border:1px solid var(--border-color);">
            <div class="p-6 border-b flex items-center justify-between" style="border-color:var(--border-color);background-color:var(--surface-glass);">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-primary rounded-full animate-pulse shadow-[0_0_8px_#7c6aff]"></div>
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em]">Registered Endpoints</h4>
                </div>
                <button onclick="AdminApp.initWebhookHub()" class="p-2 rounded-xl bg-white/5 hover:bg-primary hover:text-white transition-all border border-white/5">
                    <i class="ph-bold ph-arrow-clockwise text-sm"></i>
                </button>
            </div>
            <div id="webhook-list-container" class="divide-y" style="border-color:var(--border-color);">
                <div class="p-16 text-center animate-pulse">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-30">Initializing Hook Registry...</p>
                </div>
            </div>
        </div>

    </div><!-- /tab-content-webhooks -->
</div><!-- /section-logs -->
