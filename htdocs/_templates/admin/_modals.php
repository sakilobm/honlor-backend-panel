<?php use Aether\Session; ?>

<!-- Global Modal: Create Ad Campaign -->
<div id="create-ad-modal" class="modal-overlay hidden">
    <div class="modal-card">
        <div class="flex items-center justify-between p-6 border-b border-white/5">
            <h3 class="text-xl font-bold text-white tracking-tight">Create New Campaign</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-white transition-colors p-2"><i class="ph ph-x text-2xl"></i></button>
        </div>
        <form id="create-ad-form" class="p-8 space-y-6">
            <input type="hidden" name="id" id="ad-edit-id">
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
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Media Injection (Ads Code)</label>
                <textarea name="ad_code" class="w-full bg-transparent border rounded-2xl p-4 text-white font-mono text-xs focus:outline-none focus:border-primary transition-all resize-none" rows="5" style="border-color: var(--border-color);" placeholder="<!-- Paste your ad scripts here -->"></textarea>
            </div>
            <div class="pt-6 flex gap-3">
                <button type="button" onclick="closeModal()" class="btn-secondary flex-1 !justify-center py-4">Cancel</button>
                <button type="submit" id="ad-submit-btn" class="btn-primary flex-1 !justify-center py-4 text-lg">Save Campaign</button>
            </div>
        </form>
    </div>
</div>

<!-- Add more global modals here as needed -->
