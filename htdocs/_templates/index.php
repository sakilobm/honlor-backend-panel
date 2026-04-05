<div class="relative min-h-screen pt-32 pb-20 overflow-hidden">
    <!-- Ambient Background Elements -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[600px] bg-primary/20 blur-[120px] rounded-full -z-10 opacity-50"></div>
    <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-blue-500/10 blur-[100px] rounded-full -z-10 opacity-30"></div>

    <div class="max-w-6xl mx-auto px-6">
        <!-- Hero Section -->
        <div class="text-center space-y-8 mb-32 animate-in fade-in">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-[10px] font-black uppercase tracking-widest">
                <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
                Version 12.4 LTS is now Live
            </div>
            <h1 class="text-5xl md:text-8xl font-black tracking-tight leading-[0.9] lg:max-w-4xl mx-auto">
                Next-Gen <span class="gradient-text">Performance</span> for the Modern Web
            </h1>
            <p class="text-lg md:text-xl text-gray-500 font-medium max-w-2xl mx-auto leading-relaxed">
                Aether Catalyst is an ultra-lightweight, high-performance PHP framework built for speed, security, and developer ergonomics.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                <a href="/login" class="btn-primary">
                    Get Started Free
                    <i class="ph-bold ph-arrow-right"></i>
                </a>
                <a href="/docs" class="btn-secondary">
                    Read Documentation
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-32">
            <?php 
            $stats = [
                ['label' => 'Total Users', 'value' => '4.8k', 'icon' => 'ph-users'],
                ['label' => 'API Uptime', 'value' => '99.9%', 'icon' => 'ph-chart-line-up'],
                ['label' => 'Latency', 'value' => '14ms', 'icon' => 'ph-lightning'],
                ['label' => 'Security Score', 'value' => 'A+', 'icon' => 'ph-shield-check'],
            ];
            foreach ($stats as $s): ?>
            <div class="glass-card hover:scale-[1.05] transition-transform cursor-pointer group">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-4 group-hover:bg-primary group-hover:text-white transition-all">
                    <i class="ph-bold <?= $s['icon'] ?> text-xl"></i>
                </div>
                <h3 class="text-2xl font-black"><?= $s['value'] ?></h3>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500"><?= $s['label'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Feature Spotlight -->
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <h2 class="text-4xl font-black tracking-tight leading-tight">
                    Engineered for <span class="gradient-text">Absolute Control</span>.
                </h2>
                <p class="text-gray-500 leading-relaxed font-medium">
                    Stop fighting with heavy abstractions. Aether gives you a refined, minimalist ecosystem that puts performance and developer experience first.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-center gap-3 font-bold text-sm">
                        <div class="w-6 h-6 bg-green-500/20 text-green-500 rounded-lg flex items-center justify-center"><i class="ph-bold ph-check text-xs"></i></div>
                        Zero-Dependency Core (under 2MB)
                    </li>
                    <li class="flex items-center gap-3 font-bold text-sm">
                        <div class="w-6 h-6 bg-green-500/20 text-green-500 rounded-lg flex items-center justify-center"><i class="ph-bold ph-check text-xs"></i></div>
                        Native Dual-Theme Architecture
                    </li>
                    <li class="flex items-center gap-3 font-bold text-sm">
                        <div class="w-6 h-6 bg-green-500/20 text-green-500 rounded-lg flex items-center justify-center"><i class="ph-bold ph-check text-xs"></i></div>
                        End-to-End RESTful API Support
                    </li>
                </ul>
            </div>
            <div class="relative">
                <div class="glass-card !p-2 relative z-10 overflow-hidden shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=2070&auto=format&fit=crop" class="rounded-[2rem] w-full" alt="Code Snippet">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-bottom p-8">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-primary mb-1">Developer Preview</p>
                            <h4 class="text-white font-bold">Fast Routing Engine v4</h4>
                        </div>
                    </div>
                </div>
                <div class="absolute -top-6 -right-6 w-32 h-32 bg-primary/30 blur-3xl rounded-full"></div>
            </div>
        </div>
    </div>
</div>
