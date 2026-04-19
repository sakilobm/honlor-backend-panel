<aside id="sidebar"
    class="w-80 shrink-0 border-r transition-all duration-500 md:relative fixed inset-y-0 left-0 z-[100] -translate-x-full md:translate-x-0 flex flex-col backdrop-blur-3xl"
    style="background-color: var(--surface-glass); border-color: var(--border-color); overflow: visible !important;">
    <!-- Mobile Close Button -->
    <button
        class="md:hidden absolute top-6 right-6 p-2 rounded-xl bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all border border-primary/20"
        onclick="toggleMobileSidebar()">
        <i class="ph-bold ph-x text-xl"></i>
    </button>

    <div class="p-8 pb-4 relative">
        <!-- Brand -->
        <div class="flex items-center gap-3 mb-10 overflow-hidden">
            <div
                class="w-12 h-12 shrink-0 bg-primary/20 rounded-[1.25rem] flex items-center justify-center border border-primary/20 shadow-xl shadow-primary/20">
                <i class="ph-bold ph-hexagon text-primary text-2xl"></i>
            </div>
            <div class="sidebar-hide whitespace-nowrap">
                <h1 class="font-black text-xl tracking-tight uppercase">
                    <?= htmlspecialchars(get_config('project_title', 'Aether')) ?>
                </h1>
                <p class="text-[9px] uppercase font-black tracking-[0.25em] text-primary" style="opacity: 0.8;">Admin
                    Intelligence</p>
            </div>
        </div>

        <!-- Sidebar Toggle (Desktop Handle) -->
        <button id="sidebar-toggle"
            class="hidden md:flex absolute -right-4 top-10 w-8 h-8 bg-primary text-black rounded-full items-center justify-center transition-all z-50 shadow-2xl hover:scale-110 active:scale-90 border-4 border-[#030407]"
            onclick="toggleSidebarResilient()">
            <i class="ph-bold ph-caret-left text-xs" id="toggle-icon"></i>
        </button>
    </div>

    <!-- Failsafe Sidebar Handler (Zero-Dependency) -->
    <script>
        function toggleSidebarResilient() {
            var sidebar = document.getElementById('sidebar');
            var icon = document.getElementById('toggle-icon');
            if (!sidebar) return;

            var isCollapsed = sidebar.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed);

            if (icon) {
                if (isCollapsed) {
                    icon.classList.remove('ph-caret-left');
                    icon.classList.add('ph-caret-right');
                } else {
                    icon.classList.remove('ph-caret-right');
                    icon.classList.add('ph-caret-left');
                }
            }

            // Sync AdminApp if available
            if (window.AdminApp && typeof AdminApp.syncSidebarIcon === 'function') {
                AdminApp.syncSidebarIcon();
            }

            // Force reflow for charts
            window.dispatchEvent(new Event('resize'));
        }

        function toggleMobileSidebar() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebar-overlay');
            if (!sidebar) return;

            var isActive = sidebar.classList.toggle('sidebar-mobile-active');
            if (overlay) {
                if (isActive) {
                    overlay.classList.remove('hidden');
                    setTimeout(function() { overlay.style.opacity = '1'; }, 10);
                } else {
                    overlay.style.opacity = '0';
                    setTimeout(function() { overlay.classList.add('hidden'); }, 300);
                }
            }
        }
    </script>

    <!-- Unified Scrollable Navigation -->
    <div class="px-8 py-0 flex-grow overflow-y-auto custom-scrollbar">
        <!-- Workspace Nav -->
        <div class="mb-10">
            <h3 class="nav-group-title sidebar-hide">Workspace</h3>
            <nav class="space-y-1.5">
                <a href="javascript:AdminApp.switchSection('dashboard')"
                    class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'dashboard' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-blue"><i class="ph-bold ph-squares-four text-lg"></i></div>
                    <span class="sidebar-hide">Overview</span>
                </a>
                <a href="javascript:AdminApp.switchSection('users')"
                    class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'users' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-purple"><i class="ph-bold ph-users text-lg"></i></div>
                    <span class="sidebar-hide">Identity Vault</span>
                </a>
                <a href="javascript:AdminApp.switchSection('messages')"
                    class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'messages' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-green"><i class="ph-bold ph-chats-circle text-lg"></i></div>
                    <span class="sidebar-hide">Moderation</span>
                </a>
                <a href="javascript:AdminApp.switchSection('channels')"
                    class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'channels' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-indigo"><i class="ph-bold ph-graph text-lg"></i></div>
                    <span class="sidebar-hide">Channels</span>
                </a>
            </nav>
        </div>

        <div class="mb-10">
            <h3 class="nav-group-title sidebar-hide">Governance</h3>
            <nav class="space-y-1.5">
                <a href="javascript:AdminApp.switchSection('ads')"
                    class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'ads' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-orange"><i class="ph-bold ph-megaphone text-lg"></i></div>
                    <span class="sidebar-hide">Ads Pipeline</span>
                </a>
                <a href="javascript:AdminApp.switchSection('reports')"
                    class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'reports' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-red"><i class="ph-bold ph-shield-warning text-lg"></i></div>
                    <span class="sidebar-hide">Compliance</span>
                </a>
                <a href="javascript:AdminApp.switchSection('deletion_requests')"
                    class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'deletion_requests' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-amber"><i class="ph-bold ph-user-minus text-lg"></i></div>
                    <span class="sidebar-hide">Account Deletion</span>
                </a>
                <a href="javascript:AdminApp.switchSection('analytics')"
                    class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'analytics' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-cyan"><i class="ph-bold ph-activity text-lg"></i></div>
                    <span class="sidebar-hide">Intelligence</span>
                </a>
                <a href="javascript:AdminApp.switchSection('policy_editor')"
                    class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'policy_editor' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-rose"><i class="ph-bold ph-read-cv-logo text-lg"></i></div>
                    <span class="sidebar-hide">Policy Studio</span>
                </a>
            </nav>
        </div>

        <div class="mb-4">
            <h3 class="nav-group-title sidebar-hide">System</h3>
            <nav class="space-y-1.5">
                <a href="javascript:AdminApp.switchSection('settings')"
                    class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'settings' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-primary"><i class="ph-bold ph-sliders text-lg"></i></div>
                    <span class="sidebar-hide">Control Center</span>
                </a>
                <a href="javascript:AdminApp.switchSection('logs')"
                    class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'logs' ? 'active' : '' ?>">
                    <div class="nav-duotone nav-emerald"><i class="ph-bold ph-terminal-window text-lg"></i></div>
                    <span class="sidebar-hide">Server Health</span>
                </a>
                <?php if (Session::isMaster()): ?>
                    <a href="javascript:AdminApp.switchSection('roles')"
                        class="nav-link-premium <?= Session::getCurrentPageIdentifier() === 'roles' ? 'active' : '' ?>">
                        <div class="nav-duotone nav-rose"><i class="ph-bold ph-shield-star text-lg"></i></div>
                        <span class="sidebar-hide">Role Studio</span>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>

    <!-- Footer / Profile -->
    <div class="p-6">
        <div class="glass-card !p-4 !rounded-[2rem] flex items-center justify-between group transition-all hover:scale-[1.05] cursor-pointer shadow-xl shadow-black/20"
            onclick="AdminApp.openDrawer('user', '<?php echo Session::getUser()->id; ?>')">
            <div class="flex items-center gap-3 overflow-hidden">
                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Marcus"
                    class="w-10 h-10 shrink-0 rounded-xl bg-primary/20 border border-primary/20" alt="Avatar">
                <div class="sidebar-hide overflow-hidden">
                    <p class="font-bold text-[11px] truncate leading-tight uppercase tracking-tight">
                        <?php $u = Session::getUser();
                        echo $u ? htmlspecialchars($u->getUsername()) : 'Admin'; ?>
                    </p>
                    <p class="text-[9px] font-black text-primary uppercase tracking-widest mt-0.5 opacity-80">Online
                        Agent</p>
                </div>
            </div>
            <button
                class="w-10 h-10 shrink-0 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all border border-red-500/20"
                title="Logout" onclick="window.location.href='<?= get_config('base_path') ?>admin?logout=1'">
                <i class="ph-bold ph-power text-lg"></i>
            </button>
        </div>
    </div>
</aside>