<?php use Aether\Session; ?>

<!-- Global Modal: Create Ad Campaign -->
<div id="create-ad-modal" class="modal-overlay hidden">
    <div class="modal-card">
        <div class="flex items-center justify-between p-6 border-b" style="border-color: var(--border-color);">
            <h3 class="text-xl font-bold tracking-tight">Create New Campaign</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-primary transition-colors p-2"><i class="ph ph-x text-2xl"></i></button>
        </div>
        <form id="create-ad-form" class="flex flex-col overflow-hidden h-full">
            <input type="hidden" name="id" id="ad-edit-id">
            
            <!-- Scrollable Body -->
            <div class="p-6 space-y-4 overflow-y-auto flex-grow custom-scrollbar">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Campaign Reference Name</label>
                    <input type="text" name="name" required class="w-full bg-transparent border rounded-2xl p-4 focus:outline-none focus:border-primary transition-all font-medium" style="border-color: var(--border-color); color: var(--text-main);" placeholder="e.g. Winter Sale 2026">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Strategy Type</label>
                        <select name="type" required class="w-full bg-transparent border rounded-2xl p-4 focus:outline-none focus:border-primary transition-all font-medium" style="border-color: var(--border-color); color: var(--text-main);">
                            <option value="Social">Social Broad</option>
                            <option value="Search">Search Target</option>
                            <option value="Display">Visual Display</option>
                            <option value="Email">Email Outreach</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Daily Budget ($)</label>
                        <input type="number" step="0.01" name="budget" required class="w-full bg-transparent border rounded-2xl p-4 font-medium focus:outline-none focus:border-primary transition-all" style="border-color: var(--border-color); color: var(--text-main);">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Launch Date</label>
                        <input type="date" name="start_date" required class="w-full bg-transparent border rounded-2xl p-4 font-medium focus:outline-none" style="border-color: var(--border-color); color: var(--text-main);">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">End Date</label>
                        <input type="date" name="end_date" required class="w-full bg-transparent border rounded-2xl p-4 font-medium focus:outline-none" style="border-color: var(--border-color); color: var(--text-main);">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Media Injection (Ads Code)</label>
                    <textarea name="ad_code" class="w-full bg-transparent border rounded-2xl p-4 font-mono text-xs focus:outline-none focus:border-primary transition-all resize-none" rows="12" style="border-color: var(--border-color); color: var(--text-main);" placeholder="<!-- Paste your ad scripts here -->"></textarea>
                </div>
            </div>

            <!-- Fixed Footer -->
            <div class="p-6 border-t flex gap-3" style="border-color: var(--border-color);">
                <button type="button" onclick="closeModal()" class="btn-secondary flex-1 !justify-center py-4">Cancel</button>
                <button type="submit" id="ad-submit-btn" class="btn-primary flex-1 !justify-center py-4 text-lg">Save Campaign</button>
            </div>
        </form>
    </div>
</div>

<!-- Add more global modals here as needed -->
