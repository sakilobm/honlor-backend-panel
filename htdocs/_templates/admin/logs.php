<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
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
    </div>
</div>
