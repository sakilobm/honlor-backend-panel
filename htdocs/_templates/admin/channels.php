<?php use Aether\Session; ?>
<!-- Section: Channels (Network Graph) -->
<div id="section-channels" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight mb-2">Network Architecture</h2>
            <p class="font-medium" style="color: var(--text-muted);">Manage channel hierarchies and message propagation nodes.</p>
        </div>
        <div class="flex gap-3">
            <button class="btn-primary">
                <i class="ph-bold ph-circles-three-plus"></i>
                Create Channel
            </button>
        </div>
    </div>

    <!-- Cluster Map Placeholder -->
    <div class="stat-card !p-0 min-h-[500px] flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-30 pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-primary/20 blur-3xl animate-pulse rounded-full"></div>
            <div class="absolute bottom-1/3 right-1/4 w-64 h-64 bg-blue-500/10 blur-[100px] rounded-full"></div>
        </div>
        <div class="text-center relative z-10 px-8">
            <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center text-primary mx-auto mb-6 border border-primary/20 shadow-[0_0_40px_rgba(124,106,255,0.2)] animate-bounce font-bold tracking-widest uppercase">
                <i class="ph ph-graph text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold mb-3 tracking-tight">Interactive Cluster Map</h3>
            <p class="max-w-md mx-auto leading-relaxed mb-8" style="color: var(--text-muted);">Visualize live message propagation across global nodes. Select a node to adjust shard allocation or view localized logs.</p>
            <div class="flex justify-center gap-4">
                <button class="btn-secondary !text-xs !py-1.5 !px-4">Show Heatmap</button>
                <button class="btn-secondary !text-xs !py-1.5 !px-4">Shard Overlay</button>
            </div>
        </div>
    </div>

    <!-- Active Propagation Table -->
    <div class="stat-card">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-xl font-bold">Priority Channels</h3>
            <span class="badge-neutral border-primary/20 text-primary">Live Optimization</span>
        </div>
        <div class="space-y-6">
            <div class="flex items-center justify-between p-5 rounded-[2rem] border hover:bg-white/5 transition-all cursor-pointer" style="border-color: var(--border-color);">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary font-bold text-xl">#hq</div>
                    <div>
                        <p class="font-bold text-base">Global HQ</p>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-gray-500">1.2M ACTIVE USERS • 14ms LATENCY</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="badge-success">Optimal</span>
                </div>
            </div>
            <div class="flex items-center justify-between p-5 rounded-[2rem] border hover:bg-white/5 transition-all cursor-pointer" style="border-color: var(--border-color);">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400 font-bold text-xl">#mkt</div>
                    <div>
                        <p class="font-bold text-base">Marketing Alpha</p>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-gray-500">45k ACTIVE USERS • 82ms LATENCY</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="badge-warning">Scaling</span>
                </div>
            </div>
        </div>
    </div>
</div>
