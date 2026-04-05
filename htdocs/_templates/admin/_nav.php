<aside id="sidebar" class="w-72 shrink-0 border-r transition-all duration-300 md:relative fixed inset-y-0 left-0 z-50 -translate-x-full md:translate-x-0 overflow-hidden flex flex-col" style="background-color: var(--surface); border-color: var(--border-color);">
    <div class="p-8 pb-4">
        <!-- Brand -->
        <div class="flex items-center gap-3 mb-10">
            <div class="w-10 h-10 bg-primary rounded-2xl flex items-center justify-center shadow-lg shadow-primary/30">
                <i class="ph-bold ph-hexagon text-white text-xl"></i>
            </div>
            <div>
                <h1 class="font-bold text-xl tracking-tight">Honlor</h1>
                <p class="text-[9px] uppercase font-black tracking-[0.25em] text-primary" style="opacity: 0.8;">Admin Core</p>
            </div>
        </div>
    </div>
    
    <!-- Unified Scrollable Navigation -->
    <div class="px-8 py-0 flex-grow overflow-y-auto custom-scrollbar">
        <!-- Workspace Nav -->
        <div class="mb-10">
            <h3 class="nav-group-title">Workspace</h3>
            <nav class="space-y-1.5">
                <a href="/admin?page=dashboard" class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'dashboard' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-blue"><i class="ph-bold ph-squares-four text-lg"></i></div>
                    <span>Overview</span>
                </a>
                <a href="/admin?page=users" class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'users' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-purple"><i class="ph-bold ph-users text-lg"></i></div>
                    <span>Identity Vault</span>
                </a>
                <a href="/admin?page=messages" class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'messages' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-green"><i class="ph-bold ph-chats-circle text-lg"></i></div>
                    <span>Moderation</span>
                </a>
                <a href="/admin?page=channels" class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'channels' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-indigo"><i class="ph-bold ph-graph text-lg"></i></div>
                    <span>Channels</span>
                </a>
            </nav>
        </div>

        <div class="mb-10">
            <h3 class="nav-group-title">Governance</h3>
            <nav class="space-y-1.5">
                <a href="/admin?page=ads" class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'ads' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-orange"><i class="ph-bold ph-megaphone text-lg"></i></div>
                    <span>Ads Pipeline</span>
                </a>
                <a href="/admin?page=reports" class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'reports' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-red"><i class="ph-bold ph-shield-warning text-lg"></i></div>
                    <span>Compliance</span>
                </a>
                <a href="/admin?page=deletion_requests" class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'deletion_requests' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-amber"><i class="ph-bold ph-user-minus text-lg"></i></div>
                    <span>Account Deletion</span>
                </a>
                <a href="/admin?page=analytics" class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'analytics' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-cyan"><i class="ph-bold ph-activity text-lg"></i></div>
                    <span>Intelligence</span>
                </a>
            </nav>
        </div>

        <div class="mb-4">
            <h3 class="nav-group-title">System</h3>
            <nav class="space-y-1.5">
                <a href="/admin?page=settings" class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'settings' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-zinc"><i class="ph-bold ph-sliders text-lg"></i></div>
                    <span>Control Center</span>
                </a>
                <a href="/admin?page=logs" class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'logs' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-emerald"><i class="ph-bold ph-terminal-window text-lg"></i></div>
                    <span>Server Health</span>
                </a>
            </nav>
        </div>
    </div>
    
    <!-- Footer / Profile -->
    <div class="p-6">
        <div class="stat-card !p-4 !rounded-[2rem] bg-glass-white backdrop-blur-xl border-white/5 flex items-center justify-between group transition-all hover:scale-[1.05] cursor-pointer" onclick="AdminApp.openDrawer('user', '<?php echo Session::getUser()->id; ?>')">
            <div class="flex items-center gap-3 overflow-hidden">
                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Marcus" class="w-10 h-10 rounded-xl bg-primary/20 border border-primary/20" alt="Avatar">
                <div class="overflow-hidden">
                    <p class="font-bold text-xs truncate leading-tight"><?php $u = Session::getUser(); echo $u ? htmlspecialchars($u->getUsername()) : 'Admin'; ?></p>
                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mt-0.5">Online Agent</p>
                </div>
            </div>
            <a href="/admin?logout=1" class="w-8 h-8 rounded-xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-lg shadow-red-500/20" title="Logout">
                <i class="ph-bold ph-power"></i>
            </a>
        </div>
    </div>
</aside>
