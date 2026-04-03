<?php use Aether\Session; ?>
<!-- Section: Global Messaging -->
<div id="section-messages" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight mb-2">Network Traffic</h2>
            <p class="font-medium" style="color: var(--text-muted);">Real-time messaging frequency and delivery analytics.</p>
        </div>
        <div class="flex gap-3">
            <button class="btn-secondary">
                <i class="ph-bold ph-gear"></i>
                Rate Limits
            </button>
            <button class="btn-primary">
                <i class="ph-bold ph-broadcast"></i>
                System Alert
            </button>
        </div>
    </div>

    <!-- Stats & Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="stat-card lg:col-span-1">
            <h3 class="font-bold text-lg mb-6">Delivery Success</h3>
            <div class="flex justify-center mb-8">
                <div class="relative w-48 h-48 flex items-center justify-center">
                    <svg class="w-full h-full -rotate-90">
                        <circle cx="96" cy="96" r="88" stroke="currentColor" stroke-width="12" fill="transparent" style="color: var(--border-color);"></circle>
                        <circle cx="96" cy="96" r="88" stroke="currentColor" stroke-width="12" fill="transparent" class="text-primary" stroke-dasharray="552.92" stroke-dashoffset="13" stroke-linecap="round"></circle>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-bold">98.2%</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-muted);">Uptime</span>
                    </div>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between text-xs font-bold">
                    <span style="color: var(--text-muted);">Requests Sent</span>
                    <span>1.2M</span>
                </div>
                <div class="flex justify-between text-xs font-bold">
                    <span style="color: var(--text-muted);">Failed Retries</span>
                    <span class="text-red-500">2.4k</span>
                </div>
            </div>
        </div>

        <!-- Latency Heatmap -->
        <div class="stat-card lg:col-span-2">
            <h3 class="font-bold text-lg mb-6">Regional Latency</h3>
            <div class="grid grid-cols-12 gap-2 h-64 items-end">
                <!-- Simple mock bars -->
                <div class="bg-primary/20 h-[30%] rounded-t-lg transition-all hover:bg-primary"></div>
                <div class="bg-primary/20 h-[50%] rounded-t-lg transition-all hover:bg-primary"></div>
                <div class="bg-primary/20 h-[80%] rounded-t-lg transition-all hover:bg-primary"></div>
                <div class="bg-primary/20 h-[60%] rounded-t-lg transition-all hover:bg-primary"></div>
                <div class="bg-primary/20 h-[40%] rounded-t-lg transition-all hover:bg-primary"></div>
                <div class="bg-primary/40 h-[90%] rounded-t-lg transition-all hover:bg-primary animate-pulse"></div>
                <div class="bg-primary/20 h-[40%] rounded-t-lg transition-all hover:bg-primary"></div>
                <div class="bg-primary/20 h-[70%] rounded-t-lg transition-all hover:bg-primary"></div>
                <div class="bg-primary/20 h-[55%] rounded-t-lg transition-all hover:bg-primary"></div>
                <div class="bg-primary/20 h-[35%] rounded-t-lg transition-all hover:bg-primary"></div>
                <div class="bg-primary/20 h-[65%] rounded-t-lg transition-all hover:bg-primary"></div>
                <div class="bg-primary/20 h-[45%] rounded-t-lg transition-all hover:bg-primary"></div>
            </div>
            <div class="flex justify-between mt-6 text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-muted);">
                <span>00:00</span>
                <span>06:00</span>
                <span>12:00</span>
                <span>18:00</span>
                <span>23:59</span>
            </div>
        </div>
    </div>

    <!-- Active Traffic Log -->
    <div class="stat-card">
        <h3 class="font-bold text-lg mb-6">Recent Deliveries</h3>
        <div class="space-y-4">
             <div class="flex items-center justify-between p-4 rounded-2xl border hover:bg-white/5 transition-all" style="border-color: var(--border-color);">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center text-green-400">
                        <i class="ph ph-chat-centered-text text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-sm">Encrypted Message Packet</p>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Source: #market-alpha</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-white">Delivered</p>
                    <p class="text-[10px] uppercase font-bold text-gray-500">24ms ago</p>
                </div>
            </div>
            <div class="flex items-center justify-between p-4 rounded-2xl border hover:bg-white/5 transition-all" style="border-color: var(--border-color);">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400">
                        <i class="ph ph-image text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-sm">Media Cluster Upload</p>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Source: UI-Mobile</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-white">Processing</p>
                    <p class="text-[10px] uppercase font-bold text-gray-500">1m ago</p>
                </div>
            </div>
        </div>
    </div>
</div>
