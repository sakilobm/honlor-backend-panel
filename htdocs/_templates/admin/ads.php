<?php use Aether\Session; ?>
<!-- Section: Ads Manager -->
<div id="section-ads" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight mb-2">Ads Manager</h2>
            <p class="font-medium" style="color: var(--text-muted);">Control and monitor your advertising campaigns</p>
        </div>
        <div class="flex gap-3">
            <button class="btn-primary" onclick="openModal('create-ad-modal')">
                <i class="ph-bold ph-plus text-lg"></i>
                Create Campaign
            </button>
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="stat-card !p-0 overflow-hidden">
        <div class="p-6 border-b flex justify-between items-center" style="border-color: var(--border-color);">
            <h3 class="font-bold flex items-center gap-2">
                Active Campaigns
                <span class="badge-neutral text-primary border-primary/20 bg-primary/10" id="ads-count-badge">...</span>
            </h3>
            <div class="flex gap-2">
                 <input type="text" placeholder="Search campaigns..." class="bg-transparent border rounded-xl px-4 py-1.5 text-xs outline-none focus:border-primary/50" style="border-color: var(--border-color);">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b" style="border-color: var(--border-color); background-color: var(--glass-bg);">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Campaign Name</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Budget</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">CTR</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody id="ads-table-body" class="divide-y" style="border-color: var(--border-color);">
                    <!-- Data injected by JS -->
                </tbody>
            </table>
        </div>
    </div>

        </div>
    </div>
</div>
