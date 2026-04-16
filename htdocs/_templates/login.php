<!doctype html>
<html lang="en">
<head>
    <?php use Aether\Session; ?>
    <?php Session::loadTemplate('core/_head'); ?>
    <title>Sign In — <?= htmlspecialchars(get_config('project_title', 'Aether')) ?></title>
</head>
<body class="selection:bg-primary/30 selection:text-primary">
    <!-- Ambient Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-primary/20 blur-[120px] rounded-full opacity-50"></div>
        <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] bg-blue-500/10 blur-[100px] rounded-full opacity-30"></div>
    </div>

    <!-- Theme Toggle (Fixed Corner) -->
    <button onclick="toggleTheme()" class="fixed top-8 right-8 w-12 h-12 rounded-2xl bg-glass-white flex items-center justify-center hover:bg-primary hover:text-white transition-all shadow-xl z-50 border" style="background-color: var(--glass-bg); border-color: var(--border-color);">
        <i id="theme-icon" class="ph-bold ph-moon text-xl"></i>
    </button>

    <div id="ball"></div>

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md animate-in fade-in">
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-primary/20 rounded-[1.5rem] flex items-center justify-center text-primary border border-primary/20 mx-auto mb-6 shadow-xl shadow-primary/20">
                    <i class="ph-bold ph-terminal-window text-3xl"></i>
                </div>
                <h1 class="text-3xl font-black tracking-tight leading-tight">Welcome <span class="gradient-text">Back</span></h1>
                <p class="text-sm font-medium text-gray-500 mt-2">Access your Aether intelligence console.</p>
            </div>

            <div class="glass-card">
                <form id="login-form" class="space-y-5" novalidate>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 ml-1">Identity Access</label>
                        <div class="relative group">
                            <i class="ph ph-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors"></i>
                            <input type="text" id="login-user" name="user" placeholder="Username or Email" required
                                class="w-full bg-white/5 border rounded-2xl py-4 pl-12 pr-4 outline-none focus:border-primary transition-all font-semibold text-sm"
                                style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between px-1">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Security Key</label>
                            <a href="#" class="text-[10px] font-black uppercase text-primary hover:underline">Forgot?</a>
                        </div>
                        <div class="relative group">
                            <i class="ph ph-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors"></i>
                            <input type="password" id="login-password" name="password" placeholder="••••••••" required
                                class="w-full bg-white/5 border rounded-2xl py-4 pl-12 pr-4 outline-none focus:border-primary transition-all font-semibold text-sm"
                                style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn-primary w-full !justify-center text-lg py-5">
                            Authenticate
                            <i class="ph-bold ph-shield-check"></i>
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-8 border-t text-center space-y-4" style="border-color: var(--border-color);">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest leading-loose">
                        Non-Agent User? <a href="<?= Session::url('signup') ?>" class="text-primary hover:underline">Request Access</a>
                    </p>
                </div>
            </div>
            
            <p class="text-center mt-10 text-[10px] font-black uppercase tracking-[0.3em] opacity-30">
                Aether Catalyst &copy; <?= date('Y') ?>
            </p>
        </div>
    </div>

    <div class="toast-panel" id="toast-container"></div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script type="module">
        import FingerprintJS from '<?= get_config('base_path') ?>assets/js/fingerprintv3.js';
        const fp = await FingerprintJS.load();
        const r  = await fp.get();
        document.cookie = `fingerprint=${r.visitorId}; path=/; SameSite=None; Secure`;
    </script>

    <script src="<?= get_config('base_path') ?>assets/js/toastv3.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/ball.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/apis.js"></script>
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const isLight = html.classList.toggle('light');
            const theme = isLight ? 'light' : 'dark';
            localStorage.setItem('app-theme', theme);
            localStorage.setItem('admin-theme', theme);
            const icon = document.getElementById('theme-icon');
            if (icon) {
                if (isLight) icon.classList.replace('ph-moon', 'ph-sun');
                else icon.classList.replace('ph-sun', 'ph-moon');
            }
        }
    </script>
</body>
</html>
