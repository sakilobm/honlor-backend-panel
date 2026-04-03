<?php use Aether\Session; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php Session::loadTemplate('admin/_head'); ?>
</head>
<body class="dark h-screen flex">
    
    <!-- Sidebar Component (admin/_nav) -->
    <?php Session::loadTemplate('admin/_nav'); ?>

    <!-- Main Content Area -->
    <main class="flex-grow flex flex-col relative overflow-hidden h-full">
        <!-- Topbar -->
        <header class="h-20 border-b flex items-center justify-between px-8 shrink-0 backdrop-blur-lg sticky top-0 z-40 transition-all duration-300" style="background-color: var(--surface-glass); border-color: var(--border-color);">
            <div class="flex items-center gap-6 flex-grow max-w-2xl">
                <button class="md:hidden p-2" style="color: var(--text-muted);" onclick="toggleMobileSidebar()">
                    <i class="ph-bold ph-list text-2xl"></i>
                </button>
                <div class="relative flex-grow group">
                    <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 transition-colors" style="color: var(--text-muted);"></i>
                    <input type="text" placeholder="Search records, channels, or ads..." 
                        class="w-full border rounded-2xl py-2.5 pl-12 pr-4 outline-none focus:border-primary/50 transition-all text-sm font-medium bg-transparent" style="border-color: var(--border-color); color: var(--text-main);">
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <button onclick="toggleTheme()" class="p-2.5 rounded-xl transition-all group" style="background-color: var(--glass-bg); color: var(--text-muted);">
                    <i id="theme-icon" class="ph-bold ph-moon text-xl group-hover:text-primary transition-colors"></i>
                </button>
                <div class="flex items-center gap-2 mr-2">
                    <button class="p-2.5 rounded-xl transition-all relative" style="background-color: var(--glass-bg); color: var(--text-muted);">
                        <i class="ph-bold ph-bell text-xl"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2" style="border-color: var(--surface);"></span>
                    </button>
                    <button class="p-2.5 rounded-xl transition-all" style="background-color: var(--glass-bg); color: var(--text-muted);">
                        <i class="ph-bold ph-gear text-xl"></i>
                    </button>
                </div>
                <!-- Separator -->
                <div class="h-8 w-px mx-2" style="background-color: var(--border-color);"></div>
                
                <!-- Admin Info -->
                <div class="flex items-center gap-3 pl-2">
                    <div class="text-right hidden sm:block">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5 leading-tight">Admin Level</p>
                        <p class="text-xs font-semibold text-white">Super Admin</p>
                    </div>
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Marcus" class="w-10 h-10 rounded-xl bg-primary/20 border border-white/5" alt="Avatar">
                </div>
            </div>
        </header>

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

    <!-- CDNs and Infrastructure Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/toastv3.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/apis.js"></script>
    
    <!-- Application Logic (Populates chart, metrics, etc) -->
    <script src="/js/admin.min.js"></script>

    <!-- UI State Handlers -->
    <script>
        function toggleTheme() {
            const body = document.body;
            const icon = document.getElementById('theme-icon');
            if (body.classList.contains('light')) {
                body.classList.remove('light');
                body.classList.add('dark');
                icon.classList.remove('ph-sun');
                icon.classList.add('ph-moon');
                localStorage.setItem('admin-theme', 'dark');
            } else {
                body.classList.remove('dark');
                body.classList.add('light');
                icon.classList.remove('ph-moon');
                icon.classList.add('ph-sun');
                localStorage.setItem('admin-theme', 'light');
            }
        }

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        // Apply saved theme
        if (localStorage.getItem('admin-theme') === 'light') toggleTheme();
    </script>

</body>
</html>
