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

    <!-- Active Traffic Log (Moderation View) -->
    <div class="stat-card !p-0 overflow-hidden">
        <div class="p-6 border-b flex justify-between items-center" style="border-color: var(--border-color);">
            <h3 class="font-bold flex items-center gap-2">
                Recent Messages
                <span class="badge-neutral border-primary/20 text-primary">Live Moderation</span>
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b" style="border-color: var(--border-color); background-color: var(--glass-bg);">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Channel</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">User / Content</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Timestamp</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="messages-table-body" class="divide-y" style="border-color: var(--border-color);">
                    <!-- Rows injected by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>
