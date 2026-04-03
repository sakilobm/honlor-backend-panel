<?php use Aether\Session; ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars(get_config('project_title', 'Honlor Admin')) ?></title>

<!-- CDNs -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    primary: '#7c6aff',
                    'primary-hover': '#6a59e6',
                    'bg-dark': '#0B0F19',
                    'surface-dark': '#111827',
                    'glass-white': 'rgba(255, 255, 255, 0.03)',
                    'border-dark': 'rgba(255, 255, 255, 0.08)'
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
            --bg-main: #0B0F19;
            --surface: #111827;
            --surface-glass: rgba(11, 15, 25, 0.7);
            --text-main: #F9FAFB;
            --text-muted: #9CA3AF;
            --border-color: rgba(255, 255, 255, 0.08);
            --glass-bg: rgba(255, 255, 255, 0.03);
            --card-shadow: 0 10px 30px -10px rgba(0,0,0,0.5);
        }
        .light {
            --bg-main: #F8FAFC;
            --surface: #FFFFFF;
            --surface-glass: rgba(248, 250, 252, 0.82);
            --text-main: #0F172A;
            --text-muted: #64748B;
            --border-color: rgba(0, 0, 0, 0.06);
            --glass-bg: rgba(0, 0, 0, 0.03);
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
            @apply bg-primary/10 text-primary font-bold; 
        }
        .stat-card { 
            background-color: var(--surface);
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            @apply p-6 rounded-3xl transition-all hover:scale-[1.02]; 
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
    }

    /* Scrollbar override */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }

    /* Custom classes for PHP framework integration */
    .section-content { @apply flex-grow overflow-y-auto p-8 space-y-8 pb-32; }
</style>
