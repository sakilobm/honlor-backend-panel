<nav class="fixed top-6 left-1/2 -translate-x-1/2 z-50 w-full max-w-[95%] sm:max-w-4xl px-4 animate-in fade-in">
    <div class="glass-card !p-4 !rounded-[2rem] flex items-center justify-between shadow-[0_20px_50px_rgba(0,0,0,0.3)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.5)]">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-primary/20 rounded-xl flex items-center justify-center text-primary border border-primary/20">
                <i class="ph-bold ph-terminal-window text-xl"></i>
            </div>
            <a href="/" class="font-black text-lg tracking-tight uppercase"><?= htmlspecialchars(get_config('project_title', 'Aether')) ?></a>
        </div>

        <ul class="hidden md:flex items-center gap-1 font-bold text-sm bg-black/5 dark:bg-white/5 p-1 rounded-2xl border" style="border-color: var(--border-color);">
            <li><a href="/" class="px-6 py-2 rounded-xl transition-all <?= Session::currentScript() === 'index' ? 'bg-primary text-white shadow-lg' : 'hover:bg-primary/10' ?>">Home</a></li>
            <li><a href="/posts" class="px-6 py-2 rounded-xl transition-all hover:bg-primary/10">Community</a></li>
            <li><a href="/docs" class="px-6 py-2 rounded-xl transition-all hover:bg-primary/10">API</a></li>
        </ul>

        <div class="flex items-center gap-3">
            <button onclick="toggleTheme()" class="w-10 h-10 rounded-xl bg-glass-white flex items-center justify-center hover:bg-primary hover:text-white transition-all shadow-lg" style="background-color: var(--glass-bg);">
                <i id="theme-icon" class="ph-bold ph-moon"></i>
            </button>
            <div class="h-8 w-px bg-white/10 hidden sm:block"></div>
            <?php if (Session::isAuthenticated()): ?>
            <a href="/admin" class="hidden sm:flex btn-primary !px-6 !py-2.5 !text-xs !rounded-xl">Dashboard</a>
            <a href="/?logout=1" class="sm:hidden w-10 h-10 bg-red-500/10 text-red-500 flex items-center justify-center rounded-xl"><i class="ph-bold ph-sign-out"></i></a>
            <?php else: ?>
            <a href="/login" class="btn-primary !px-6 !py-2.5 !text-xs !rounded-xl">Sign In</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
