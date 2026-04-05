<?php use Aether\Session; ?>
<!-- Section: Overview -->
<div id="section-overview" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Workspace <span class="gradient-text">Intelligence</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Real-time performance across the Aether ecosystem.</p>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <button class="btn-secondary !rounded-2xl flex-1 md:flex-none justify-center">
                <i class="ph-bold ph-calendar-check text-lg"></i>
                Timeline
            </button>
            <button class="btn-primary !rounded-2xl flex-1 md:flex-none justify-center" onclick="openModal('create-ad-modal')">
                <i class="ph-bold ph-plus-circle text-lg"></i>
                New Operation
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Users Card -->
        <div class="stat-card group relative overflow-hidden">
            <div class="flex justify-between items-start mb-8">
                <div class="w-12 h-12 bg-primary/20 rounded-[1.25rem] flex items-center justify-center text-primary border border-primary/20 transition-all group-hover:bg-primary group-hover:text-white">
                    <i class="ph-bold ph-users text-2xl"></i>
                </div>
                <span class="badge-success shadow-lg shadow-green-500/10">+12.4%</span>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Verified Agents</p>
            <h4 class="text-4xl font-black tracking-tighter" id="stat-total-users">450,230</h4>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary/30 to-transparent"></div>
        </div>

        <!-- Messages Card -->
        <div class="stat-card group relative overflow-hidden">
            <div class="flex justify-between items-start mb-8">
                <div class="w-12 h-12 bg-blue-500/20 rounded-[1.25rem] flex items-center justify-center text-blue-400 border border-blue-400/20 transition-all group-hover:bg-blue-400 group-hover:text-white">
                    <i class="ph-bold ph-messenger-logo text-2xl"></i>
                </div>
                <span class="badge-neutral border-none uppercase tracking-widest">+8% today</span>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Telemetry Stream</p>
            <h4 class="text-4xl font-black tracking-tighter" id="stat-today-messages">1,248,502</h4>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-400/30 to-transparent"></div>
        </div>

        <!-- Campaigns Card -->
        <div class="stat-card group relative overflow-hidden">
            <div class="flex justify-between items-start mb-8">
                <div class="w-12 h-12 bg-orange-500/20 rounded-[1.25rem] flex items-center justify-center text-orange-400 border border-orange-400/20 transition-all group-hover:bg-orange-400 group-hover:text-white">
                    <i class="ph-bold ph-megaphone text-2xl"></i>
                </div>
                <span class="badge-warning">Priority</span>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Active Pipelines</p>
            <h4 class="text-4xl font-black tracking-tighter" id="stat-active-ads">12</h4>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-orange-400/30 to-transparent"></div>
        </div>

        <!-- Health/Analytics Card -->
        <div class="stat-card group relative overflow-hidden">
            <div class="flex justify-between items-start mb-8">
                <div class="w-12 h-12 bg-purple-500/20 rounded-[1.25rem] flex items-center justify-center text-purple-400 border border-purple-400/20 transition-all group-hover:bg-purple-400 group-hover:text-white">
                    <i class="ph-bold ph-globe text-2xl"></i>
                </div>
                <span class="badge-success">Operational</span>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Edge Clusters</p>
            <h4 class="text-4xl font-black tracking-tighter" id="stat-active-channels">08</h4>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-purple-400/30 to-transparent"></div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Chart -->
        <div class="lg:col-span-2 glass-card !p-8">
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

        <!-- Live Feed Side Card -->
        <div class="glass-card !p-8">
            <div class="flex items-center justify-between mb-10">
                <h3 class="text-xl font-black uppercase tracking-tight flex items-center gap-2">
                    Intelligence
                    <span class="w-3 h-3 bg-primary rounded-full animate-pulse shadow-[0_0_15px_#7c6aff]"></span>
                </h3>
            </div>
            <div id="recent-activity-list" class="space-y-6 overflow-y-auto max-h-[400px] pr-2 custom-scrollbar">
                <p class="text-center text-gray-500 py-8 animate-pulse font-black text-[10px] uppercase tracking-widest">Awaiting Packets...</p>
            </div>
            <button class="btn-secondary w-full mt-8 !rounded-2xl !py-4 font-black text-[10px] uppercase tracking-widest">Access All Segments</button>
        </div>
    </div>

    <!-- Recent Signups -->
    <div class="mt-8">
        <h3 class="text-xl font-bold mb-6 tracking-tight">Recent Intelligence <span class="text-xs uppercase font-black text-primary tracking-widest ml-2 opacity-60">Verified Agents</span></h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <!-- Example Agent 1 -->
            <div class="glass-card !p-5 text-center group transition-all hover:scale-[1.05] cursor-pointer">
                <div class="relative inline-block mb-4">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Alex" class="w-16 h-16 mx-auto rounded-3xl bg-primary/10 border border-primary/20 group-hover:bg-primary group-hover:rotate-6 transition-all" alt="Avatar">
                    <span class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 border-4 border-[var(--surface)] rounded-full"></span>
                </div>
                <p class="font-black text-[10px] truncate uppercase tracking-tighter">Alex Rivera</p>
                <div class="badge-success mt-2 inline-block">Online</div>
            </div>
            <!-- Example Agent 2 -->
            <div class="glass-card !p-5 text-center group transition-all hover:scale-[1.05] cursor-pointer">
                <div class="relative inline-block mb-4">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah" class="w-16 h-16 mx-auto rounded-3xl bg-primary/10 border border-primary/20 group-hover:bg-primary group-hover:-rotate-6 transition-all" alt="Avatar">
                    <span class="absolute bottom-0 right-0 w-4 h-4 bg-gray-500 border-4 border-[var(--surface)] rounded-full"></span>
                </div>
                <p class="font-black text-[10px] truncate uppercase tracking-tighter">Sarah Jenks</p>
                <div class="badge-neutral mt-2 inline-block uppercase text-[9px]">2h ago</div>
            </div>
        </div>
    </div>
</div>
