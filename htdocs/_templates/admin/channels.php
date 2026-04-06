<?php use Aether\Session; ?>
<!-- Section: Node Orchestration (Channels) -->
<div id="section-channels" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Node <span class="gradient-text">Registry</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Manage global node clusters and communication interfaces.</p>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <button class="btn-primary !rounded-2xl flex-1 md:flex-none justify-center shadow-xl shadow-primary/20" onclick="openModal('create-channel-modal')">
                <i class="ph-bold ph-plus text-lg"></i>
                Register Node
            </button>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-8 border-b border-white/5" id="channels-tabs">
        <button class="tab-btn active" data-tab="list" onclick="AdminApp.switchTab('channels', 'list')">
            Active Clusters
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn" data-tab="protocols" onclick="AdminApp.switchTab('channels', 'protocols')">
            Node Protocols
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: Active Clusters -->
    <div id="tab-content-list" class="tab-content space-y-8 animate-in fade-in duration-700">
        <div class="stat-card !p-0 overflow-hidden">
            <div class="p-8 border-b border-white/5 bg-white/5">
                <h3 class="text-xl font-black uppercase tracking-tight">Ecosystem <span class="gradient-text">Nodes</span></h3>
                <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mt-1">Real-time status across 24 edge clusters</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5" style="background-color: var(--glass-bg);">
                            <th class="py-6 px-8">Cluster Name</th>
                            <th class="py-6 px-8">Sync Protocol</th>
                            <th class="py-6 px-8">Member Load</th>
                            <th class="py-6 px-8">Deployment</th>
                            <th class="py-6 px-8 text-right">Synchronization</th>
                        </tr>
                    </thead>
                    <tbody id="channels-table-body" class="divide-y divide-white/5">
                        <!-- Node Clusters Synced by JS -->
                        <tr><td colspan="5" class="p-20 text-center"><p class="animate-pulse font-black text-[10px] uppercase tracking-widest opacity-40">Authenticaton Nodes Ingress...</p></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Node Protocols -->
    <div id="tab-content-protocols" class="tab-content hidden space-y-8 animate-in fade-in duration-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="stat-card">
                 <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Global Communication Standards</h3>
                 <div class="space-y-6">
                      <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5 group">
                           <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20"><i class="ph-bold ph-shield-check text-lg"></i></div>
                                <span class="text-xs font-black uppercase tracking-widest">TLS 1.3 Sync</span>
                           </div>
                           <span class="badge-success uppercase tracking-widest text-[9px]">Mandatory</span>
                      </div>
                      <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5 opacity-40">
                           <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-400 border border-orange-400/20"><i class="ph-bold ph-radio-button text-lg"></i></div>
                                <span class="text-xs font-black uppercase tracking-widest">Legacy Auth (Dep)</span>
                           </div>
                           <span class="badge-neutral uppercase tracking-widest text-[9px]">Disabled</span>
                      </div>
                 </div>
            </div>

            <div class="stat-card border-primary/20 bg-gradient-to-br from-primary/10 to-transparent">
                 <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Node Integrity Check</h3>
                 <div class="mt-4 p-8 rounded-3xl bg-white/5 border border-white/5 text-center flex flex-col items-center">
                      <div class="w-20 h-20 rounded-full border-4 border-dashed border-primary/30 flex items-center justify-center text-primary animate-[spin_10s_linear_infinite]">
                           <i class="ph ph-lightning text-3xl"></i>
                      </div>
                      <p class="text-xl font-black uppercase tracking-tighter mt-8">System <span class="gradient-text">Verified</span></p>
                      <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest mt-2 leading-relaxed">All node clusters are currently reporting 100% data integrity and synchronization success.</p>
                      <button class="btn-primary !bg-white !text-primary !shadow-none !justify-center !px-10 !py-3 !rounded-[1.25rem] mt-10 text-[9px] font-black tracking-[0.2em] uppercase border border-primary/10 hover:!bg-primary hover:!text-white transition-all">Deep Scan Cluster</button>
                 </div>
            </div>
        </div>
    </div>
</div>
