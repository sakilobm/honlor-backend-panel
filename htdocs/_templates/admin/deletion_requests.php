<div id="section-deletion_requests" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Identity <span class="gradient-text">Erasure</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Manage global user account removal and GDPR-compliant synchronization.</p>
        </div>
        <div class="flex gap-4 w-full md:w-auto">
            <div class="glass-card !p-4 !px-8 flex items-center gap-6 shadow-2xl shadow-orange-500/10 border-orange-500/20 group hover:scale-[1.02] transition-all">
                <div class="w-14 h-14 rounded-[2rem] bg-orange-500/10 flex items-center justify-center text-orange-500 border border-orange-500/20 group-hover:bg-orange-500 group-hover:text-white transition-all">
                    <i class="ph-bold ph-trash-simple text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Erasure Queue</p>
                    <p class="text-3xl font-black tracking-tighter mt-1" id="pending-deletion-count">00</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-10 border-b border-white/5" id="deletion-tabs">
        <button class="tab-btn active uppercase tracking-[0.2em] text-[10px] font-black" data-tab="requests" onclick="AdminApp.switchTab('deletion_requests', 'requests')">
            Erasure Orchestrator
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn uppercase tracking-[0.2em] text-[10px] font-black" data-tab="policy" onclick="AdminApp.switchTab('deletion_requests', 'policy')">
            Regulatory Governance
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: Requests -->
    <div id="tab-content-requests" class="tab-content space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <!-- Governance Telemetry Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="stat-card border-indigo-400/20 bg-gradient-to-br from-indigo-400/[0.05] to-transparent">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 rounded-[2.5rem] bg-indigo-400/10 flex items-center justify-center text-indigo-400 border border-indigo-400/20 shadow-2xl">
                        <i class="ph-bold ph-shield-check text-3xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Compliance Score</p>
                        <p class="text-3xl font-black tracking-tighter mt-1">99.2%</p>
                    </div>
                </div>
            </div>

            <div class="stat-card border-emerald-400/20 bg-gradient-to-br from-emerald-400/[0.05] to-transparent">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 rounded-[2.5rem] bg-emerald-400/10 flex items-center justify-center text-emerald-400 border border-emerald-400/20 shadow-2xl">
                        <i class="ph-bold ph-lightning text-3xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Sync Velocity</p>
                        <p class="text-3xl font-black tracking-tighter mt-1" id="metric-deletion-velocity">0.0%</p>
                    </div>
                </div>
            </div>

            <div class="stat-card border-primary/20 bg-gradient-to-br from-primary/[0.05] to-transparent">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 rounded-[2.5rem] bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shadow-2xl">
                        <i class="ph-bold ph-arrows-merge text-3xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Sync Beacons</p>
                        <p class="text-3xl font-black tracking-tighter mt-1">Active</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card !p-0 overflow-hidden shadow-2xl" style="background-color: var(--glass-bg); border: 1px solid var(--border-color);">
            <div class="p-8 border-b border-white/5 bg-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h3 class="text-xl font-black uppercase tracking-tight">Active <span class="gradient-text">Erasure Packets</span></h3>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mt-1 flex items-center gap-3">
                        <span class="flex h-2 w-2 relative">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-500 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                        </span>
                        Awaiting Manual Sync Authorization
                    </p>
                </div>
                <div class="flex items-center gap-4">
                     <span class="text-[10px] font-black uppercase tracking-widest opacity-40 mr-4">Node: Global Sink 0xAF</span>
                     <button class="btn-primary !p-3 !rounded-xl !bg-primary/10 !border-primary/20 !text-primary hover:!bg-primary hover:!text-white transition-all shadow-xl shadow-primary/5" onclick="AdminApp.loadDeletionRequests()">
                        <i class="ph-bold ph-arrows-clockwise text-lg"></i>
                     </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5" style="background-color: var(--glass-bg);">
                            <th class="py-6 px-8">Identity / Node</th>
                            <th class="py-6 px-8 text-center">Protocol Justification</th>
                            <th class="py-6 px-8 text-center">Sync State</th>
                            <th class="py-6 px-8">Request Date</th>
                            <th class="py-6 px-8 text-right">Synchronization</th>
                        </tr>
                    </thead>
                    <tbody id="deletion-requests-table-body" class="divide-y divide-white/[0.03]">
                        <!-- Erasure Packets Synced by JS -->
                        <tr><td colspan="5" class="p-32 text-center">
                            <div class="flex flex-col items-center gap-5">
                                <div class="w-12 h-12 border-4 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
                                <p class="font-black text-[10px] uppercase tracking-[0.3em] opacity-40">Authenticating Erasure Streams...</p>
                            </div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Policy -->
    <div id="tab-content-policy" class="tab-content hidden space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="stat-card border-orange-500/20 bg-gradient-to-br from-orange-500/5 to-transparent flex flex-col pt-10">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-12 opacity-60 text-orange-400">GDPR Compliance Synchronizer</h3>
                <div class="p-8 rounded-[2.5rem] bg-orange-500/[0.03] border border-orange-500/10 space-y-8 flex-grow">
                     <div class="flex items-start gap-6 group">
                          <div class="w-2 h-2 rounded-full bg-orange-500 mt-2 shadow-[0_0_12px_rgba(249,115,22,0.6)]"></div>
                          <div>
                               <p class="text-[11px] font-black uppercase tracking-widest leading-relaxed" style="color: var(--text-main);">"Right to be Forgotten" Active</p>
                               <p class="text-[10px] font-medium opacity-60 mt-2 leading-relaxed" style="color: var(--text-muted);">The Aether ecosystem strictly adheres to the 24-hour erasure window for all European and International identity requests.</p>
                          </div>
                     </div>
                     <div class="flex items-start gap-6 group mt-8 pt-8 border-t border-orange-500/10">
                          <div class="w-2 h-2 rounded-full bg-indigo-400 mt-2 shadow-[0_0_12px_rgba(129,140,248,0.6)]"></div>
                          <div>
                               <p class="text-[11px] font-black uppercase tracking-widest leading-relaxed" style="color: var(--text-main);">Multi-Chain Data Purge</p>
                               <p class="text-[10px] font-medium opacity-60 mt-2 leading-relaxed" style="color: var(--text-muted);">Upon admin authorization, erasure packets are broadcasted to all decentralized vault shards and CDN edge locations.</p>
                          </div>
                     </div>
                </div>
                <div class="mt-8 p-6 bg-white/[0.03] rounded-3xl border border-white/5 flex items-center justify-between">
                    <div>
                         <p class="text-[9px] font-black uppercase tracking-widest opacity-40">Sync Protocol</p>
                         <p class="text-xs font-black tracking-tight" style="color: var(--text-main);">SHA-256 ERASURE_SIG_V2</p>
                    </div>
                    <button class="btn-primary !p-3 !px-6 !rounded-xl !text-[9px] !font-black !uppercase tracking-widest">Update Policy</button>
                </div>
            </div>

            <div class="stat-card border-white/5 bg-white/[0.02] pt-10">
                 <h3 class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-12">Asset Protection Protocols</h3>
                 <div class="grid grid-cols-1 gap-4">
                      <div class="group p-6 rounded-[2rem] border border-white/5 hover:border-blue-400/30 bg-white/[0.01] hover:bg-blue-400/[0.03] transition-all flex items-center justify-between">
                           <div class="flex items-center gap-6">
                                <div class="w-14 h-14 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-400/20 group-hover:scale-110 transition-transform">
                                     <i class="ph-bold ph-database text-2xl"></i>
                                </div>
                                <div>
                                     <p class="text-xs font-black uppercase tracking-widest" style="color: var(--text-main);">Database Scrubbing</p>
                                     <p class="text-[9px] font-bold opacity-30 mt-1 uppercase tracking-widest">Multi-Cluster SQL Sync</p>
                                </div>
                           </div>
                           <div class="px-3 py-1 bg-blue-500/10 border border-blue-500/20 rounded-lg text-[8px] font-black text-blue-400 uppercase tracking-widest">Verified</div>
                      </div>

                      <div class="group p-6 rounded-[2rem] border border-white/5 hover:border-purple-400/30 bg-white/[0.01] hover:bg-purple-400/[0.03] transition-all flex items-center justify-between">
                           <div class="flex items-center gap-6">
                                <div class="w-14 h-14 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-400 border border-purple-400/20 group-hover:scale-110 transition-transform">
                                     <i class="ph-bold ph-image text-2xl"></i>
                                </div>
                                <div>
                                     <p class="text-xs font-black uppercase tracking-widest" style="color: var(--text-main);">Media Depletion</p>
                                     <p class="text-[9px] font-bold opacity-30 mt-1 uppercase tracking-widest">Global CDN Purge</p>
                                </div>
                           </div>
                           <div class="px-3 py-1 bg-purple-500/10 border border-purple-400/20 rounded-lg text-[8px] font-black text-purple-400 uppercase tracking-widest">Active</div>
                      </div>

                      <div class="group p-6 rounded-[2rem] border border-white/5 hover:border-orange-500/30 bg-white/[0.01] hover:bg-orange-500/[0.03] transition-all flex items-center justify-between">
                           <div class="flex items-center gap-6">
                                <div class="w-14 h-14 rounded-2xl bg-orange-500/10 flex items-center justify-center text-orange-400 border border-orange-500/20 group-hover:scale-110 transition-transform">
                                     <i class="ph-bold ph-chats-teardrop text-2xl"></i>
                                </div>
                                <div>
                                     <p class="text-xs font-black uppercase tracking-widest" style="color: var(--text-main);">Message Erasure</p>
                                     <p class="text-[9px] font-bold opacity-30 mt-1 uppercase tracking-widest">WIPEOUT V3.1</p>
                                </div>
                           </div>
                           <div class="px-3 py-1 bg-orange-500/10 border border-orange-500/20 rounded-lg text-[8px] font-black text-orange-400 uppercase tracking-widest">Ready</div>
                      </div>
                 </div>
                 <div class="mt-8 p-6 bg-primary/5 rounded-[2rem] border border-dashed border-primary/20 text-center">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40">Identity Vault Governance Protocol v4.0</p>
                 </div>
            </div>
        </div>
    </div>
</div>
