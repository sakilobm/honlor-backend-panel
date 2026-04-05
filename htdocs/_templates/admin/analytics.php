<?php use Aether\Session; ?>
<!-- Section: Ecosystem Analytics -->
<div id="section-analytics" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight mb-2">Ecosystem Analytics</h2>
            <p class="font-medium" style="color: var(--text-muted);">Deep-dive into user retention, engagement, and conversion funnels.</p>
        </div>
        <div class="flex gap-3">
            <button class="btn-secondary">
                <i class="ph-bold ph-calendar"></i>
                Custom Range
            </button>
            <button class="btn-primary">
                <i class="ph-bold ph-chart-line-up"></i>
                Generate Insights
            </button>
        </div>
    </div>

    <!-- Analytics Grids -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="stat-card ring-1 ring-primary/20">
            <h3 class="font-bold text-lg mb-6">Retention Cohorts</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center text-xs font-bold uppercase tracking-widest text-gray-400">
                    <span>Week 1</span>
                    <span style="color: var(--text-main);">92%</span>
                </div>
                <div class="w-full bg-border-dark h-3 rounded-full overflow-hidden">
                    <div class="bg-primary h-full w-[92%]"></div>
                </div>
                
                <div class="flex justify-between items-center text-xs font-bold uppercase tracking-widest text-gray-400">
                    <span>Week 2</span>
                    <span style="color: var(--text-main);">78%</span>
                </div>
                <div class="w-full bg-border-dark h-3 rounded-full overflow-hidden">
                    <div class="bg-primary/80 h-full w-[78%]"></div>
                </div>

                <div class="flex justify-between items-center text-xs font-bold uppercase tracking-widest text-gray-400">
                    <span>Week 4</span>
                    <span style="color: var(--text-main);">64%</span>
                </div>
                <div class="w-full bg-border-dark h-3 rounded-full overflow-hidden">
                    <div class="bg-primary/60 h-full w-[64%]"></div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <h3 class="font-bold text-lg mb-6">Regional Distribution</h3>
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-2 h-2 rounded-full bg-blue-400 shadow-[0_0_8px_rgba(96,165,250,0.5)]"></div>
                    <div class="flex-grow">
                         <div class="flex justify-between text-sm font-bold mb-1"><span>North America</span><span>42%</span></div>
                         <div class="w-full h-1.5 rounded-full" style="background-color: var(--glass-bg);"><div class="bg-blue-400 h-full w-[42%] rounded-full"></div></div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-2 h-2 rounded-full bg-purple-400"></div>
                    <div class="flex-grow">
                         <div class="flex justify-between text-sm font-bold mb-1"><span>Europe</span><span>28%</span></div>
                         <div class="w-full h-1.5 rounded-full" style="background-color: var(--glass-bg);"><div class="bg-purple-400 h-full w-[28%] rounded-full"></div></div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-2 h-2 rounded-full bg-orange-400"></div>
                    <div class="flex-grow">
                         <div class="flex justify-between text-sm font-bold mb-1"><span>Asia Pacific</span><span>15%</span></div>
                         <div class="w-full h-1.5 rounded-full" style="background-color: var(--glass-bg);"><div class="bg-orange-400 h-full w-[15%] rounded-full"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Engagement Heatmap Placeholder -->
    <div class="stat-card">
        <h3 class="font-bold text-lg mb-8 text-center uppercase tracking-widest" style="color: var(--text-muted);">Global Engagement Heatmap</h3>
        <div class="grid grid-cols-12 gap-3 aspect-video md:aspect-[21/9]">
            <?php for($i=0; $i<12*4; $i++): ?>
                <div class="rounded-lg transition-all hover:scale-110 cursor-help" 
                    style="background-color: var(--glass-bg); opacity: <?= rand(2, 10) / 10 ?>; border: 1px solid var(--border-color);">
                </div>
            <?php endfor; ?>
        </div>
        <p class="text-[11px] font-bold text-center mt-8 text-gray-500 uppercase tracking-widest">Real-time interaction density across 24 timezone clusters</p>
    </div>
</div>
