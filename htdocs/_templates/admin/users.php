<?php use Aether\Session; ?>
<!-- Section: Identity Vault (App Users) -->
<div id="section-users" class="section active space-y-8 animate-in fade-in duration-500">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Identity <span class="gradient-text">Vault</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Live Supabase app users — manage accounts, enforce bans, inspect profiles.</p>
        </div>
        <button class="glass-card !bg-[var(--surface-2)] !p-4 !px-8 flex items-center gap-3 border border-[var(--border-color)] hover:border-primary/50 transition-all group" onclick="AdminApp.loadAppUsers(1)">
            <i class="ph-bold ph-arrow-clockwise text-lg group-hover:rotate-180 transition-transform duration-500"></i>
            <span class="text-[10px] font-black uppercase tracking-widest">Refresh Vault</span>
        </button>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div class="stat-card border-primary/20 bg-gradient-to-br from-primary/[0.05] to-transparent group">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-[2rem] bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shadow-xl group-hover:scale-110 transition-transform">
                    <i class="ph-bold ph-users text-2xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40">Total Users</p>
                    <p class="text-2xl font-black tracking-tighter mt-0.5" id="stat-users-total">—</p>
                </div>
            </div>
        </div>
        <div class="stat-card border-emerald-400/20 bg-gradient-to-br from-emerald-400/[0.05] to-transparent group">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-[2rem] bg-emerald-400/10 flex items-center justify-center text-emerald-400 border border-emerald-400/20 shadow-xl group-hover:scale-110 transition-transform">
                    <i class="ph-bold ph-user-check text-2xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40">Onboarded</p>
                    <p class="text-2xl font-black tracking-tighter mt-0.5 text-emerald-400" id="stat-users-onboarded">—</p>
                </div>
            </div>
        </div>
        <div class="stat-card border-blue-400/20 bg-gradient-to-br from-blue-400/[0.05] to-transparent group">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-[2rem] bg-blue-400/10 flex items-center justify-center text-blue-400 border border-blue-400/20 shadow-xl group-hover:scale-110 transition-transform">
                    <i class="ph-bold ph-calendar-plus text-2xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40">New This Week</p>
                    <p class="text-2xl font-black tracking-tighter mt-0.5 text-blue-400" id="stat-users-new-week">—</p>
                </div>
            </div>
        </div>
        <div class="stat-card border-red-500/20 bg-gradient-to-br from-red-500/[0.05] to-transparent group">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-[2rem] bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20 shadow-xl group-hover:rotate-12 transition-transform">
                    <i class="ph-bold ph-prohibit text-2xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40">Banned</p>
                    <p class="text-2xl font-black tracking-tighter mt-0.5 text-red-500" id="stat-users-banned">—</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-8 border-b border-[var(--border-color)]" id="users-tabs">
        <button class="tab-btn active" data-tab="identities" onclick="AdminApp.switchTab('users', 'identities')">
            App Identities
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn" data-tab="security" onclick="AdminApp.switchTab('users', 'security')">
            Security Groups
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn" data-tab="handshakes" onclick="AdminApp.switchTab('users', 'handshakes')">
            <span class="flex items-center gap-2">
                Handshake Hub
                <div id="handshake-count-badge" class="hidden w-4 h-4 bg-primary text-[8px] font-black items-center justify-center rounded-full text-white">0</div>
            </span>
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab: App Identities -->
    <div id="tab-content-identities" class="tab-content space-y-6 animate-in fade-in duration-700">

        <!-- Filter Bar -->
        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">

            <!-- Search + Status chips -->
            <div class="flex flex-col sm:flex-row gap-3 flex-1">
                <!-- Search -->
                <div class="relative group flex-1 max-w-sm">
                    <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-primary transition-colors text-sm"></i>
                    <input type="text" id="app-user-search" placeholder="Search name, username, email..."
                        class="w-full pl-11 pr-4 py-3 rounded-2xl text-[11px] font-bold tracking-wide focus:border-primary/50 outline-none transition-all placeholder:text-gray-600"
                        style="background-color: var(--glass-bg); border: 1px solid var(--border-color); color: var(--text-main);"
                        oninput="AdminApp.onAppUserSearch(this.value)">
                </div>
                <!-- Status chips -->
                <div class="flex gap-2">
                    <button id="status-chip-all" onclick="AdminApp.setAppUserStatus('all')" class="app-status-chip active px-4 py-2.5 rounded-2xl text-[9px] font-black uppercase tracking-widest border transition-all">All</button>
                    <button id="status-chip-active" onclick="AdminApp.setAppUserStatus('active')" class="app-status-chip px-4 py-2.5 rounded-2xl text-[9px] font-black uppercase tracking-widest border transition-all">Active</button>
                    <button id="status-chip-banned" onclick="AdminApp.setAppUserStatus('banned')" class="app-status-chip px-4 py-2.5 rounded-2xl text-[9px] font-black uppercase tracking-widest border transition-all">Banned</button>
                </div>
            </div>

            <!-- View Toggle -->
            <div class="flex gap-1 p-1 rounded-2xl border" style="background-color: var(--surface-2); border-color: var(--border-color);">
                <button id="view-btn-card" onclick="AdminApp.setAppUserView('card')" title="Card View"
                    class="view-toggle-btn active w-9 h-9 rounded-xl flex items-center justify-center transition-all">
                    <i class="ph-bold ph-squares-four text-base"></i>
                </button>
                <button id="view-btn-table" onclick="AdminApp.setAppUserView('table')" title="Table View"
                    class="view-toggle-btn w-9 h-9 rounded-xl flex items-center justify-center transition-all">
                    <i class="ph-bold ph-list text-base"></i>
                </button>
            </div>
        </div>

        <!-- Card View -->
        <div id="app-users-card-view">
            <div id="app-users-card-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <!-- Cards injected by JS -->
                <div class="col-span-full py-24 flex flex-col items-center gap-4 opacity-40">
                    <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em]">Loading identities...</p>
                </div>
            </div>
        </div>

        <!-- Table View (hidden by default) -->
        <div id="app-users-table-view" class="hidden">
            <div class="stat-card !p-0 overflow-hidden shadow-2xl" style="background-color: var(--glass-bg); border: 1px solid var(--border-color);">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr style="background-color: var(--surface-2); border-bottom: 1px solid var(--border-color);">
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest opacity-50">Identity</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest opacity-50">Username</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest opacity-50">Status</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest opacity-50">Onboarded</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest opacity-50">Joined</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest opacity-50 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="app-users-table-body" class="divide-y" style="border-color: var(--border-color);">
                            <tr>
                                <td colspan="6" class="py-24 text-center">
                                    <p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-30 animate-pulse">Loading identities...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between pt-2">
            <p id="app-users-count-text" class="text-[10px] font-black uppercase tracking-widest opacity-30">—</p>
            <div class="flex gap-3">
                <button onclick="AdminApp.changeAppUserPage(-1)"
                    class="btn-secondary !py-2 !px-5 !text-[9px] !font-black !uppercase tracking-widest !rounded-xl">
                    <i class="ph-bold ph-caret-left mr-1"></i> Prev
                </button>
                <button onclick="AdminApp.changeAppUserPage(1)"
                    class="btn-secondary !py-2 !px-5 !text-[9px] !font-black !uppercase tracking-widest !rounded-xl !bg-primary/10 !text-primary !border-primary/20">
                    Next <i class="ph-bold ph-caret-right ml-1"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Tab: Security Groups (unchanged) -->
    <div id="tab-content-security" class="tab-content hidden space-y-8 animate-in fade-in duration-700">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="stat-card border-red-500/20 bg-gradient-to-br from-red-500/10 to-transparent">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Master Admins</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Admin1" class="w-8 h-8 rounded-lg bg-red-500/10" alt="">
                        <p class="text-xs font-black uppercase tracking-widest">Admin_Main</p>
                    </div>
                    <p class="text-[9px] font-bold opacity-40 uppercase tracking-widest mt-2 leading-relaxed">Full ecosystem orchestration privileges across all clusters.</p>
                </div>
            </div>
            <div class="stat-card border-blue-500/20">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Moderation Units</h3>
                <p class="text-3xl font-black tracking-tighter">14 Agents</p>
                <p class="text-[9px] font-bold opacity-40 uppercase tracking-widest mt-2 leading-relaxed">Active moderators tracking community guideline adherence.</p>
                <button class="w-full mt-6 btn-secondary !py-2.5 !text-[9px] !font-black !uppercase !justify-center !rounded-xl">Manage Units</button>
            </div>
            <div class="stat-card border-green-500/20">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">System Analysts</h3>
                <p class="text-3xl font-black tracking-tighter">04 Nodes</p>
                <p class="text-[9px] font-bold opacity-40 uppercase tracking-widest mt-2 leading-relaxed">Data-level access for ROI and engagement orchestration.</p>
                <button class="w-full mt-6 btn-secondary !py-2.5 !text-[9px] !font-black !uppercase !justify-center !rounded-xl">Audit Roles</button>
            </div>
        </div>
    </div>

    <!-- Tab: Handshake Hub (unchanged) -->
    <div id="tab-content-handshakes" class="tab-content hidden space-y-8 animate-in fade-in duration-700">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="handshake-requests-grid">
            <div class="col-span-full p-20 text-center">
                <p class="animate-pulse font-black text-[10px] uppercase tracking-widest opacity-40">Scanning for Pending Handshakes...</p>
            </div>
        </div>
    </div>

</div>

<!-- Ban Modal -->
<div id="ban-user-modal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-[#030407]/80 backdrop-blur-xl animate-in fade-in zoom-in-95 duration-300">
    <div class="w-full max-w-md rounded-[2.5rem] overflow-hidden shadow-[0_32px_64px_-16px_rgba(0,0,0,0.8)] border border-red-500/20" style="background-color: var(--surface-glass);">
        <div class="p-8 border-b border-red-500/10 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-black uppercase tracking-tight text-red-400">Suspend Account</h3>
                <p class="text-[9px] font-bold opacity-30 uppercase tracking-[0.2em] mt-1">Issue ban enforcement on this identity</p>
            </div>
            <button onclick="window.closeModal()" class="w-10 h-10 rounded-2xl bg-white/5 hover:bg-red-500/10 hover:text-red-400 transition-all flex items-center justify-center border border-white/5">
                <i class="ph-bold ph-x text-sm"></i>
            </button>
        </div>
        <div class="p-8 space-y-5">
            <input type="hidden" id="ban-target-user-id">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest opacity-50">Ban Reason</label>
                <textarea id="ban-reason-input" rows="3" placeholder="e.g. Violated community guidelines — harassment." class="w-full rounded-2xl p-4 text-sm font-medium focus:border-red-500/50 outline-none transition-all resize-none" style="background-color: var(--glass-bg); border: 1px solid var(--border-color); color: var(--text-main);"></textarea>
            </div>
            <button onclick="AdminApp.confirmBanUser()" class="w-full py-4 rounded-2xl bg-red-500 hover:bg-red-600 text-white font-black uppercase tracking-widest text-[10px] transition-all shadow-xl shadow-red-500/20 flex items-center justify-center gap-2">
                <i class="ph-bold ph-prohibit"></i> Enforce Ban
            </button>
        </div>
    </div>
</div>
