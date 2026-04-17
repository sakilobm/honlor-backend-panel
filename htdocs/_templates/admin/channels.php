<?php use Aether\Session; ?>
<!-- Section: Node Orchestration (Channels) -->
<div id="section-channels" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Node <span class="gradient-text">Registry</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Coordinate global node clusters and high-frequency communication interfaces.</p>
        </div>
        <div class="flex gap-4 w-full md:w-auto">
             <div class="glass-card !p-3 !px-6 flex items-center gap-4 border-indigo-500/20 shadow-xl shadow-indigo-500/5">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 border border-indigo-400/20 shadow-[0_0_15px_rgba(99,102,241,0.2)]">
                    <i class="ph-bold ph-graph text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-widest opacity-40">Active Links</p>
                    <p class="text-xl font-black tracking-tighter" id="metric-total-nodes">00</p>
                </div>
            </div>
            <button class="btn-primary !rounded-2xl !px-8 shadow-xl shadow-primary/20 flex-grow md:flex-none justify-center" onclick="AdminApp.openModal('create-channel-modal')">
                <i class="ph-bold ph-plus-circle text-lg"></i>
                Register Node
            </button>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-8 border-b border-white/5" id="channels-tabs">
        <button class="tab-btn active uppercase tracking-widest text-[10px] font-black" data-tab="list" onclick="AdminApp.switchTab('channels', 'list')">
            Active Clusters
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn uppercase tracking-widest text-[10px] font-black relative" data-tab="protocols" onclick="AdminApp.switchTab('channels', 'protocols')">
            Node Protocols
            <span class="absolute -top-1 -right-2 px-1.5 py-0.5 bg-primary text-[7px] text-white rounded-md animate-pulse">PRO</span>
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: Active Clusters -->
    <div id="tab-content-list" class="tab-content space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="stat-card !p-0 overflow-hidden bg-white/[0.02] border-white/5 shadow-2xl">
            <div class="p-8 border-b border-white/5 bg-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h3 class="text-xl font-black uppercase tracking-tight">Ecosystem <span class="gradient-text">Nodes</span></h3>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mt-1 flex items-center gap-3">
                        <span class="flex h-2 w-2 relative">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Real-time status across global edge clusters
                    </p>
                </div>
                <div class="flex gap-4 w-full md:w-auto">
                    <button class="btn-secondary !rounded-xl !p-2.5 transition-all hover:bg-white/10" onclick="AdminApp.loadChannelList()">
                        <i class="ph ph-arrows-clockwise text-lg"></i>
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5" style="background-color: var(--glass-bg);">
                            <th class="py-6 px-8">Cluster Identity</th>
                            <th class="py-6 px-8">Sync Protocol</th>
                            <th class="py-6 px-8 text-center">Load Factor</th>
                            <th class="py-6 px-8">Deployment</th>
                            <th class="py-6 px-8 text-right">Synchronization</th>
                        </tr>
                    </thead>
                    <tbody id="channels-table-body" class="divide-y divide-white/[0.03]">
                        <!-- Node Clusters Synced by JS -->
                        <tr><td colspan="5" class="p-24 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                                <p class="font-black text-[10px] uppercase tracking-[0.3em] opacity-40">Connecting to Global Registry...</p>
                            </div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Node Protocols -->
    <div id="tab-content-protocols" class="tab-content hidden space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="stat-card border-white/5 bg-white/[0.02] group">
                 <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60 flex items-center justify-between">
                     Global Standards
                     <i class="ph-bold ph-gear text-lg opacity-40 group-hover:rotate-90 transition-transform duration-500"></i>
                 </h3>
                 <div class="space-y-4">
                      <div class="flex items-center justify-between p-5 rounded-3xl bg-white/[0.03] border border-white/5 hover:border-emerald-500/30 transition-all duration-300">
                           <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 border border-emerald-500/20 shadow-xl shadow-emerald-500/5">
                                    <i class="ph-bold ph-shield-check text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest">TLS 1.3 Encryption</p>
                                    <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest mt-0.5">Mandatory Secure Tunnel</p>
                                </div>
                           </div>
                           <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 rounded-lg text-[8px] font-black uppercase tracking-widest">Active</span>
                      </div>
                      <div class="flex items-center justify-between p-5 rounded-3xl bg-white/[0.03] border border-white/5 hover:border-blue-500/30 transition-all duration-300">
                           <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20 shadow-xl shadow-blue-500/5">
                                    <i class="ph-bold ph-swap text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest">gRPC Data Pipeline</p>
                                    <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest mt-0.5">High-frequency Relay</p>
                                </div>
                           </div>
                           <span class="px-3 py-1 bg-blue-500/20 text-blue-400 border border-blue-500/20 rounded-lg text-[8px] font-black uppercase tracking-widest uppercase">Optimized</span>
                      </div>
                 </div>
            </div>

            <div class="stat-card border-primary/20 bg-gradient-to-br from-primary/[0.07] to-transparent relative overflow-hidden group">
                 <div class="absolute -right-4 -bottom-4 w-40 h-40 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-all duration-700"></div>
                 <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Node Integrity Center</h3>
                 <div class="mt-4 p-8 rounded-[3rem] bg-white/[0.03] border border-white/5 text-center flex flex-col items-center backdrop-blur-md">
                      <div class="w-24 h-24 rounded-full border-4 border-dashed border-primary/20 flex items-center justify-center text-primary relative">
                           <div class="absolute inset-0 rounded-full border-4 border-primary border-t-transparent animate-spin opacity-40"></div>
                           <i class="ph-bold ph-lightning text-4xl animate-pulse text-indigo-400"></i>
                      </div>
                      <p class="text-2xl font-black uppercase tracking-tighter mt-8">System <span class="gradient-text">Synchronized</span></p>
                      <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest mt-2 leading-relaxed max-w-[280px]">Global node registry is reporting 100% data integrity across all active communication clusters.</p>
                      <button class="btn-primary !rounded-[1.5rem] !justify-center !px-12 !py-4 mt-10 text-[9px] font-black tracking-[0.3em] uppercase shadow-2xl shadow-primary/20 hover:scale-[1.05] transition-all" onclick="AdminApp.deepScanNetwork()">Run Infrastructure Audit</button>
                 </div>
            </div>
        </div>
    </div>
</div>
