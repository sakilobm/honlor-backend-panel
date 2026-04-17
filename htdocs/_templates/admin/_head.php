<!-- Favicon -->
<link rel="icon" type="image/png" href="<?= get_config('base_path') ?>assets/img/favicon.png">

<!-- Global Environment Configuration -->
<script>
    window.BASE_PATH = '<?= get_config('base_path') ?>';
</script>

<!-- CDNs -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script>
    // Blocking theme script to prevent FOUC (Flash of Dark Mode)
    (function () {
        const theme = localStorage.getItem('admin-theme');
        if (theme === 'light') {
            document.documentElement.classList.add('light');
        } else {
            document.documentElement.classList.remove('light');
        }
    })();
</script>

<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    primary: '#7c6aff',
                    'primary-hover': '#6a59e6',
                },
                fontFamily: {
                    sans: ['Outfit', 'Inter', 'system-ui', 'sans-serif'],
                },
            }
        }
    }
</script>

<style type="text/tailwindcss">
    @layer base {
        :root {
            --bg-main: #060910;
            --surface: #0B0F19;
            --surface-glass: rgba(11, 15, 25, 0.7);
            --text-main: #F9FAFB;
            --text-muted: #9CA3AF;
            --border-color: rgba(255, 255, 255, 0.08);
            --glass-bg: rgba(255, 255, 255, 0.03);
            --toggle-off: rgba(255, 255, 255, 0.1);
            --card-shadow: 0 20px 50px -12px rgba(0,0,0,0.5);
        }
        .light {
            --bg-main: #F8FAFC;
            --surface: #FFFFFF;
            --surface-glass: rgba(248, 250, 252, 0.82);
            --text-main: #0F172A;
            --text-muted: #64748B;
            --border-color: rgba(0, 0, 0, 0.06);
            --glass-bg: rgba(0, 0, 0, 0.03);
            --toggle-off: rgba(15, 23, 42, 0.08);
            --card-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
        }
        body { 
            background-color: var(--bg-main);
            color: var(--text-main);
            @apply font-sans antialiased overflow-hidden transition-colors duration-500;
        }
    }
    @layer components {
        .nav-item { 
            @apply flex items-center gap-3 px-4 py-3 rounded-2xl transition-all cursor-pointer; 
            color: var(--text-muted);
        }
        .nav-item:hover { 
            background-color: var(--glass-bg);
            color: var(--text-main);
        }
        .nav-item.active { 
            @apply bg-primary/10 text-primary font-bold relative; 
        }
        .nav-item.active::before {
            content: "";
            @apply absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full shadow-[0_0_10px_rgba(124,106,255,0.8)];
        }
        .nav-link-premium {
            @apply flex items-center gap-3 px-4 py-3 rounded-2xl transition-all cursor-pointer relative;
            color: var(--text-muted);
        }
        .nav-link-premium:hover {
            background-color: var(--glass-bg);
            color: var(--text-main);
        }
        .nav-link-premium.active {
            @apply bg-primary/5 text-primary font-bold shadow-[inset_0_1px_1px_rgba(255,255,255,0.05)];
        }
        .nav-link-premium.active::before {
            content: "";
            @apply absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full shadow-[0_0_15px_#7c6aff];
        }
        .nav-duotone {
            @apply w-10 h-10 rounded-xl flex items-center justify-center transition-all;
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .nav-link-premium:hover .nav-duotone {
            @apply scale-110;
        }
        .nav-link-premium.active .nav-duotone {
            @apply bg-opacity-20 border-opacity-30 opacity-100;
        }
        .nav-primary { @apply text-primary bg-primary/10 border-primary/20; }
        .nav-blue { @apply text-blue-400 bg-blue-400/5 border-blue-400/10; }
        .nav-purple { @apply text-purple-400 bg-purple-400/5 border-purple-400/10; }
        .nav-green { @apply text-green-400 bg-green-400/5 border-green-400/10; }
        .nav-indigo { @apply text-indigo-400 bg-indigo-400/5 border-indigo-400/10; }
        .nav-orange { @apply text-orange-400 bg-orange-400/5 border-orange-400/10; }
        .nav-red { @apply text-red-400 bg-red-400/5 border-red-400/10; }
        .nav-cyan { @apply text-cyan-400 bg-cyan-400/5 border-cyan-400/10; }
        .nav-emerald { @apply text-emerald-400 bg-emerald-400/5 border-emerald-400/10; }
        .nav-zinc { @apply text-zinc-400 bg-zinc-400/5 border-zinc-400/10; }
        .nav-amber { @apply text-amber-500 bg-amber-500/5 border-amber-500/10; }
        .nav-rose { @apply text-rose-500 bg-rose-500/5 border-rose-500/10; }
        .nav-group-title {
            @apply px-4 mb-4 text-[10px] uppercase font-black tracking-[0.2em];
            color: var(--text-muted);
            opacity: 0.6;
        }
        .glass-card {
            background-color: var(--surface-glass);
            border: 1px solid var(--border-color);
            @apply backdrop-blur-2xl rounded-[2.5rem] p-8 shadow-2xl;
        }
        .gradient-text {
            @apply bg-clip-text text-transparent bg-gradient-to-r from-primary to-blue-400;
        }
        .stat-card { 
            background-color: var(--surface-glass);
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            @apply backdrop-blur-xl p-8 rounded-[2.5rem] transition-all hover:scale-[1.02] hover:shadow-2xl hover:shadow-primary/5; 
        }
        .btn-primary { 
            @apply px-5 py-2.5 bg-primary hover:bg-primary-hover text-white rounded-2xl font-medium transition-all shadow-lg shadow-primary/20 flex items-center gap-2; 
        }
        .btn-secondary { 
            background-color: var(--glass-bg);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            @apply px-5 py-2.5 rounded-2xl font-medium transition-all flex items-center gap-2; 
        }
        .badge-success { @apply px-2.5 py-1 bg-green-500/10 text-green-500 text-[10px] font-bold rounded-full border border-green-500/20 whitespace-nowrap; }
        .badge-warning { @apply px-2.5 py-1 bg-yellow-500/10 text-yellow-600 text-[10px] font-bold rounded-full border border-yellow-500/20 whitespace-nowrap; }
        .badge-danger { @apply px-2.5 py-1 bg-red-500/10 text-red-500 text-[10px] font-bold rounded-full border border-red-500/20 whitespace-nowrap; }
        .badge-neutral { @apply px-2.5 py-1 bg-gray-500/10 text-gray-500 text-[10px] font-bold rounded-full border border-gray-500/20 whitespace-nowrap; }
        
        /* Premium Tabs System */
        .tab-btn {
            @apply relative py-4 text-[10px] font-black uppercase tracking-[0.2em] transition-all cursor-pointer;
            color: var(--text-muted);
        }
        .tab-btn:hover {
            color: var(--text-main);
        }
        .tab-btn.active {
            color: var(--primary);
        }
        .tab-underline {
            @apply absolute bottom-0 left-0 w-full h-0.5 bg-primary rounded-full transition-all duration-300 opacity-0 scale-x-0;
            box-shadow: 0 0 10px var(--primary);
        }
        .tab-btn.active .tab-underline {
            @apply opacity-100 scale-x-100;
        }
        
        /* Modals System */
        .modal-overlay {
            @apply fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-6;
        }
        .modal-card {
            background-color: var(--surface);
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            @apply w-full max-w-2xl rounded-[2.5rem] overflow-hidden flex flex-col max-h-[90vh];
            animation: zoomIn 0.3s cubic-bezier(0, 0, 0.2, 1) forwards;
        }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { 
            background: var(--border-color); 
            border-radius: 10px; 
            transition: all 0.3s ease;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }

        /* Toast Notifications */
        .toast-panel {
            @apply fixed top-6 right-6 z-[1000] flex flex-col gap-4 w-full max-w-[400px] pointer-events-none;
        }
        .toast-item {
            @apply pointer-events-auto transition-all duration-300;
            animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .toast {
            background-color: var(--surface-glass);
            border: 1px solid var(--border-color);
            @apply backdrop-blur-xl p-5 rounded-[2rem] shadow-2xl relative overflow-hidden;
        }
        .toast h3 { @apply font-bold text-sm mb-1; }
        .toast p { @apply text-xs font-medium text-gray-500 leading-relaxed; }
        .toast .close {
            @apply absolute top-4 right-4 w-6 h-6 rounded-full bg-white/5 flex items-center justify-center cursor-pointer hover:bg-white/10 transition-colors after:content-['\2715'] after:text-[10px] after:flex after:items-center after:justify-center;
        }
        .toast.success { border-left: 4px solid #10B981; }
        .toast.error { border-left: 4px solid #EF4444; }
        .toast.warning { border-left: 4px solid #F59E0B; }
        .toast.help { border-left: 4px solid #3B82F6; }

        /* Animation System */
        .fade-in { animation: fadeIn 0.5s ease forwards; }
        .animate-in { animation-fill-mode: both; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes zoomIn {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateX(20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    }

    /* Scrollbar override */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }

    /* Custom classes for PHP framework integration */
    .section-content { @apply flex-grow overflow-y-auto p-8 space-y-8 pb-32; }
</style>