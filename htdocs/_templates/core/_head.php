<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars(get_config('project_title', 'My App')) ?></title>
<meta name="description" content="<?= htmlspecialchars(get_config('meta_description', '')) ?>">
<meta name="author" content="<?= htmlspecialchars(get_config('meta_author', '')) ?>">

<!-- Favicon -->
<link rel="icon" type="image/png" href="<?= get_config('base_path') ?>assets/img/favicon.png">

<!-- Global Environment Configuration -->
<script>
    window.BASE_PATH = '<?= get_config('base_path') ?>';
</script>


<!-- CDNs -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script>
    // Blocking theme script to prevent FOUC
    (function() {
        const theme = localStorage.getItem('app-theme') || 'dark';
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
        }
        body { 
            background-color: var(--bg-main);
            color: var(--text-main);
            @apply font-sans antialiased transition-colors duration-500;
        }
    }
    @layer components {
        .btn-primary { 
            @apply px-8 py-4 bg-primary hover:bg-primary-hover text-white rounded-[1.5rem] font-bold transition-all shadow-xl shadow-primary/20 flex items-center gap-2 hover:scale-105 active:scale-95; 
        }
        .btn-secondary { 
            background-color: var(--glass-bg);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            @apply px-8 py-4 rounded-[1.5rem] font-bold transition-all flex items-center gap-2 hover:bg-opacity-10 backdrop-blur-md; 
        }
        .glass-card {
            background-color: var(--surface-glass);
            border: 1px solid var(--border-color);
            @apply backdrop-blur-2xl rounded-[2.5rem] p-8 shadow-2xl;
        }
        .gradient-text {
            @apply bg-clip-text text-transparent bg-gradient-to-r from-primary to-blue-400;
        }
    }

    /* GSAP Ball Cursor */
    #ball {
        @apply fixed top-0 left-0 w-8 h-8 bg-primary rounded-full pointer-events-none z-[9999] opacity-20 blur-sm mix-blend-screen transition-transform duration-75;
    }

    /* Animations */
    .fade-in { animation: fadeIn 0.8s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<!-- Toast Notifications Container -->
<div class="toast-panel" id="toast-container"></div>

