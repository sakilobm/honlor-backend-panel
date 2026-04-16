<nav class="fixed top-6 inset-x-0 z-50 flex justify-center px-4 pointer-events-none">
    <div class="glass-card !p-3 sm:!p-4 !rounded-[2rem] flex items-center justify-between shadow-[0_20px_50px_rgba(0,0,0,0.3)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.5)] w-full max-w-4xl animate-in fade-in zoom-in-95 duration-700 pointer-events-auto">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-primary/20 rounded-xl flex items-center justify-center text-primary border border-primary/20">
                <i class="ph-bold ph-terminal-window text-xl"></i>
            </div>
            <a href="<?= Session::url() ?>" class="font-black text-sm sm:text-lg tracking-tight uppercase truncate max-w-[120px] sm:max-w-none"><?= htmlspecialchars(get_config('project_title', 'Aether')) ?></a>
        </div>

        <ul class="hidden md:flex items-center gap-1 font-bold text-sm bg-black/5 dark:bg-white/5 p-1 rounded-2xl border" style="border-color: var(--border-color);">
            <li><a href="<?= Session::url() ?>" class="px-6 py-2 rounded-xl transition-all <?= Session::getCurrentPageIdentifier() === 'dashboard' ? '' : 'bg-primary text-white shadow-lg' ?>">Home</a></li>
        </ul>

        <div class="flex items-center gap-2 sm:gap-3">
            <button onclick="toggleTheme()" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-glass-white flex items-center justify-center hover:bg-primary hover:text-white transition-all shadow-lg" style="background-color: var(--glass-bg);">
                <i id="theme-icon" class="ph-bold ph-moon"></i>
            </button>
            <div class="h-8 w-px bg-white/10 hidden sm:block"></div>
            <?php if (Session::isAuthenticated()): ?>
            <a href="<?= Session::url('admin') ?>" class="hidden xs:flex btn-primary !px-6 !py-2.5 !text-xs !rounded-xl">Dashboard</a>
            <a href="<?= Session::url('?logout=1') ?>" class="xs:hidden w-9 h-9 bg-red-500/10 text-red-500 flex items-center justify-center rounded-xl"><i class="ph-bold ph-sign-out"></i></a>
            <?php else: ?>
            <a href="<?= Session::url('login') ?>" class="btn-primary !px-5 sm:!px-6 !py-2 sm:!py-2.5 !text-[10px] sm:!text-xs !rounded-xl">Sign In</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
