<?php use Aether\Session; ?>
<!-- Section: Identity Erasure (Account Deletion) -->
<div id="section-deletion_requests" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Identity <span class="gradient-text">Erasure</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Manage global user account removal and GDPR-compliant synchronization.</p>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <div class="glass-card !p-4 !px-6 flex items-center gap-5 shadow-2xl shadow-orange-500/10 border-orange-500/20 group">
                <div class="w-12 h-12 rounded-2xl bg-orange-500/20 flex items-center justify-center text-orange-500 border border-orange-500/20 group-hover:bg-orange-500 group-hover:text-white transition-all scale-100 group-hover:scale-110">
                    <i class="ph-bold ph-warning-circle text-2xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-60">Pending Sync</p>
                    <p class="text-3xl font-black tracking-tighter" id="pending-deletion-count">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-8 border-b border-white/5" id="deletion-tabs">
        <button class="tab-btn active" data-tab="requests" onclick="AdminApp.switchTab('deletion_requests', 'requests')">
            Active Erasure Queue
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn" data-tab="policy" onclick="AdminApp.switchTab('deletion_requests', 'policy')">
            Regulatory Governance
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: Requests -->
    <div id="tab-content-requests" class="tab-content space-y-8 animate-in fade-in duration-700">
        <div class="stat-card !p-0 overflow-hidden">
            <div class="p-8 border-b border-white/5 bg-white/5">
                <h3 class="text-xl font-black uppercase tracking-tight">Active <span class="gradient-text">Applications</span></h3>
                <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mt-1">Pending user de-registration across 24 node clusters</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5" style="background-color: var(--glass-bg);">
                            <th class="py-6 px-8">Identity Profile</th>
                            <th class="py-6 px-8">Reason / Protocol</th>
                            <th class="py-6 px-8 text-center">Sync State</th>
                            <th class="py-6 px-8">Sync Date</th>
                            <th class="py-6 px-8 text-right">Synchronization</th>
                        </tr>
                    </thead>
                    <tbody id="deletion-requests-table-body" class="divide-y divide-white/5 font-medium">
                        <!-- Erasure Packets Synced by JS -->
                        <tr><td colspan="5" class="p-20 text-center"><p class="animate-pulse font-black text-[10px] uppercase tracking-widest opacity-40">Monitoring Ingress Deletions...</p></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Policy -->
    <div id="tab-content-policy" class="tab-content hidden space-y-8 animate-in fade-in duration-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="stat-card border-primary/20 bg-gradient-to-br from-primary/10 to-transparent">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">GDPR Compliance Synchronizer</h3>
                <div class="space-y-6 mt-5 p-4 rounded-3xl bg-white/5 border border-white/5">
                     <p class="text-xs font-bold leading-relaxed opacity-80 uppercase tracking-widest">Global "Right to be Forgotten" protocol is currently active across all European node clusters. Erasure packets are broadcasted to decentralized ledgers upon admin confirmation.</p>
                     <div class="h-px w-full bg-white/10 my-6"></div>
                     <div class="flex justify-between items-center text-[10px] font-black tracking-widest uppercase">
                          <span>Auto-Purge Threshold</span>
                          <span class="text-primary">30 Earth Days</span>
                     </div>
                </div>
                <button class="w-full mt-6 btn-primary !rounded-[1.25rem] !py-3 !text-[10px] !font-black !uppercase tracking-widest !justify-center shadow-none">Modify Standard</button>
            </div>

            <div class="stat-card">
                 <h3 class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-8">Asset Protection Protocols</h3>
                 <div class="space-y-6">
                      <div class="flex items-center gap-4 p-4 rounded-2xl hover:bg-white/5 transition-all">
                           <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-400/20"><i class="ph-bold ph-database text-lg"></i></div>
                           <div>
                                <p class="text-xs font-black uppercase tracking-tight">Database Scrubbing</p>
                                <p class="text-[9px] font-bold opacity-40 uppercase tracking-widest">Multi-Cluster Sync</p>
                           </div>
                      </div>
                      <div class="flex items-center gap-4 p-4 rounded-2xl hover:bg-white/5 transition-all">
                           <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-400 border border-purple-400/20"><i class="ph-bold ph-image text-lg"></i></div>
                           <div>
                                <p class="text-xs font-black uppercase tracking-tight">Media Depletion</p>
                                <p class="text-[9px] font-bold opacity-40 uppercase tracking-widest">CDNs Purged</p>
                           </div>
                      </div>
                      <div class="flex items-center gap-4 p-4 rounded-2xl hover:bg-white/5 transition-all">
                           <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-400 border border-orange-500/20"><i class="ph-bold ph-chats text-lg"></i></div>
                           <div>
                                <p class="text-xs font-black uppercase tracking-tight">Message Erasure</p>
                                <p class="text-[9px] font-bold opacity-40 uppercase tracking-widest">Full Thread WIPEOUT</p>
                           </div>
                      </div>
                 </div>
            </div>
        </div>
    </div>
</div>
