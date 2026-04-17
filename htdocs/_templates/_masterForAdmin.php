<?php use Aether\Session; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php Session::loadTemplate('admin/_head'); ?>
</head>

<body class="h-screen flex transition-colors duration-500 overflow-hidden relative">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[95] hidden md:hidden opacity-0 transition-opacity duration-300"
        onclick="toggleMobileSidebar()"></div>


    <!-- Sidebar Component (admin/_nav) -->
    <?php Session::loadTemplate('admin/_nav'); ?>

    <!-- Main Content Area -->
    <main class="flex-grow flex flex-col relative overflow-hidden h-full min-w-0">
        <!-- Topbar -->
        <header
            class="h-20 border-b flex items-center justify-between px-8 shrink-0 backdrop-blur-lg sticky top-0 z-40 transition-all duration-300"
            style="background-color: var(--surface-glass); border-color: var(--border-color);">
            <div class="flex items-center gap-3 md:gap-6 flex-grow max-w-2xl">
                <button
                    class="md:hidden p-2 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center"
                    style="color: var(--text-muted);" onclick="toggleMobileSidebar()">
                    <i class="ph-bold ph-list text-2xl"></i>
                </button>
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
    <div id="global-modal-container" class="modal-overlay hidden z-[100]">
        <div id="modal-content-target" class="w-full h-full flex items-center justify-center p-6">
            <!-- Dynamic Modals (Ban, Warn, Create, Delete) injected here -->
        </div>
    </div>

    <!-- Side Drawer (Record Inspection) -->
    <div id="side-drawer"
        class="fixed inset-y-0 right-0 w-[450px] z-[90] translate-x-full transition-transform duration-500 backdrop-blur-3xl shadow-[-20px_0_50px_rgba(0,0,0,0.5)] border-l"
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
            const html = document.documentElement;
            const icon = document.getElementById('theme-icon');
            const isLight = html.classList.toggle('light');

            if (isLight) {
                icon.classList.replace('ph-moon', 'ph-sun');
                localStorage.setItem('admin-theme', 'light');
            } else {
                icon.classList.replace('ph-sun', 'ph-moon');
                localStorage.setItem('admin-theme', 'dark');
            }
        }

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) sidebar.classList.toggle('-translate-x-full');
        }

        // Apply saved theme icon on load
        (function () {
            if (localStorage.getItem('admin-theme') === 'light') {
                const icon = document.getElementById('theme-icon');
                if (icon) {
                    icon.classList.remove('ph-moon');
                    icon.classList.add('ph-sun');
                }
            }
        })();
    </script>

    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const isOpen = sidebar.classList.contains('translate-x-0');

            if (isOpen) {
                sidebar.classList.replace('translate-x-0', '-translate-x-full');
                overlay.classList.replace('opacity-100', 'opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            } else {
                sidebar.classList.replace('-translate-x-full', 'translate-x-0');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.replace('opacity-0', 'opacity-100'), 10);
            }
        }

        // Close mobile sidebar on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && document.getElementById('sidebar').classList.contains('translate-x-0')) {
                toggleMobileSidebar();
            }
        });
    </script>
</body>

</html>