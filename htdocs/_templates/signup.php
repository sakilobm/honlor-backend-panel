<!doctype html>
<html lang="en">
<head>
    <?php use Aether\Session; ?>
    <?php Session::loadTemplate('core/_head'); ?>
    <title>Request Access — <?= htmlspecialchars(get_config('project_title', 'Aether')) ?></title>
</head>
<body class="selection:bg-primary/30 selection:text-primary">
    <!-- Ambient Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute top-0 right-1/4 w-[500px] h-[500px] bg-primary/20 blur-[120px] rounded-full opacity-50"></div>
        <div class="absolute bottom-0 left-1/4 w-[400px] h-[400px] bg-blue-500/10 blur-[100px] rounded-full opacity-30"></div>
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
                    <i class="ph-bold ph-user-plus text-3xl"></i>
                </div>
                <h1 class="text-3xl font-black tracking-tight leading-tight">Request <span class="gradient-text">Enlisting</span></h1>
                <p class="text-sm font-medium text-gray-500 mt-2">Join the next generation of administrators.</p>
            </div>

            <div class="glass-card">
                <form id="signup-form" class="space-y-4" novalidate>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 ml-1">Agent Alias</label>
                        <div class="relative group">
                            <i class="ph ph-identification-card absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors"></i>
                            <input type="text" id="signup-username" name="username" placeholder="j_doe_07" required
                                class="w-full bg-white/5 border rounded-2xl py-3.5 pl-12 pr-4 outline-none focus:border-primary transition-all font-semibold text-sm"
                                style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 ml-1">Electronic Mail</label>
                        <div class="relative group">
                            <i class="ph ph-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary transition-colors"></i>
                            <input type="email" id="signup-email" name="email_address" placeholder="agent@aether.com" required
                                class="w-full bg-white/5 border rounded-2xl py-3.5 pl-12 pr-4 outline-none focus:border-primary transition-all font-semibold text-sm"
                                style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 ml-1">Phone (Opt)</label>
                            <input type="tel" id="signup-phone" name="phone" placeholder="+1..."
                                class="w-full bg-white/5 border rounded-2xl py-3.5 px-4 outline-none focus:border-primary transition-all font-semibold text-sm"
                                style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 ml-1">Security Key</label>
                            <input type="password" id="signup-password" name="password" placeholder="••••••••" required
                                class="w-full bg-white/5 border rounded-2xl py-3.5 px-4 outline-none focus:border-primary transition-all font-semibold text-sm"
                                style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="btn-primary w-full !justify-center text-lg py-5 shadow-[0_15px_30px_rgba(124,106,255,0.3)]">
                            Begin Onboarding
                            <i class="ph-bold ph-rocket-launch"></i>
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-8 border-t text-center space-y-4" style="border-color: var(--border-color);">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest leading-loose">
                        Already Enlisted? <a href="/login" class="text-primary hover:underline">Authenticate</a>
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
