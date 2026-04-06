<?php use Aether\Session; ?>
<!-- Section: Identity Vault (Users) -->
<div id="section-users" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Identity <span class="gradient-text">Vault</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Manage global user accounts and secure ecosystem identities.</p>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <button class="btn-secondary !rounded-2xl flex-1 md:flex-none justify-center" onclick="AdminApp.exportUserCSV()">
                <i class="ph-bold ph-export text-lg"></i>
                Export Ledger
            </button>
            <button class="btn-primary !rounded-2xl flex-1 md:flex-none justify-center shadow-xl shadow-primary/20" onclick="AdminApp.openModal('invite-user-modal')">
                <i class="ph-bold ph-user-plus text-lg"></i>
                Invite Entity
            </button>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-8 border-b border-white/5" id="users-tabs">
        <button class="tab-btn active" data-tab="list" onclick="AdminApp.switchTab('users', 'list')">
            Global Identities
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn" data-tab="security" onclick="AdminApp.switchTab('users', 'security')">
            Security Groups
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn" data-tab="access" onclick="AdminApp.switchTab('users', 'access')">
            Access Governance
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: Global List -->
    <div id="tab-content-list" class="tab-content space-y-8 animate-in fade-in duration-700">
        <div class="stat-card !p-0 overflow-hidden">
            <div class="p-8 border-b border-white/5 bg-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                   <h3 class="text-xl font-black uppercase tracking-tight">Active <span class="gradient-text">Entities</span></h3>
                   <span id="users-total-count" class="text-[10px] font-black uppercase tracking-widest opacity-40 mt-1 leading-none">Scanning Vault...</span>
                </div>
                <div class="flex gap-4 w-full md:w-auto">
                    <div class="relative flex-grow md:flex-none group">
                        <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-primary transition-colors"></i>
                        <input type="text" id="user-filter" placeholder="Search Identities..." 
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-2.5 text-[10px] font-black uppercase tracking-widest focus:border-primary/50 outline-none transition-all placeholder:text-gray-500 text-white">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5" style="background-color: var(--glass-bg);">
                            <th class="py-6 px-8">Identity Profile</th>
                            <th class="py-6 px-8">Safety Status</th>
                            <th class="py-6 px-8">Engagement</th>
                            <th class="py-6 px-8">Auth Date</th>
                            <th class="py-6 px-8 text-right">Synchronization</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body" class="divide-y divide-white/5">
                        <!-- Identities Dynamically Injected by JS -->
                        <tr><td colspan="5" class="p-20 text-center"><p class="animate-pulse font-black text-[10px] uppercase tracking-widest opacity-40">Decrypting Identity Vault...</p></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="p-8 border-t border-white/5 flex justify-between items-center">
                <p id="users-count-text" class="text-[10px] font-black uppercase tracking-widest opacity-40">Awaiting stream packets...</p>
                <div class="flex gap-3">
                    <button onclick="AdminApp.changeUserPage(-1)" class="btn-secondary !py-2 !px-4 !text-[10px] !font-black !uppercase tracking-widest !rounded-xl transition-all">PREV DATA</button>
                    <button onclick="AdminApp.changeUserPage(1)" class="btn-secondary !py-2 !px-4 !text-[10px] !font-black !uppercase tracking-widest !rounded-xl !bg-primary/20 !text-primary !border-primary/20 transition-all">NEXT DATA</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content: Security Groups -->
    <div id="tab-content-security" class="tab-content hidden space-y-8 animate-in fade-in duration-700">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="stat-card border-red-500/20 bg-gradient-to-br from-red-500/10 to-transparent">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Master Admins</h3>
                <div class="space-y-4">
                     <div class="flex items-center gap-4">
                          <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Admin1" class="w-8 h-8 rounded-lg bg-red-500/10">
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

    <!-- Tab Content: Access Governance -->
    <div id="tab-content-access" class="tab-content hidden space-y-8 animate-in fade-in duration-700">
        <div class="glass-card border-primary/20 bg-gradient-to-br from-primary/10 to-transparent flex flex-col md:flex-row items-center gap-10">
            <div class="w-32 h-32 rounded-full border-4 border-dashed border-primary/30 flex items-center justify-center animate-[spin_10s_linear_infinite]">
                 <i class="ph-bold ph-lock-key text-4xl text-primary opacity-60"></i>
            </div>
            <div class="flex-grow text-center md:text-left">
                 <h3 class="text-2xl font-black uppercase tracking-tight mb-2">Pending <span class="gradient-text">Invitations</span></h3>
                 <p class="text-xs font-bold opacity-60 uppercase tracking-widest leading-relaxed">Ecosystem invitations awaiting identity verification. Packets expire in 24 hours.</p>
                 <div class="mt-8 flex flex-wrap justify-center md:justify-start gap-4">
                      <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-1 min-w-[200px]">
                           <p class="text-[10px] font-black uppercase tracking-widest text-primary">agent_beta@honlor.net</p>
                           <p class="text-[8px] font-bold opacity-40 uppercase tracking-widest">Expires in 14h 22m</p>
                      </div>
                 </div>
            </div>
        </div>
    </div>
</div>
