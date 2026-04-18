<!-- Section: Cognitive Intelligence Hub (Analytics) -->
<div id="section-analytics" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Cognitive <span class="gradient-text">Intelligence</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Synchronize global node telemetry and analyze real-time cognitive interaction patterns.</p>
        </div>
        <div class="flex gap-4 w-full md:w-auto">
            <div class="glass-card !p-4 !px-8 flex items-center gap-6 shadow-2xl shadow-primary/10 border-primary/20 group hover:scale-[1.02] transition-all">
                <div class="w-14 h-14 rounded-[2rem] bg-primary/10 flex items-center justify-center text-primary border border-primary/20 group-hover:bg-primary group-hover:text-white transition-all">
                    <i class="ph-bold ph-brain text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Intelligence Velocity</p>
                    <p class="text-3xl font-black tracking-tighter mt-1" id="metric-intelligence-velocity">98.4</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-10 border-b border-[var(--border-color)]" id="analytics-tabs">
        <button class="tab-btn active uppercase tracking-[0.2em] text-[10px] font-black" data-tab="neural" onclick="AdminApp.switchTab('analytics', 'neural')">
            Neural Dynamics
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn uppercase tracking-[0.2em] text-[10px] font-black" data-tab="geography" onclick="AdminApp.switchTab('analytics', 'geography')">
            Regional Density
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: Neural -->
    <div id="tab-content-neural" class="tab-content space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <!-- Telemetry Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="stat-card border-indigo-400/20 bg-gradient-to-br from-indigo-400/[0.05] to-transparent group">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 rounded-[2.5rem] bg-indigo-400/10 flex items-center justify-center text-indigo-400 border border-indigo-400/20 shadow-2xl group-hover:scale-110 transition-transform">
                        <i class="ph-bold ph-lightning text-3xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Signal Strength</p>
                        <p class="text-3xl font-black tracking-tighter mt-1" id="metric-signal-strength">0.0%</p>
                    </div>
                </div>
            </div>

            <div class="stat-card border-emerald-400/20 bg-gradient-to-br from-emerald-400/[0.05] to-transparent group">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 rounded-[2.5rem] bg-emerald-400/10 flex items-center justify-center text-emerald-400 border border-emerald-400/20 shadow-2xl group-hover:rotate-12 transition-transform">
                        <i class="ph-bold ph-activity text-3xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Cognitive Retention</p>
                        <p class="text-3xl font-black tracking-tighter mt-1" id="metric-cognitive-retention">0.0%</p>
                    </div>
                </div>
            </div>

            <div class="stat-card border-orange-500/20 bg-gradient-to-br from-orange-500/[0.05] to-transparent group">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 rounded-[2.5rem] bg-orange-500/10 flex items-center justify-center text-orange-400 border border-orange-500/20 shadow-2xl group-hover:-rotate-12 transition-transform">
                        <i class="ph-bold ph-target text-3xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Insight Accuracy</p>
                        <p class="text-3xl font-black tracking-tighter mt-1">Optimal</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Dynamic Insights Module -->
            <div class="stat-card !p-0 overflow-hidden flex flex-col shadow-2xl" style="background-color: var(--glass-bg); border: 1px solid var(--border-color);">
                <div class="p-8 border-b border-[var(--border-color)] bg-[var(--surface-2)] flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        <h3 class="text-xl font-black uppercase tracking-tight">Ecosystem <span class="gradient-text">Insights</span></h3>
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mt-1 flex items-center gap-3">
                            <span class="flex h-2 w-2 relative">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                            </span>
                            Neural Signal Active
                        </p>
                    </div>
                    <button class="btn-primary !p-3 !px-8 !rounded-2xl shadow-xl shadow-primary/20 hover:scale-[1.05] transition-all" onclick="AdminApp.generateInsights()">
                        <i class="ph-bold ph-magic-wand text-lg"></i>
                        Analyze
                    </button>
                </div>
                <div class="p-10 flex-grow flex flex-col justify-center items-center text-center space-y-6" id="insights-container">
                    <div class="w-16 h-16 rounded-full border-4 border-primary border-t-transparent animate-spin"></div>
                    <p class="font-black text-[10px] uppercase tracking-[0.3em] opacity-40">Synchronizing with Global Node Clusters...</p>
                </div>
            </div>

            <!-- Signal Integrity Chart -->
            <div class="stat-card border-[var(--border-color)] bg-[var(--surface-2)]">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-10 opacity-60 flex items-center justify-between text-primary">
                    Signal Integrity Map
                    <span class="px-2 py-1 rounded-lg bg-primary/10 border border-primary/20 text-[8px] tracking-widest">REAL-TIME</span>
                </h3>
                <div class="aspect-[21/9] flex items-end justify-between px-10 pb-10 bg-primary/[0.03] border border-primary/10 rounded-[2.5rem] relative overflow-hidden group">
                     <?php for($i=0; $i<18; $i++): ?>
                     <div class="w-3 md:w-5 bg-gradient-to-t from-primary to-indigo-400 rounded-t-lg transition-all duration-1000 origin-bottom group-hover:scale-y-[1.1] relative z-10" 
                          style="height: <?= rand(40, 95) ?>%; box-shadow: 0 0 20px rgba(124, 106, 255, 0.3);">
                     </div>
                     <?php endfor; ?>
                </div>
                <div class="grid grid-cols-2 gap-8 mt-10">
                    <div class="p-6 rounded-[2rem] bg-[var(--surface-2)] border border-[var(--border-color)]">
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2">Protocol Health</p>
                        <p class="text-2xl font-black tracking-tighter" style="color: var(--text-main);">SHA-512 SECURE</p>
                    </div>
                    <div class="p-6 rounded-[2rem] bg-[var(--surface-2)] border border-[var(--border-color)] text-right">
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2">Node Latency</p>
                        <p class="text-2xl font-black text-indigo-400 tracking-tighter">14ms</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content: Geography -->
    <div id="tab-content-geography" class="tab-content hidden space-y-8 animate-in fade-in duration-700">
        <div class="stat-card !p-0 overflow-hidden shadow-2xl" style="background-color: var(--glass-bg); border: 1px solid var(--border-color);">
             <div class="p-8 border-b border-[var(--border-color)] bg-[var(--surface-2)]">
                 <h3 class="text-xl font-black uppercase tracking-tight">Regional <span class="gradient-text">Density Matrix</span></h3>
                 <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mt-1">Real-time interaction density packets across 24 timezone clusters</p>
             </div>
             <div class="p-10">
                 <div class="grid grid-cols-6 md:grid-cols-12 gap-3 aspect-video md:aspect-[21/8]">
                    <?php for($i=0; $i<12*6; $i++): ?>
                        <div class="rounded-xl border border-[var(--border-color)] transition-all hover:bg-primary/20 hover:scale-[1.15] hover:shadow-[0_0_20px_rgba(124,106,255,0.4)] group/box cursor-help relative" 
                             style="background-color: rgba(124, 106, 255, <?= rand(5, 50) / 100 ?>);">
                             <div class="absolute -top-12 left-1/2 -translate-x-1/2 bg-surface p-3 rounded-xl border border-white/10 text-[9px] font-black uppercase tracking-widest opacity-0 group-hover/box:opacity-100 whitespace-nowrap pointer-events-none transition-all z-20 shadow-2xl">
                                Node Cluster 0<?= rand(1,9) ?>: <?= rand(10, 99) ?>% LOAD
                             </div>
                        </div>
                    <?php endfor; ?>
                </div>
                <div class="flex flex-wrap justify-center gap-10 mt-12 pt-8 border-t border-[var(--border-color)]">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-blue-400"></div>
                        <span class="text-[9px] font-black uppercase tracking-widest opacity-60">North America</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-purple-400"></div>
                        <span class="text-[9px] font-black uppercase tracking-widest opacity-60">Europe</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-orange-400"></div>
                        <span class="text-[9px] font-black uppercase tracking-widest opacity-60">Asia Pacific</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                        <span class="text-[9px] font-black uppercase tracking-widest opacity-60">Others</span>
                    </div>
                </div>
             </div>
        </div>
    </div>
</div>
