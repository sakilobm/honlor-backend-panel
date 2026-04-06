<?php use Aether\Session; ?>
<!-- Section: Workspace Intelligence (Dashboard) -->
<div id="section-dashboard" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Workspace <span class="gradient-text">Intelligence</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Real-time performance across the Aether ecosystem.</p>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <button class="btn-secondary !rounded-2xl flex-1 md:flex-none justify-center" onclick="AdminApp.openModal('custom-range-modal')">
                <i class="ph-bold ph-calendar-check text-lg"></i>
                Timeline
            </button>
            <button class="btn-primary !rounded-2xl flex-1 md:flex-none justify-center shadow-xl shadow-primary/20" onclick="AdminApp.generateInsights()">
                <i class="ph-bold ph-magic-wand text-lg"></i>
                Insights
            </button>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-8 border-b border-white/5" id="dashboard-tabs">
        <button class="tab-btn active" data-tab="intelligence" onclick="AdminApp.switchTab('dashboard', 'intelligence')">
            Strategic Metrics
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn" data-tab="feed" onclick="AdminApp.switchTab('dashboard', 'feed')">
            Neural Feed
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn" data-tab="agents" onclick="AdminApp.switchTab('dashboard', 'agents')">
            Agent Status
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: Intelligence -->
    <div id="tab-content-intelligence" class="tab-content space-y-8 animate-in fade-in duration-700">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="stat-card group relative overflow-hidden">
                <div class="flex justify-between items-start mb-8">
                    <div class="w-12 h-12 bg-primary/20 rounded-[1.25rem] flex items-center justify-center text-primary border border-primary/20 transition-all group-hover:bg-primary group-hover:text-white">
                        <i class="ph-bold ph-users text-2xl"></i>
                    </div>
                    <span class="badge-success shadow-lg shadow-green-500/10">+12.4%</span>
                </div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Verified Agents</p>
                <h4 class="text-4xl font-black tracking-tighter" id="stat-total-users">...</h4>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary/30 to-transparent"></div>
            </div>

            <div class="stat-card group relative overflow-hidden">
                <div class="flex justify-between items-start mb-8">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-[1.25rem] flex items-center justify-center text-blue-400 border border-blue-400/20 transition-all group-hover:bg-blue-400 group-hover:text-white">
                        <i class="ph-bold ph-messenger-logo text-2xl"></i>
                    </div>
                    <span class="badge-neutral border-none uppercase tracking-widest">+8% today</span>
                </div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Telemetry Stream</p>
                <h4 class="text-4xl font-black tracking-tighter" id="stat-today-messages">...</h4>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-400/30 to-transparent"></div>
            </div>

            <div class="stat-card group relative overflow-hidden">
                <div class="flex justify-between items-start mb-8">
                    <div class="w-12 h-12 bg-orange-500/20 rounded-[1.25rem] flex items-center justify-center text-orange-400 border border-orange-400/20 transition-all group-hover:bg-orange-400 group-hover:text-white">
                        <i class="ph-bold ph-megaphone text-2xl"></i>
                    </div>
                    <span class="badge-warning">Priority</span>
                </div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Active Pipelines</p>
                <h4 class="text-4xl font-black tracking-tighter" id="stat-active-ads">...</h4>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-orange-400/30 to-transparent"></div>
            </div>

            <div class="stat-card group relative overflow-hidden">
                <div class="flex justify-between items-start mb-8">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-[1.25rem] flex items-center justify-center text-purple-400 border border-purple-400/20 transition-all group-hover:bg-purple-400 group-hover:text-white">
                        <i class="ph-bold ph-globe text-2xl"></i>
                    </div>
                    <span class="badge-success">Operational</span>
                </div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Edge Clusters</p>
                <h4 class="text-4xl font-black tracking-tighter" id="stat-active-channels">...</h4>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-purple-400/30 to-transparent"></div>
            </div>
        </div>

        <!-- Main Chart -->
        <div class="glass-card !p-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h3 class="text-xl font-black uppercase tracking-tight">Growth <span class="gradient-text">Stream</span></h3>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-50">Comparative Neural Analytics</p>
                </div>
                <div class="p-1 rounded-2xl flex border" style="background-color: var(--glass-bg); border-color: var(--border-color);">
                    <button class="px-6 py-2 bg-primary text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg">Real-time</button>
                    <button class="px-6 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl opacity-50 hover:opacity-100 transition-all">Historical</button>
                </div>
            </div>
            <div class="h-80 w-full">
                <canvas id="growthChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tab Content: Feed -->
    <div id="tab-content-feed" class="tab-content hidden space-y-8 animate-in fade-in duration-700">
        <div class="stat-card !p-0 overflow-hidden">
            <div class="p-8 border-b border-white/5 bg-white/5">
                <h3 class="text-xl font-black uppercase tracking-tight">Neural <span class="gradient-text">Activity Feed</span></h3>
                <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mt-1">Live platform events and synchronization logs</p>
            </div>
            <div id="recent-activity-list" class="p-8 space-y-6 max-h-[600px] overflow-y-auto custom-scrollbar">
                <p class="text-center text-gray-500 py-20 animate-pulse font-black text-[10px] uppercase tracking-widest">Synchronizing Feed Packets...</p>
            </div>
        </div>
    </div>

    <!-- Tab Content: Agents -->
    <div id="tab-content-agents" class="tab-content hidden space-y-8 animate-in fade-in duration-700">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6" id="agent-status-grid">
            <!-- Example Agent 1 -->
            <div class="stat-card !p-6 text-center group transition-all hover:scale-[1.05] cursor-pointer">
                <div class="relative inline-block mb-4">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Alex" class="w-20 h-20 mx-auto rounded-[2rem] bg-primary/10 border border-primary/20 group-hover:bg-primary group-hover:rotate-6 transition-all" alt="Avatar">
                    <span class="absolute bottom-1 right-1 w-5 h-5 bg-green-500 border-4 border-[var(--surface)] rounded-full shadow-[0_0_10px_#22c55e]"></span>
                </div>
                <p class="font-black text-xs uppercase tracking-tight">Alex Rivera</p>
                <p class="text-[9px] font-black uppercase tracking-widest text-primary mt-1 leading-none shadow-primary/20">Super Admin</p>
                <div class="badge-success mt-4 inline-block">Online</div>
            </div>
            
            <!-- JavaScript will inject dynamic agents if logic exists, otherwise these are placeholders -->
            <div class="stat-card !p-6 text-center group transition-all hover:scale-[1.05] cursor-pointer opacity-40 grayscale group-hover:grayscale-0 group-hover:opacity-100 transition-all">
                <div class="relative inline-block mb-4">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah" class="w-20 h-20 mx-auto rounded-[2rem] bg-white/10 border border-white/20 transition-all" alt="Avatar">
                    <span class="absolute bottom-1 right-1 w-5 h-5 bg-gray-500 border-4 border-[var(--surface)] rounded-full"></span>
                </div>
                <p class="font-black text-xs uppercase tracking-tight">Sarah Jenks</p>
                <p class="text-[9px] font-bold uppercase tracking-widest opacity-60 mt-1 leading-none">Moderator</p>
                <div class="badge-neutral mt-4 inline-block uppercase text-[9px]">2h ago</div>
            </div>
        </div>
    </div>
</div>
