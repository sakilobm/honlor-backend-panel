<?php use Aether\Session; ?>
<!-- Section: Ads Pipeline (Marketing) -->
<div id="section-ads" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Ads <span class="gradient-text">Orchestrator</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Synchronize global marketing nodes and optimize ROI through predictive telemetry.</p>
        </div>
        <div class="flex gap-4 w-full md:w-auto">
            <button class="btn-primary !rounded-2xl !px-10 flex-grow md:flex-none justify-center shadow-2xl shadow-primary/20 hover:scale-[1.05] transition-all" onclick="AdminApp.openModal('create-ad-modal')">
                <i class="ph-bold ph-plus-square text-xl"></i>
                Deploy Stream
            </button>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-8 border-b border-white/5" id="ads-tabs">
        <button class="tab-btn active uppercase tracking-widest text-[10px] font-black" data-tab="campaigns" onclick="AdminApp.switchTab('ads', 'campaigns')">
            Active Streams
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn uppercase tracking-widest text-[10px] font-black relative group" data-tab="performance" onclick="AdminApp.switchTab('ads', 'performance')">
            Deep ROI Analytics
            <span class="absolute -top-1 -right-2 px-1.5 py-0.5 bg-indigo-500 text-[7px] text-white rounded-md group-hover:animate-bounce">VIP</span>
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: Campaigns -->
    <div id="tab-content-campaigns" class="tab-content space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <!-- Dashboard Summary Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="stat-card border-primary/20 bg-gradient-to-br from-primary/[0.05] to-transparent relative overflow-hidden group hover:scale-[1.02] transition-transform">
                <div class="flex items-center gap-6 relative z-10">
                    <div class="w-16 h-16 rounded-[2rem] bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shadow-2xl shadow-primary/10 group-hover:rotate-12 transition-transform duration-500">
                        <i class="ph-bold ph-megaphone text-3xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Active Nodes</p>
                        <p class="text-3xl font-black tracking-tighter mt-1" id="ads-count-badge">00</p>
                    </div>
                </div>
                <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors"></div>
            </div>
            
            <div class="stat-card border-blue-500/20 bg-gradient-to-br from-blue-500/[0.05] to-transparent relative overflow-hidden group hover:scale-[1.02] transition-transform">
                <div class="flex items-center gap-6 relative z-10">
                    <div class="w-16 h-16 rounded-[2rem] bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20 shadow-2xl shadow-blue-500/10 group-hover:-rotate-12 transition-transform duration-500">
                        <i class="ph-bold ph-currency-circle-dollar text-3xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Financial Flux</p>
                        <p class="text-3xl font-black tracking-tighter mt-1" id="ads-spend-badge">$0,000</p>
                    </div>
                </div>
                <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/10 transition-colors"></div>
            </div>

            <div class="stat-card border-orange-500/20 bg-gradient-to-br from-orange-500/[0.05] to-transparent relative overflow-hidden group hover:scale-[1.02] transition-transform">
                <div class="flex items-center gap-6 relative z-10">
                    <div class="w-16 h-16 rounded-[2rem] bg-orange-500/10 flex items-center justify-center text-orange-400 border border-orange-500/20 shadow-2xl shadow-orange-500/10 group-hover:scale-110 transition-transform duration-500">
                        <i class="ph-bold ph-chart-line-up text-3xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Avg CTR Density</p>
                        <p class="text-3xl font-black tracking-tighter mt-1" id="ads-roi-badge">0.00%</p>
                    </div>
                </div>
                <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-orange-500/5 rounded-full blur-3xl group-hover:bg-orange-500/10 transition-colors"></div>
            </div>
        </div>

        <div class="stat-card !p-0 overflow-hidden bg-white/[0.02] border-white/5 shadow-2xl">
            <div class="p-8 border-b border-white/5 bg-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h3 class="text-xl font-black uppercase tracking-tight">Stream <span class="gradient-text">Orchestration</span></h3>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mt-1 flex items-center gap-3">
                        <span class="flex h-2 w-2 relative">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                        </span>
                        Managed Global Marketing Ingress
                    </p>
                </div>
                <div class="flex gap-4 w-full md:w-auto">
                    <div class="relative flex-grow md:flex-none">
                        <i class="ph-bold ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-primary opacity-60"></i>
                        <input type="text" placeholder="Filter Marketing Nodes..." 
                               class="w-full md:w-64 rounded-xl pl-12 pr-4 py-3 text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                               style="background-color: var(--glass-bg); border: 1px solid var(--border-color); color: var(--text-main);">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5" style="background-color: var(--glass-bg);">
                            <th class="py-6 px-8">Identity / Protocol</th>
                            <th class="py-6 px-8">Deployment State</th>
                            <th class="py-6 px-8 text-center">Fuel (Budget)</th>
                            <th class="py-6 px-8 text-center">CTR Density</th>
                            <th class="py-6 px-8 text-right">Synchronization</th>
                        </tr>
                    </thead>
                    <tbody id="ads-table-body" class="divide-y divide-white/[0.03]">
                        <!-- Campaigns Synchronized by JS -->
                        <tr><td colspan="5" class="p-32 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
                                <p class="font-black text-[10px] uppercase tracking-[0.3em] opacity-40">Authenticating Stream Ledger...</p>
                            </div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Performance -->
    <div id="tab-content-performance" class="tab-content hidden space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="stat-card border-indigo-500/20 bg-white/[0.02]">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-10 opacity-60 flex items-center justify-between text-indigo-400">
                    ROI Probability Map
                    <span class="px-2 py-1 rounded bg-indigo-500/10 border border-indigo-500/20 text-[8px]">Live Telementry</span>
                </h3>
                <div class="aspect-[21/9] flex items-end justify-between px-10 pb-10 bg-indigo-500/[0.03] border border-indigo-500/10 rounded-[2.5rem] relative overflow-hidden group/chart group">
                     <div class="absolute inset-0 bg-gradient-to-t from-indigo-500/[0.05] to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-1000"></div>
                     <?php for($i=0; $i<18; $i++): ?>
                     <div class="w-3 md:w-5 bg-gradient-to-t from-indigo-500 to-indigo-300 rounded-t-lg transition-all duration-1000 origin-bottom group-hover:scale-y-[1.1] relative z-10" 
                          style="height: <?= rand(20, 90) ?>%; box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);">
                          <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[8px] font-bold opacity-0 group-hover/chart:opacity-60 transition-opacity"><?= rand(40, 99) ?>%</div>
                     </div>
                     <?php endfor; ?>
                </div>
                <div class="grid grid-cols-3 gap-8 mt-12 bg-white/[0.03] p-8 rounded-[2rem] border border-white/5">
                     <div class="text-center">
                          <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2 leading-tight">Performance Rank</p>
                          <p class="text-3xl font-black text-indigo-400 tracking-tighter">S-Tier</p>
                     </div>
                     <div class="text-center border-x border-white/10">
                          <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2 leading-tight">Daily Mean CPM</p>
                          <p class="text-3xl font-black tracking-tighter" style="color: var(--text-main);">$4.85</p>
                     </div>
                     <div class="text-center">
                          <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2 leading-tight">ROAS Coefficient</p>
                          <p class="text-3xl font-black text-emerald-400 tracking-tighter">14.2x</p>
                     </div>
                </div>
            </div>

            <div class="stat-card border-white/5 bg-white/[0.02] flex flex-col pt-10">
                 <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-12 opacity-60">Asset Distribution Efficiency</h3>
                 <div class="space-y-10 px-4">
                      <div class="space-y-4">
                           <div class="flex justify-between items-center px-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary border border-primary/20"><i class="ph-bold ph-image text-sm"></i></div>
                                    <span class="text-[11px] font-black uppercase tracking-widest opacity-60">Static Visual Node</span>
                                </div>
                                <span class="text-primary font-black text-xs tracking-tighter">88.4%</span>
                           </div>
                           <div class="w-full bg-white/5 h-1.5 rounded-full overflow-hidden p-[1px]">
                                <div class="bg-primary h-full w-[88.4%] rounded-full shadow-[0_0_15px_rgba(124,106,255,0.5)]"></div>
                           </div>
                      </div>
                      <div class="space-y-4">
                           <div class="flex justify-between items-center px-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400 border border-purple-500/20"><i class="ph-bold ph-video-camera text-sm"></i></div>
                                    <span class="text-[11px] font-black uppercase tracking-widest opacity-60">Dynamic Rich Media</span>
                                </div>
                                <span class="text-purple-400 font-black text-xs tracking-tighter">62.1%</span>
                           </div>
                           <div class="w-full bg-white/5 h-1.5 rounded-full overflow-hidden p-[1px]">
                                <div class="bg-purple-500 h-full w-[62.1%] rounded-full shadow-[0_0_15px_rgba(168,85,247,0.5)]"></div>
                           </div>
                      </div>
                      <div class="space-y-4">
                           <div class="flex justify-between items-center px-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-orange-500/10 flex items-center justify-center text-orange-400 border border-orange-500/20"><i class="ph-bold ph-funnel text-sm"></i></div>
                                    <span class="text-[11px] font-black uppercase tracking-widest opacity-60">Lead Conversion Funnels</span>
                                </div>
                                <span class="text-orange-400 font-black text-xs tracking-tighter">44.8%</span>
                           </div>
                           <div class="w-full bg-white/5 h-1.5 rounded-full overflow-hidden p-[1px]">
                                <div class="bg-orange-500 h-full w-[44.8%] rounded-full shadow-[0_0_15px_rgba(249,115,22,0.5)]"></div>
                           </div>
                      </div>
                 </div>
                 <div class="mt-12 p-6 bg-white/[0.03] rounded-3xl border border-dashed border-white/10 text-center">
                    <p class="text-[9px] font-bold opacity-30 uppercase tracking-[0.2em]">Telemetry calibrated with global marketing nodes</p>
                 </div>
            </div>
        </div>
    </div>
</div>
