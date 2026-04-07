<?php use Aether\Session; ?>
<!-- Section: Ads Pipeline (Marketing) -->
<div id="section-ads" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Ads <span class="gradient-text">Pipeline</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Coordinate global marketing streams and maximize user acquisition.</p>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <button class="btn-primary !rounded-2xl flex-1 md:flex-none justify-center shadow-xl shadow-primary/20" onclick="openModal('create-ad-modal')">
                <i class="ph-bold ph-plus text-lg"></i>
                Create Stream
            </button>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-8 border-b border-white/5" id="ads-tabs">
        <button class="tab-btn active" data-tab="campaigns" onclick="AdminApp.switchTab('ads', 'campaigns')">
            Active Stream
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn" data-tab="performance" onclick="AdminApp.switchTab('ads', 'performance')">
            Deep ROI Analytics
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: Campaigns -->
    <div id="tab-content-campaigns" class="tab-content space-y-8 animate-in fade-in duration-700">
        <!-- Dashboard Summary Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="stat-card group hover:scale-[1.02] transition-all relative overflow-hidden">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-primary/20 flex items-center justify-center text-primary border border-primary/20">
                        <i class="ph-bold ph-megaphone text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-60">Active Nodes</p>
                        <p class="text-3xl font-black tracking-tighter" id="ads-count-badge">...</p>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary/30 to-transparent"></div>
            </div>
            
            <div class="stat-card group hover:scale-[1.02] transition-all relative overflow-hidden">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/20 flex items-center justify-center text-blue-400 border border-blue-400/20">
                        <i class="ph-bold ph-currency-dollar text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-60">Daily Expenditure</p>
                        <p class="text-3xl font-black tracking-tighter">$14,248</p>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-400/30 to-transparent"></div>
            </div>

            <div class="stat-card group hover:scale-[1.02] transition-all relative overflow-hidden">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-orange-500/20 flex items-center justify-center text-orange-400 border border-orange-500/20">
                        <i class="ph-bold ph-chart-line-up text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-60">Avg Persistence</p>
                        <p class="text-3xl font-black tracking-tighter">4.82%</p>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-orange-400/30 to-transparent"></div>
            </div>
        </div>

        <div class="stat-card !p-0 overflow-hidden">
            <div class="p-8 border-b border-white/5 bg-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h3 class="text-xl font-black uppercase tracking-tight">Campaign <span class="gradient-text">Orchestration</span></h3>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mt-1">Real-time status across 24 node clusters</p>
                </div>
                <div class="flex gap-4 w-full md:w-auto">
                    <div class="relative flex-grow md:flex-none">
                        <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="text" placeholder="Search streams..." 
                               class="w-full rounded-xl pl-10 pr-4 py-2 text-xs font-bold uppercase tracking-widest focus:border-primary/50 outline-none transition-all"
                               style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5" style="background-color: var(--glass-bg);">
                            <th class="py-6 px-8">Audit ID / Name</th>
                            <th class="py-6 px-8">Deployment State</th>
                            <th class="py-6 px-8">Daily Budget</th>
                            <th class="py-6 px-8 text-center">CTR Density</th>
                            <th class="py-6 px-8 text-right">Synchronization</th>
                        </tr>
                    </thead>
                    <tbody id="ads-table-body" class="divide-y divide-white/5">
                        <!-- Campaigns Dynamically Synced by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Performance -->
    <div id="tab-content-performance" class="tab-content hidden space-y-8 animate-in fade-in duration-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="stat-card">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60 text-primary">ROI Probability Map</h3>
                <div class="aspect-[21/9] flex items-center justify-center p-10 bg-primary/5 border border-primary/10 rounded-3xl relative overflow-hidden group/chart">
                     <span class="relative z-10 text-[10px] font-black uppercase tracking-widest opacity-60 animate-pulse">Awaiting ROI Packets...</span>
                     <!-- Background dynamic bars -->
                     <div class="absolute bottom-0 left-0 w-full flex items-end justify-between px-10 gap-2 h-1/2 opacity-20">
                          <?php for($i=0; $i<20; $i++): ?>
                          <div class="w-full bg-primary rounded-t-lg transition-all group-hover/chart:h-full" style="height: <?= rand(10, 100) ?>%"></div>
                          <?php endfor; ?>
                     </div>
                </div>
                <div class="grid grid-cols-3 gap-6 mt-10">
                     <div class="text-center">
                          <p class="text-xs font-black uppercase tracking-widest opacity-40 mb-1 leading-tight">Return Rank</p>
                          <p class="text-2xl font-black text-primary tracking-tighter">A+</p>
                     </div>
                     <div class="text-center">
                          <p class="text-xs font-black uppercase tracking-widest opacity-40 mb-1 leading-tight">CPM Mean</p>
                          <p class="text-2xl font-black tracking-tighter" style="color: var(--text-main);">$4.20</p>
                     </div>
                     <div class="text-center">
                          <p class="text-xs font-black uppercase tracking-widest opacity-40 mb-1 leading-tight">ROAS Avg</p>
                          <p class="text-2xl font-black text-green-400 tracking-tighter">12.4x</p>
                     </div>
                </div>
            </div>

            <div class="stat-card">
                 <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Asset Performance Distribution</h3>
                 <div class="space-y-8 mt-5">
                      <div class="space-y-2">
                           <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest opacity-60">
                                <span>Visual Display (Static)</span>
                                <span class="text-primary font-bold">82% Perf</span>
                           </div>
                           <div class="w-full bg-white/5 h-1 rounded-full overflow-hidden">
                                <div class="bg-primary h-full w-[82%] shadow-[0_0_8px_#7c6aff]"></div>
                           </div>
                      </div>
                      <div class="space-y-2">
                           <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest opacity-60">
                                <span>Interactive Modules (Rich)</span>
                                <span class="text-purple-400 font-bold">64% Perf</span>
                           </div>
                           <div class="w-full bg-white/5 h-1 rounded-full overflow-hidden">
                                <div class="bg-purple-500 h-full w-[64%] shadow-[0_0_8px_#a855f7]"></div>
                           </div>
                      </div>
                      <div class="space-y-2">
                           <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest opacity-60">
                                <span>External Lead Funnels</span>
                                <span class="text-orange-400 font-bold">48% Perf</span>
                           </div>
                           <div class="w-full bg-white/5 h-1 rounded-full overflow-hidden">
                                <div class="bg-orange-500 h-full w-[48%] shadow-[0_0_8px_#f97316]"></div>
                           </div>
                      </div>
                 </div>
            </div>
        </div>
    </div>
</div>
