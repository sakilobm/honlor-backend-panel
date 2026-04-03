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

    <!-- Create Campaign Modal -->
    <div id="create-ad-modal" class="modal-overlay hidden">
        <div class="modal-card">
            <div class="flex items-center justify-between p-6 border-b border-white/5">
                <h3 class="text-xl font-bold text-white tracking-tight">Create New Campaign</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-white transition-colors p-2"><i class="ph ph-x text-2xl"></i></button>
            </div>
            <form id="create-ad-form" class="p-8 space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Campaign Reference Name</label>
                    <input type="text" name="name" required class="w-full bg-transparent border rounded-2xl p-4 text-white focus:outline-none focus:border-primary transition-all font-medium" style="border-color: var(--border-color);" placeholder="e.g. Winter Sale 2026">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Strategy Type</label>
                        <select name="type" required class="w-full bg-transparent border rounded-2xl p-4 text-white focus:outline-none focus:border-primary transition-all font-medium" style="border-color: var(--border-color);">
                            <option value="Social">Social Broad</option>
                            <option value="Search">Search Target</option>
                            <option value="Display">Visual Display</option>
                            <option value="Email">Email Outreach</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Daily Budget ($)</label>
                        <input type="number" step="0.01" name="budget" required class="w-full bg-transparent border rounded-2xl p-4 text-white font-medium focus:outline-none focus:border-primary transition-all" style="border-color: var(--border-color);">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Launch Date</label>
                        <input type="date" name="start_date" required class="w-full bg-transparent border rounded-2xl p-4 text-white font-medium focus:outline-none" style="border-color: var(--border-color);">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">End Date</label>
                        <input type="date" name="end_date" required class="w-full bg-transparent border rounded-2xl p-4 text-white font-medium focus:outline-none" style="border-color: var(--border-color);">
                    </div>
                </div>
                <div class="pt-6 flex gap-3">
                    <button type="button" onclick="closeModal()" class="btn-secondary flex-1 !justify-center py-4">Cancel</button>
                    <button type="submit" class="btn-primary flex-1 !justify-center py-4 text-lg">Launch Campaign</button>
                </div>
            </form>
        </div>
    </div>
</div>
