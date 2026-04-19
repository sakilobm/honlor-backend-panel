<div id="section-reports" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Safety <span class="gradient-text">Center</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Coordinate global integrity protocols and moderate cross-node behavioral incidents.</p>
        </div>
        <div class="flex gap-4 w-full md:w-auto">
            <button class="btn-secondary !rounded-2xl flex-grow md:flex-none justify-center hover:bg-white/10 transition-colors" onclick="AdminApp.switchSection('policy_editor')">
                <i class="ph-bold ph-newspaper text-xl"></i>
                Policy Ledger
            </button>
            <button class="btn-primary !rounded-2xl !px-10 flex-grow md:flex-none justify-center shadow-2xl shadow-primary/20 hover:scale-[1.05] transition-all" onclick="toast.info('Audit Active', 'Synchronizing with global safety beacons...')">
                <i class="ph-bold ph-shield-check text-xl"></i>
                Deep Audit
            </button>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-8 border-b border-white/5" id="reports-tabs">
        <button class="tab-btn active uppercase tracking-widest text-[10px] font-black" data-tab="incidents" onclick="AdminApp.switchTab('reports', 'incidents')">
            Incident Queue
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn uppercase tracking-widest text-[10px] font-black" data-tab="audit" onclick="AdminApp.switchTab('reports', 'audit')">
            Resolution History
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: Incidents -->
    <div id="tab-content-incidents" class="tab-content space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <!-- Telemetry Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="stat-card border-red-500/20 bg-gradient-to-br from-red-500/[0.05] to-transparent relative overflow-hidden group">
                <div class="flex items-center gap-6 relative z-10">
                    <div class="w-16 h-16 rounded-[2rem] bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20 shadow-2xl shadow-red-500/10 group-hover:scale-110 transition-transform duration-500">
                        <i class="ph-bold ph-warning-octagon text-3xl animate-pulse"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Active Queue</p>
                        <p class="text-3xl font-black tracking-tighter mt-1" id="metric-active-incidents">00</p>
                    </div>
                </div>
                <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-red-500/5 rounded-full blur-3xl group-hover:bg-red-500/10 transition-colors"></div>
            </div>

            <div class="stat-card border-emerald-400/20 bg-gradient-to-br from-emerald-400/[0.05] to-transparent relative overflow-hidden group">
                <div class="flex items-center gap-6 relative z-10">
                    <div class="w-16 h-16 rounded-[2rem] bg-emerald-400/10 flex items-center justify-center text-emerald-400 border border-emerald-400/20 shadow-2xl shadow-emerald-400/10 group-hover:rotate-12 transition-transform duration-500">
                        <i class="ph-bold ph-lightning text-3xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Resolution Velocity</p>
                        <p class="text-3xl font-black tracking-tighter mt-1" id="metric-resolution-velocity">0.0%</p>
                    </div>
                </div>
                <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-emerald-400/5 rounded-full blur-3xl group-hover:bg-emerald-400/10 transition-colors"></div>
            </div>

            <div class="stat-card border-primary/20 bg-gradient-to-br from-primary/[0.05] to-transparent relative overflow-hidden group">
                <div class="flex items-center gap-6 relative z-10">
                    <div class="w-16 h-16 rounded-[2rem] bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shadow-2xl shadow-primary/10 group-hover:-rotate-12 transition-transform duration-500">
                        <i class="ph-bold ph-eye text-3xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Safety Beacons</p>
                        <p class="text-3xl font-black tracking-tighter mt-1" id="metric-total-reports">00</p>
                    </div>
                </div>
                <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors"></div>
            </div>
        </div>

        <div class="stat-card !p-0 overflow-hidden shadow-2xl" style="background-color: var(--glass-bg); border: 1px solid var(--border-color);">
            <div class="p-8 border-b border-white/5 bg-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h3 class="text-xl font-black uppercase tracking-tight">Compliance <span class="gradient-text">Orchestrator</span></h3>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mt-1 flex items-center gap-3">
                        <span class="flex h-2 w-2 relative">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                        Managed Global Integrity Sink
                    </p>
                </div>
                <div class="flex gap-4 w-full md:w-auto">
                    <div class="relative flex-grow md:flex-none">
                        <i class="ph-bold ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-primary opacity-60"></i>
                        <input type="text" placeholder="Filter Cases..." 
                               class="w-full md:w-64 rounded-xl pl-12 pr-4 py-3 text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                               style="background-color: var(--glass-bg); border: 1px solid var(--border-color); color: var(--text-main);">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5" style="background-color: var(--glass-bg);">
                            <th class="py-6 px-8">Reporter / Subject</th>
                            <th class="py-6 px-8 text-center">Protocol Violation</th>
                            <th class="py-6 px-8">Evidence Packet</th>
                            <th class="py-6 px-8 text-center">Urgency</th>
                            <th class="py-6 px-8 text-right">Synchronization</th>
                        </tr>
                    </thead>
                    <tbody id="reports-table-body" class="divide-y divide-white/[0.03]">
                        <!-- Incident Packets Synchronized by JS -->
                        <tr><td colspan="5" class="p-32 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
                                <p class="font-black text-[10px] uppercase tracking-[0.3em] opacity-40">Authenticating Safety Ledger...</p>
                            </div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Audit -->
    <div id="tab-content-audit" class="tab-content hidden space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="stat-card border-emerald-400/20 bg-emerald-400/[0.02]">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-10 opacity-60 flex items-center justify-between text-emerald-400">
                    Resolution Resolution Map
                    <span class="px-2 py-1 rounded bg-emerald-400/10 border border-emerald-400/20 text-[8px]">Live History</span>
                </h3>
                <div class="aspect-[21/9] flex items-end justify-between px-10 pb-10 bg-emerald-400/[0.03] border border-emerald-400/10 rounded-[2.5rem] relative overflow-hidden group">
                     <?php for($i=0; $i<18; $i++): ?>
                     <div class="w-3 md:w-5 bg-gradient-to-t from-emerald-400 to-emerald-200 rounded-t-lg transition-all duration-1000 origin-bottom group-hover:scale-y-[1.1] relative z-10" 
                          style="height: <?= rand(40, 95) ?>%; box-shadow: 0 0 20px rgba(52, 211, 153, 0.3);">
                     </div>
                     <?php endfor; ?>
                </div>
                <div class="grid grid-cols-3 gap-8 mt-12 p-8 rounded-[2rem] border border-white/5" style="background-color: var(--glass-bg);">
                     <div class="text-center">
                          <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2 leading-tight">Sync Success</p>
                          <p class="text-3xl font-black text-emerald-400 tracking-tighter">98.4%</p>
                     </div>
                     <div class="text-center border-x border-white/10">
                          <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2 leading-tight">Mean Resolve Time</p>
                          <p class="text-3xl font-black tracking-tighter" style="color: var(--text-main);">14m</p>
                     </div>
                     <div class="text-center">
                          <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2 leading-tight">Integrity Index</p>
                          <p class="text-3xl font-black text-indigo-400 tracking-tighter">Optimal</p>
                     </div>
                </div>
            </div>

            <div class="stat-card border-white/5 bg-white/[0.02] flex flex-col pt-10">
                 <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-12 opacity-60">Verified Resolution Stream</h3>
                 <div class="space-y-6 flex-grow relative min-h-[300px] z-10" id="reports-history-list">
                      <div class="absolute left-[3px] top-4 bottom-4 w-px bg-white/5 z-0"></div>
                      <!-- History Synchronized by JS -->
                 </div>
                 <div class="mt-12 p-6 rounded-3xl border border-dashed border-white/20 text-center" style="background-color: var(--glass-bg);">
                    <p class="text-[9px] font-bold opacity-40 uppercase tracking-[0.2em]" style="color: var(--text-muted);">Authenticating Resolution Ledger with Global Beacons</p>
                 </div>
            </div>
        </div>
    </div>
</div>
