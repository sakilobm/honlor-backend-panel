<?php use Aether\Session; ?>
<!-- Section: Overview -->
<div id="section-overview" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight mb-2">Workspace Overview</h2>
            <p class="font-medium" style="color: var(--text-muted);">Real-time performance across the Honlor ecosystem.</p>
        </div>
        <div class="flex gap-3">
            <button class="btn-secondary">
                <i class="ph-bold ph-download-simple"></i>
                Download Report
            </button>
            <button class="btn-primary" onclick="openModal('create-ad-modal')">
                <i class="ph-bold ph-plus"></i>
                New Campaign
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Users Card -->
        <div class="stat-card group">
            <div class="flex justify-between items-start mb-6">
                <div class="p-3 bg-primary/10 rounded-2xl text-primary transition-all group-hover:scale-110">
                    <i class="ph-bold ph-users text-2xl"></i>
                </div>
                <span class="badge-success" id="users-growth">+12.4%</span>
            </div>
            <p class="text-sm font-semibold mb-1" style="color: var(--text-muted);">Total Active Users</p>
            <h4 class="text-4xl font-bold tracking-tight" id="stat-total-users">450,230</h4>
            <div class="w-full h-1.5 mt-6 rounded-full overflow-hidden" style="background-color: var(--border-color);">
                <div class="bg-primary h-full rounded-full transition-all duration-1000" style="width: 78%;"></div>
            </div>
        </div>

        <!-- Messages Card -->
        <div class="stat-card group">
            <div class="flex justify-between items-start mb-6">
                <div class="p-3 bg-blue-500/10 rounded-2xl text-blue-400 transition-all group-hover:scale-110">
                    <i class="ph-bold ph-messenger-logo text-2xl"></i>
                </div>
                <span class="badge-neutral" id="messages-today">+8% today</span>
            </div>
            <p class="text-sm font-semibold mb-1" style="color: var(--text-muted);">Messages Today</p>
            <h4 class="text-4xl font-bold tracking-tight" id="stat-today-messages">1,248,502</h4>
            <div class="w-full h-1.5 mt-6 rounded-full overflow-hidden" style="background-color: var(--border-color);">
                <div class="bg-blue-400 h-full rounded-full transition-all duration-1000" style="width: 62%;"></div>
            </div>
        </div>

        <!-- Campaigns Card -->
        <div class="stat-card group">
            <div class="flex justify-between items-start mb-6">
                <div class="p-3 bg-orange-500/10 rounded-2xl text-orange-400 transition-all group-hover:scale-110">
                    <i class="ph-bold ph-megaphone text-2xl"></i>
                </div>
                <span class="badge-warning">Action Needed</span>
            </div>
            <p class="text-sm font-semibold mb-1" style="color: var(--text-muted);">Active Campaigns</p>
            <h4 class="text-4xl font-bold tracking-tight" id="stat-active-ads">12</h4>
            <div class="w-full h-1.5 mt-6 rounded-full overflow-hidden" style="background-color: var(--border-color);">
                <div class="bg-orange-400 h-full rounded-full transition-all duration-1000" style="width: 45%;"></div>
            </div>
        </div>

        <!-- Health/Analytics Card -->
        <div class="stat-card group">
            <div class="flex justify-between items-start mb-6">
                <div class="p-3 bg-purple-500/10 rounded-2xl text-purple-400 transition-all group-hover:scale-110">
                    <i class="ph-bold ph-globe text-2xl"></i>
                </div>
                <span class="badge-success">Healthy</span>
            </div>
            <p class="text-sm font-semibold mb-1" style="color: var(--text-muted);">Server Clusters</p>
            <h4 class="text-4xl font-bold tracking-tight">34</h4>
            <div class="w-full h-1.5 mt-6 rounded-full overflow-hidden" style="background-color: var(--border-color);">
                <div class="bg-purple-400 h-full rounded-full transition-all duration-1000" style="width: 92%;"></div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Chart -->
        <div class="lg:col-span-2 stat-card rounded-[2.5rem]">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold">User Growth & Engagement</h3>
                    <p class="text-sm" style="color: var(--text-muted);">Weekly comparative analytics</p>
                </div>
                <div class="p-1 rounded-xl flex" style="background-color: var(--glass-bg);">
                    <button class="px-4 py-1.5 bg-surface-dark text-white text-xs font-bold rounded-lg shadow-sm">Daily</button>
                    <button class="px-4 py-1.5 text-xs font-bold rounded-lg hover:text-white transition-colors" style="color: var(--text-muted);">Weekly</button>
                </div>
            </div>
            <div class="h-80 w-full">
                <canvas id="growthChart"></canvas>
            </div>
        </div>

        <!-- Live Feed Side Card -->
        <div class="stat-card">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-bold flex items-center gap-2">
                    Live Activity
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse shadow-[0_0_8px_rgba(74,222,128,0.5)]"></span>
                </h3>
            </div>
            <div class="space-y-6 overflow-y-auto max-h-[400px] pr-2 custom-scrollbar">
                <div class="flex gap-4 group cursor-pointer">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                        <i class="ph-bold ph-user-plus text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">New user registered via Apple ID</p>
                        <p class="text-[11px] uppercase mt-0.5 font-bold" style="color: var(--text-muted);">2 minutes ago</p>
                    </div>
                </div>
                <div class="flex gap-4 group cursor-pointer">
                    <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center text-green-400 group-hover:scale-110 transition-transform">
                        <i class="ph-bold ph-check-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Ad campaign approved</p>
                        <p class="text-[11px] uppercase mt-0.5 font-bold" style="color: var(--text-muted);">14 minutes ago</p>
                    </div>
                </div>
                <!-- ... other items ... -->
            </div>
            <button class="w-full mt-8 border py-3 rounded-2xl font-bold text-xs transition-all hover:bg-white/5" style="border-color: var(--border-color); color: var(--text-main);">View All Logs</button>
        </div>
    </div>

    <!-- Recent Signups -->
    <div class="stat-card border-none bg-transparent p-0">
        <h3 class="text-xl font-bold mb-6">Recent Members</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <!-- Dynamic placeholders would go here -->
            <div class="border p-4 rounded-3xl text-center group transition-all hover:bg-glass-white" style="border-color: var(--border-color); background-color: var(--surface);">
                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Alex" class="w-16 h-16 mx-auto rounded-2xl mb-3 bg-blue-500/10 p-1 group-hover:scale-105 transition-transform" alt="Avatar">
                <p class="font-bold text-sm truncate">Alex Rivera</p>
                <span class="badge-success mt-2 inline-block">Online</span>
            </div>
            <div class="border p-4 rounded-3xl text-center group transition-all hover:bg-glass-white" style="border-color: var(--border-color); background-color: var(--surface);">
                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah" class="w-16 h-16 mx-auto rounded-2xl mb-3 bg-pink-500/10 p-1 group-hover:scale-105 transition-transform" alt="Avatar">
                <p class="font-bold text-sm truncate">Sarah Jenks</p>
                <span class="badge-neutral mt-2 inline-block">2h ago</span>
            </div>
            <!-- ... more items ... -->
        </div>
    </div>
</div>
