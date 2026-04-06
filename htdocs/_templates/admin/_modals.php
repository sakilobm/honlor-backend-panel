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

<!-- Global Modal: Custom Range Picker -->
<div id="custom-range-modal" class="modal-overlay hidden">
    <div class="modal-card !max-w-md">
        <div class="flex items-center justify-between p-6 border-b" style="border-color: var(--border-color);">
            <h3 class="text-xl font-bold tracking-tight">Timeline Selection</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-primary transition-colors p-2"><i class="ph ph-x text-2xl"></i></button>
        </div>
        <form class="p-6 space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Start Date</label>
                <input type="date" name="start" required class="w-full bg-transparent border rounded-2xl p-4 font-medium focus:outline-none focus:border-primary transition-all" style="border-color: var(--border-color); color: var(--text-main);">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">End Date</label>
                <input type="date" name="end" required class="w-full bg-transparent border rounded-2xl p-4 font-medium focus:outline-none focus:border-primary transition-all" style="border-color: var(--border-color); color: var(--text-main);">
            </div>
            <button type="button" onclick="AdminApp.initDashboard(30); closeModal();" class="w-full btn-primary !justify-center py-4 text-xs font-black uppercase tracking-widest">Apply Range</button>
        </form>
    </div>
</div>

<!-- Global Modal: Invite User -->
<div id="invite-user-modal" class="modal-overlay hidden">
    <div class="modal-card !max-w-md">
        <div class="flex items-center justify-between p-6 border-b" style="border-color: var(--border-color);">
            <h3 class="text-xl font-bold tracking-tight">Identity Invitation</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-primary transition-colors p-2"><i class="ph ph-x text-2xl"></i></button>
        </div>
        <form class="p-6 space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">E-mail Address</label>
                <input type="email" name="email" required placeholder="agent@aether.net" class="w-full bg-transparent border rounded-2xl p-4 font-medium focus:outline-none focus:border-primary transition-all" style="border-color: var(--border-color); color: var(--text-main);">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Access Authorization</label>
                <select name="role" required class="w-full bg-transparent border rounded-2xl p-4 font-bold focus:outline-none focus:border-primary transition-all text-xs uppercase tracking-widest" style="border-color: var(--border-color); color: var(--text-main);">
                    <option value="agent">Moderation Agent</option>
                    <option value="admin">System Administrator</option>
                    <option value="marketing">Marketing Specialist</option>
                </select>
            </div>
            <button type="button" onclick="toast.success('Invitation Sent', 'Security link delivered to recipient.'); closeModal();" class="w-full btn-primary !justify-center py-4 text-xs font-black uppercase tracking-widest">Authorize & Deliver</button>
        </form>
    </div>
</div>
<!-- Global Modal: Policy Preview -->
<div id="policy-preview-modal" class="modal-overlay hidden">
    <div class="modal-card !max-w-3xl">
        <div class="flex items-center justify-between p-8 border-b" style="border-color: var(--border-color);">
            <div>
                <h3 class="text-xl font-bold">Protocol Preview</h3>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-1">Live Governance Draft</p>
            </div>
            <button onclick="closeModal()" class="w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center hover:bg-white/10 transition-colors">
                <i class="ph-bold ph-x text-lg"></i>
            </button>
        </div>
        <div class="p-10 overflow-y-auto custom-scrollbar max-h-[60vh]">
            <article id="policy-preview-content" class="prose prose-invert max-w-none text-sm font-medium leading-relaxed" style="color: var(--text-main);">
                <!-- Content injected by JS -->
            </article>
        </div>
        <div class="p-8 bg-white/5 border-t flex justify-end gap-3" style="border-color: var(--border-color);">
            <button onclick="closeModal()" class="btn-secondary">Dismiss</button>
            <button onclick="closeModal(); AdminApp.submitPolicy();" class="btn-primary !px-8">
                <i class="ph-bold ph-rocket-launch"></i>
                Deploy Guidelines
            </button>
        </div>
    </div>
</div>
<!-- Global Modal: System Terminal -->
<div id="system-terminal-modal" class="modal-overlay hidden">
    <div class="modal-card !max-w-4xl !bg-black shadow-[0_0_50px_rgba(124,106,255,0.15)]">
        <div class="flex items-center justify-between p-6 border-b border-white/10 bg-white/5">
            <div class="flex items-center gap-3">
                <div class="flex gap-1.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-red-500/50"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/50"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-green-500/50"></div>
                </div>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400 ml-4">Aether Kernel Console</h3>
            </div>
            <button onclick="closeModal()" class="text-gray-500 hover:text-white transition-colors p-2"><i class="ph ph-x text-xl"></i></button>
        </div>
        <div class="p-10 font-mono text-sm min-h-[400px] bg-slate-950/50 flex flex-col">
            <div id="terminal-output" class="flex-grow space-y-1">
                <!-- CLI output injected by JS -->
            </div>
            <div class="flex items-center gap-3 mt-6 border-t border-white/5 pt-6">
                <span class="text-primary font-black">root@aether:~#</span>
                <input type="text" class="bg-transparent border-none outline-none flex-grow text-white font-medium" placeholder="Enter system command..." autofocus>
            </div>
        </div>
    </div>
</div>
