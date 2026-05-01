<?php use Aether\Session; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php Session::loadTemplate('admin/_head'); ?>
</head>

<body class="h-screen flex transition-colors duration-500 overflow-hidden relative">

    <!-- Mobile Sidebar Overlay (hidden — sidebar always visible) -->
    <div id="sidebar-overlay" class="hidden"></div>


    <!-- Sidebar Component (admin/_nav) -->
    <?php Session::loadTemplate('admin/_nav'); ?>

    <!-- Main Content Area -->
    <main class="flex-grow flex flex-col relative overflow-hidden h-full min-w-0">
        <!-- Topbar -->
        <header
            class="h-20 border-b flex items-center justify-between px-8 shrink-0 backdrop-blur-lg sticky top-0 z-40 transition-all duration-300"
            style="background-color: var(--surface-glass); border-color: var(--border-color);">
            <div class="flex items-center gap-3 md:gap-6 flex-grow max-w-2xl">
                <div class="relative flex-grow group hidden xs:block">

                    <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 transition-colors"
                        style="color: var(--text-muted);"></i>
                    <input type="text" placeholder="Search records, channels, or ads..."
                        class="w-full border rounded-2xl py-2.5 pl-12 pr-4 outline-none focus:border-primary/50 transition-all text-sm font-medium bg-transparent"
                        style="border-color: var(--border-color); color: var(--text-main);">
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button onclick="toggleTheme()" class="p-2.5 rounded-xl transition-all group"
                    style="background-color: var(--glass-bg); color: var(--text-muted);">
                    <i id="theme-icon" class="ph-bold ph-moon text-xl group-hover:text-primary transition-colors"></i>
                </button>
                <div class="flex items-center gap-2 mr-2">
                    <button class="p-2.5 rounded-xl transition-all relative" onclick="AdminApp.toggleNotifications()"
                        style="background-color: var(--glass-bg); color: var(--text-muted);">
                        <i class="ph-bold ph-bell text-xl"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2"
                            style="border-color: var(--surface);"></span>
                    </button>
                    <button class="p-2.5 rounded-xl transition-all" onclick="AdminApp.switchSection('settings')"
                        style="background-color: var(--glass-bg); color: var(--text-muted);">
                        <i class="ph-bold ph-gear text-xl"></i>
                    </button>
                </div>
                <!-- Separator -->
                <div class="h-8 w-px mx-2" style="background-color: var(--border-color);"></div>

                <!-- Admin Info -->
                <div class="flex items-center gap-3 pl-2">
                    <div class="text-right hidden sm:block">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5 leading-tight">
                            Admin Level</p>
                        <p class="text-xs font-semibold" style="color: var(--text-main);">

                            <?= Session::getUser()->getRoleName() ?>
                        </p>
                    </div>
                    <?php $u = Session::getUser();
                    $profile_avatar = $u->getAvatar(); ?>
                    <img src="<?= !empty($profile_avatar) ? $profile_avatar : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . $u->username ?>"
                        class="w-10 h-10 rounded-xl bg-primary/20 border border-white/5" alt="Avatar">
                </div>
            </div>
        </header>

        <!-- Notification Pane (Interactive Feed) -->
        <div id="notification-pane"
            class="hidden absolute top-24 right-8 w-[380px] z-[100] animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="rounded-[2.5rem] border backdrop-blur-2xl shadow-2xl p-6"
                style="background-color: var(--surface-glass); border-color: var(--border-color);">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold">Activity Feed</h3>
                    <span id="notif-count-badge"
                        class="px-2 py-1 bg-primary/10 text-primary text-[10px] font-black uppercase rounded-lg">4
                        New</span>
                </div>

                <div class="space-y-4 max-h-[480px] overflow-y-auto pr-2 custom-scrollbar">
                    <!-- Notification Item -->
                    <div
                        class="p-4 rounded-3xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all cursor-pointer group">
                        <div class="flex gap-4">
                            <div
                                class="w-10 h-10 rounded-2xl bg-primary/20 flex items-center justify-center text-primary shrink-0">
                                <i class="ph-bold ph-user-plus text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold truncate">New User Registration</h4>
                                <p class="text-xs mt-1 leading-relaxed opacity-70">A new member 'alex_dev' just joined
                                    the community.</p>
                                <span class="text-[10px] font-bold opacity-40 uppercase mt-2 block">2 minutes ago</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Item -->
                    <div
                        class="p-4 rounded-3xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all cursor-pointer group">
                        <div class="flex gap-4">
                            <div
                                class="w-10 h-10 rounded-2xl bg-orange-400/20 flex items-center justify-center text-orange-400 shrink-0">
                                <i class="ph-bold ph-warning-circle text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold truncate">Mod Alert: Reported Post</h4>
                                <p class="text-xs mt-1 leading-relaxed opacity-70">Post #8412 has been flagged for
                                    manual review.</p>
                                <span class="text-[10px] font-bold opacity-40 uppercase mt-2 block">14 minutes
                                    ago</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Item -->
                    <div
                        class="p-4 rounded-3xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all cursor-pointer group">
                        <div class="flex gap-4">
                            <div
                                class="w-10 h-10 rounded-2xl bg-green-500/20 flex items-center justify-center text-green-500 shrink-0">
                                <i class="ph-bold ph-check-circle text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold truncate">Ad Campaign Approved</h4>
                                <p class="text-xs mt-1 leading-relaxed opacity-70">Your 'Summer Sale' campaign is now
                                    live.</p>
                                <span class="text-[10px] font-bold opacity-40 uppercase mt-2 block">1 hour ago</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Item (Low Priority) -->
                    <div
                        class="p-4 rounded-3xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all cursor-pointer group">
                        <div class="flex gap-4">
                            <div
                                class="w-10 h-10 rounded-2xl bg-gray-500/10 flex items-center justify-center text-gray-400 shrink-0">
                                <i class="ph-bold ph-info text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold truncate">System Update</h4>
                                <p class="text-xs mt-1 leading-relaxed opacity-70">Version 10.4 deployment was
                                    successful.</p>
                                <span class="text-[10px] font-bold opacity-40 uppercase mt-2 block">4 hours ago</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-white/5">
                    <button
                        class="w-full py-3 text-xs font-bold uppercase tracking-widest text-primary hover:text-primary-hover transition-all">View
                        All Notifications</button>
                </div>
            </div>
        </div>

        <!-- Scrollable Content Container -->
        <div id="content-container" class="section-content">
            <?php
            if (isset($content)) {
                echo $content;
            } else {
                // Fallback for direct template loading
                $page = Session::getCurrentPageIdentifier() ?? 'dashboard';
                Session::loadTemplate('admin/' . $page, ['user' => Session::getUser()]);
            }
            ?>
        </div>

    </main>

    <?php Session::loadTemplate('admin/_toastv3'); ?>
    <?php Session::loadTemplate('admin/_modals'); ?>

    <!-- CDNs and Infrastructure Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/toastv3.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/apis.js?v=<?= time() ?>"></script>

    <!-- Application Logic (Populates chart, metrics, etc) -->
    <script src="<?= get_config('base_path') ?>js/admin.js?v=<?= time() ?>"></script>

    <!-- Global Modals System -->
    <div id="global-modal-container" class="modal-overlay hidden z-[9999]">
        <div id="modal-content-target" class="w-full h-full flex items-center justify-center p-6">
            <!-- Dynamic Modals (Ban, Warn, Create, Delete) injected here -->
        </div>
    </div>

    <!-- Side Drawer (Record Inspection) -->
    <div id="side-drawer"
        class="fixed inset-y-0 right-0 w-[450px] z-[9999] translate-x-full transition-transform duration-500 backdrop-blur-3xl shadow-[-20px_0_50px_rgba(0,0,0,0.5)] border-l"
        style="background-color: var(--surface-glass); border-color: var(--border-color);">
        <div id="drawer-content-target" class="h-full flex flex-col">
            <!-- Dynamic Drawer Content injected here -->
        </div>
    </div>

    <!-- UI State Handlers -->
    <script>
        window.isRestricted = <?= $isRestricted ? 'true' : 'false' ?>;
        window.adminId = '<?php echo Session::getUser()->id; ?>';

        function toggleTheme() {
            var html = document.documentElement;
            var icon = document.getElementById('theme-icon');
            var isLight = html.classList.toggle('light');

            if (isLight) {
                if (icon) icon.classList.replace('ph-moon', 'ph-sun');
                localStorage.setItem('admin-theme', 'light');
            } else {
                if (icon) icon.classList.replace('ph-sun', 'ph-moon');
                localStorage.setItem('admin-theme', 'dark');
            }
        }

        // Apply saved theme icon on load
        (function () {
            if (localStorage.getItem('admin-theme') === 'light') {
                var icon = document.getElementById('theme-icon');
                if (icon) {
                    icon.classList.remove('ph-moon');
                    icon.classList.add('ph-sun');
                }
            }
        })();

        // Close mobile sidebar on escape key
        document.addEventListener('keydown', function (e) {
            var sidebar = document.getElementById('sidebar');
            if (e.key === 'Escape' && sidebar && sidebar.classList.contains('sidebar-mobile-active')) {
                toggleMobileSidebar();
            }
        });
    </script>
    <!-- Webhook Hub: Add Hook Modal (Global Scope) -->
    <div id="add-webhook-modal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-[#030407]/80 backdrop-blur-xl animate-in fade-in zoom-in-95 duration-300">
        <div class="w-full max-w-lg rounded-[2.5rem] overflow-hidden shadow-[0_32px_64px_-16px_rgba(0,0,0,0.8)] border border-white/10" style="background-color:var(--surface-glass); backdrop-blur-2xl;">
            <div class="p-8 border-b flex items-center justify-between" style="border-color:var(--border-color);">
                <div>
                    <h3 class="text-xl font-black uppercase tracking-tight">Register <span class="gradient-text">Webhook</span></h3>
                    <p class="text-[9px] font-bold opacity-30 uppercase tracking-[0.2em] mt-1">Configure automated delivery endpoint</p>
                </div>
                <button onclick="window.closeModal()" class="w-10 h-10 rounded-2xl bg-white/5 hover:bg-red-500/10 hover:text-red-400 transition-all flex items-center justify-center border border-white/5 shadow-inner">
                    <i class="ph-bold ph-x text-sm"></i>
                </button>
            </div>
            <div class="p-8 space-y-5 max-h-[80vh] overflow-y-auto custom-scrollbar">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest opacity-60">Hook Name</label>
                    <input type="text" id="wh-new-name" placeholder="e.g. Slack Notifier" class="w-full bg-white/[0.03] border border-white/10 rounded-2xl p-4 font-bold text-sm focus:border-primary outline-none transition-all" style="color:var(--text-main);">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest opacity-60">Endpoint URL</label>
                    <input type="url" id="wh-new-url" placeholder="https://hooks.example.com/..." class="w-full bg-white/[0.03] border border-white/10 rounded-2xl p-4 font-bold text-sm focus:border-primary outline-none transition-all font-mono" style="color:var(--text-main);">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest opacity-60">Events</label>
                    <div class="grid grid-cols-2 gap-2">
                        <?php foreach(['user.created','user.deleted','message.flagged','channel.created','report.submitted','login.failed'] as $ev): ?>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-white/10 hover:border-primary/40 cursor-pointer bg-white/[0.02] text-[10px] font-black uppercase tracking-widest transition-all">
                            <input type="checkbox" class="wh-event-check accent-primary" value="<?= $ev ?>"> <?= $ev ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest opacity-60">Secret Key (HMAC)</label>
                    <input type="text" id="wh-new-secret" placeholder="Optional signing secret" class="w-full bg-white/[0.03] border border-white/10 rounded-2xl p-4 font-bold text-sm focus:border-primary outline-none transition-all font-mono" style="color:var(--text-main);">
                </div>
                <button onclick="AdminApp.saveNewWebhook()" class="w-full btn-primary !p-5 !rounded-2xl flex items-center justify-center gap-3 shadow-xl shadow-primary/30 mt-4 group">
                    <i class="ph-bold ph-webhook text-lg group-hover:rotate-12 transition-transform"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Register &amp; Monitor</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Webhook Hub: Error Drawer (Global Scope) -->
    <div id="webhook-error-drawer" class="hidden fixed inset-y-0 right-0 w-full max-w-md z-[9999] shadow-[-64px_0_128px_rgba(0,0,0,0.8)] flex flex-col transition-all duration-500 ease-out border-l border-white/10 translate-x-full" style="background-color:var(--surface); backdrop-blur-3xl;">
        <div class="p-8 border-b flex items-center justify-between shrink-0" style="border-color:var(--border-color);">
            <div>
                <h3 class="text-xl font-black uppercase tracking-tight text-red-400">Error <span style="color:var(--text-main)">Log</span></h3>
                <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mt-1" id="wh-drawer-name">—</p>
            </div>
            <button onclick="AdminApp.closeWebhookErrorDrawer()" class="w-11 h-11 rounded-2xl bg-white/5 hover:bg-red-500/10 hover:text-red-400 transition-all flex items-center justify-center border border-white/5 shadow-inner">
                <i class="ph-bold ph-x text-base"></i>
            </button>
        </div>
        <div class="p-8 flex-grow overflow-y-auto custom-scrollbar space-y-4" id="wh-drawer-errors"></div>
        <div class="p-6 border-t shrink-0 bg-[#030407]/40" style="border-color:var(--border-color);">
            <button onclick="AdminApp.retryWebhook(AdminApp._drawerHookId)" class="w-full btn-primary !p-5 !rounded-2xl flex items-center justify-center gap-3 shadow-xl shadow-primary/20">
                <i class="ph-bold ph-arrow-clockwise text-lg"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Retry All Deliveries</span>
            </button>
        </div>
    </div>
</body>

</html>