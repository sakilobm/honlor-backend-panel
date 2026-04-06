<?php use Aether\Session; ?>
<!-- Section: Ecosystem Intelligence (Analytics) -->
<div id="section-analytics" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Ecosystem <span class="gradient-text">Intelligence</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Deep-dive into global user retention and regional node density.</p>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <button class="btn-secondary !rounded-2xl flex-1 md:flex-none justify-center" onclick="AdminApp.openModal('custom-range-modal')">
                <i class="ph-bold ph-calendar text-lg"></i>
                Timeline
            </button>
            <button class="btn-primary !rounded-2xl flex-1 md:flex-none justify-center" onclick="AdminApp.generateInsights()">
                <i class="ph-bold ph-magic-wand text-lg"></i>
                Generate Insights
            </button>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-8 border-b border-white/5" id="analytics-tabs">
        <button class="tab-btn active" data-tab="metrics" onclick="AdminApp.switchTab('analytics', 'metrics')">
            User Dynamics
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn" data-tab="geography" onclick="AdminApp.switchTab('analytics', 'geography')">
            Regional Density
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: Metrics -->
    <div id="tab-content-metrics" class="tab-content space-y-8 animate-in fade-in duration-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="stat-card group relative overflow-hidden border-primary/20 bg-gradient-to-br from-primary/10 to-transparent">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Retention Cohorts</h3>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-gray-500">
                            <span>Week 1 (Onboarding)</span>
                            <span class="text-primary">92% High</span>
                        </div>
                        <div class="w-full bg-white/5 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-primary h-full w-[92%] shadow-[0_0_8px_#7c6aff] group-hover:scale-x-105 transition-all"></div>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-gray-500">
                            <span>Week 2 (Engagement)</span>
                            <span class="text-white">78% Optimal</span>
                        </div>
                        <div class="w-full bg-white/5 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-primary/80 h-full w-[78%]"></div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-gray-500">
                            <span>Week 4 (Maturity)</span>
                            <span class="text-white">64% Stable</span>
                        </div>
                        <div class="w-full bg-white/5 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-primary/60 h-full w-[64%]"></div>
                        </div>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary/30 to-transparent"></div>
            </div>

            <div class="stat-card">
                 <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">User Churn Velocity</h3>
                 <div class="flex items-center justify-center p-10">
                      <div class="relative w-40 h-40 border-4 border-white/5 rounded-full flex items-center justify-center">
                           <div class="absolute inset-0 border-4 border-primary rounded-full border-t-transparent animate-spin"></div>
                           <div class="text-center">
                                <p class="text-3xl font-black tracking-tighter">4.2%</p>
                                <p class="text-[9px] font-black uppercase tracking-widest opacity-40">Monthly</p>
                           </div>
                      </div>
                 </div>
                 <p class="text-[10px] font-bold text-center opacity-40 leading-relaxed uppercase tracking-widest px-10">Churn rate decreased by 0.8% compared to the previous epoch.</p>
            </div>
        </div>

        <!-- Placeholder Metrics Card -->
        <div class="p-8 rounded-[2.5rem] bg-indigo-500/5 border border-indigo-500/10 flex items-center gap-6">
            <div class="w-14 h-14 rounded-2xl bg-indigo-500/20 flex items-center justify-center text-indigo-400 border border-indigo-500/20">
                <i class="ph-bold ph-strategy text-2xl"></i>
            </div>
            <div>
                <h4 class="font-black text-sm uppercase tracking-tight">Predictive Retention Insights</h4>
                <p class="text-[11px] font-bold opacity-40 leading-relaxed max-w-2xl mt-1">Based on current cluster dynamics, user engagement is projected to stabilize at 68% by the end of the fiscal quarter. Node latency optimization is the primary growth driver.</p>
            </div>
        </div>
    </div>

    <!-- Tab Content: Geography -->
    <div id="tab-content-geography" class="tab-content hidden space-y-8 animate-in fade-in duration-700">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-1 space-y-6">
                <div class="stat-card">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-6 opacity-60">Top Regions</p>
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400 border border-blue-400/20">
                                <i class="ph ph-north-star font-bold text-sm"></i>
                            </div>
                            <div class="flex-grow">
                                <div class="flex justify-between text-xs font-black mb-1.5 uppercase tracking-tighter"><span>North America</span><span>42%</span></div>
                                <div class="w-full h-1 bg-white/5 rounded-full overflow-hidden"><div class="bg-blue-400 h-full w-[42%]"></div></div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-lg bg-purple-500/20 flex items-center justify-center text-purple-400 border border-purple-400/20">
                                <i class="ph ph-target font-bold text-sm"></i>
                            </div>
                            <div class="flex-grow">
                                <div class="flex justify-between text-xs font-black mb-1.5 uppercase tracking-tighter"><span>Europe</span><span>28%</span></div>
                                <div class="w-full h-1 bg-white/5 rounded-full overflow-hidden"><div class="bg-purple-400 h-full w-[28%]"></div></div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-lg bg-orange-500/20 flex items-center justify-center text-orange-400 border border-orange-500/20">
                                <i class="ph ph-compass font-bold text-sm"></i>
                            </div>
                            <div class="flex-grow">
                                <div class="flex justify-between text-xs font-black mb-1.5 uppercase tracking-tighter"><span>Asia Pacific</span><span>15%</span></div>
                                <div class="w-full h-1 bg-white/5 rounded-full overflow-hidden"><div class="bg-orange-400 h-full w-[15%]"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Heatmap Placeholder -->
            <div class="lg:col-span-3 stat-card group relative">
                <h3 class="text-xl font-black uppercase tracking-tight mb-10 text-center">Global Interaction <span class="gradient-text">Density</span></h3>
                <div class="grid grid-cols-12 gap-3 aspect-video md:aspect-[21/10]">
                    <?php for($i=0; $i<12*5; $i++): ?>
                        <div class="rounded-xl bg-white/5 border border-white/5 transition-all hover:bg-primary/20 hover:scale-[1.1] hover:shadow-[0_0_15px_#7c6aff] group/box cursor-help relative" 
                             style="opacity: <?= rand(3, 10) / 10 ?>;">
                             <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-surface border border-white/10 p-2 rounded-lg text-[8px] font-black uppercase tracking-widest opacity-0 group-hover/box:opacity-100 whitespace-nowrap pointer-events-none transition-all">
                                Node Cluster 0<?= rand(1,9) ?>: Active
                             </div>
                        </div>
                    <?php endfor; ?>
                </div>
                <p class="text-[10px] font-black text-center mt-10 text-gray-500 uppercase tracking-widest">Real-time interaction density packets across 24 timezone clusters</p>
                <!-- Decorative pulse -->
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-primary/10 rounded-full blur-[60px] animate-pulse"></div>
            </div>
        </div>
    </div>
</div>
