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
                <?php
                $db = \Aether\Database::getConnection();
                $roles = $db->query("SELECT * FROM roles ORDER BY name ASC")->fetchAll();
                ?>
                <select name="role" required class="w-full bg-transparent border rounded-2xl p-4 font-bold focus:outline-none focus:border-primary transition-all text-xs uppercase tracking-widest" style="border-color: var(--border-color); color: var(--text-main);">
                    <?php if (empty($roles)): ?>
                        <option value="">No roles available</option>
                    <?php else: ?>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
    <div class="modal-card !max-w-4xl shadow-2xl transition-colors duration-500" style="background-color: var(--surface); border-color: var(--border-color);">
        <div class="flex items-center justify-between p-6 border-b transition-colors" style="border-color: var(--border-color); background-color: var(--glass-bg);">
            <div class="flex items-center gap-3">
                <div class="flex gap-1.5 opacity-80">
                    <div class="w-2.5 h-2.5 rounded-full bg-red-500/60 shadow-[0_0_8px_#ef4444]"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/60 shadow-[0_0_8px_#eab308]"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-green-500/60 shadow-[0_0_8px_#22c55e]"></div>
                </div>
                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] ml-6 opacity-60">Aether Kernel Console</h3>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-primary transition-all p-2 hover:scale-110"><i class="ph ph-x text-2xl"></i></button>
        </div>
        <div class="p-10 font-mono text-sm min-h-[450px] flex flex-col transition-all duration-500" style="background-color: var(--bg-main);">
            <div id="terminal-output" class="flex-grow space-y-2 overflow-y-auto custom-scrollbar pr-4">
                <!-- CLI output injected by JS -->
            </div>
            <div class="flex items-center gap-4 mt-8 border-t pt-8 transition-colors" style="border-color: var(--border-color);">
                <span class="text-primary font-black tracking-tight">root@aether:~#</span>
                <input type="text" 
                       class="bg-transparent border-none outline-none flex-grow font-black tracking-wide" 
                       style="color: var(--text-main);"
                       placeholder="Enter system command..." autofocus>
            </div>
        </div>
    </div>
</div>

<!-- Role Editor Modal (Global Registry) -->
<div id="role-editor-modal" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4 sm:p-6 backdrop-blur-2xl bg-black/60 overflow-hidden" onclick="if(event.target === this) closeModal()">
    <div class="glass-card w-full max-w-2xl max-h-[90vh] flex flex-col animate-in zoom-in duration-300 shadow-[0_30px_100px_rgba(0,0,0,0.5)] overflow-hidden !p-0">

        <div class="p-6 border-b border-white/5 flex items-center justify-between bg-white/5">
            <h3 class="text-xl font-black uppercase tracking-tight">Security <span class="gradient-text">Architect</span></h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-lg hover:bg-white/10 flex items-center justify-center transition-colors"><i class="ph ph-x text-xl"></i></button>
        </div>
        <form id="save-role-form" class="flex flex-col h-full overflow-hidden">
            <div class="p-8 space-y-8 flex-grow overflow-y-auto custom-scrollbar">

                <input type="hidden" name="role_id" value="">
                
                <div class="space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-500">Security Blueprints</label>
                            <span class="text-[8px] font-black uppercase tracking-[0.2em] opacity-30">Select a base protocol</span>
                        </div>
                        <div class="flex flex-wrap gap-2 pb-2">
                            <button type="button" data-blueprint="moderator" onclick="AdminApp.applyBlueprint('moderator')" class="blueprint-chip px-3 py-2 rounded-xl border border-white/5 bg-white/5 text-[10px] font-black uppercase tracking-tight hover:border-primary/50 transition-all">Moderator</button>
                            <button type="button" data-blueprint="curator" onclick="AdminApp.applyBlueprint('curator')" class="blueprint-chip px-3 py-2 rounded-xl border border-white/5 bg-white/5 text-[10px] font-black uppercase tracking-tight hover:border-primary/50 transition-all">Curator</button>
                            <button type="button" data-blueprint="analyst" onclick="AdminApp.applyBlueprint('analyst')" class="blueprint-chip px-3 py-2 rounded-xl border border-white/5 bg-white/5 text-[10px] font-black uppercase tracking-tight hover:border-primary/50 transition-all">Analyst</button>
                            <button type="button" data-blueprint="security" onclick="AdminApp.applyBlueprint('security')" class="blueprint-chip px-3 py-2 rounded-xl border border-white/5 bg-white/5 text-[10px] font-black uppercase tracking-tight hover:border-primary/50 transition-all">Architect</button>
                            <button type="button" data-blueprint="support" onclick="AdminApp.applyBlueprint('support')" class="blueprint-chip px-3 py-2 rounded-xl border border-white/5 bg-white/5 text-[10px] font-black uppercase tracking-tight hover:border-primary/50 transition-all">Support</button>
                            <button type="button" data-blueprint="developer" onclick="AdminApp.applyBlueprint('developer')" class="blueprint-chip px-3 py-2 rounded-xl border border-amber-500/20 bg-amber-500/5 text-amber-500 text-[10px] font-black uppercase tracking-tight hover:bg-amber-500/10 transition-all">Developer</button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Cluster Designation</label>
                        <input type="text" name="role_name" placeholder="E.g. Content Moderator, Analyst" required
                            class="w-full bg-white/5 border border-white/5 rounded-xl py-4 px-6 outline-none focus:border-primary/50 transition-all font-bold text-sm">
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <button type="button" onclick="AdminApp.toggleAllPermissions('view')" class="text-[9px] font-black uppercase tracking-widest text-primary hover:underline">All Read</button>
                        <button type="button" onclick="AdminApp.toggleAllPermissions('manage')" class="text-[9px] font-black uppercase tracking-widest text-green-500 hover:underline">All Write</button>
                        <button type="button" onclick="AdminApp.toggleAllPermissions('delete')" class="text-[9px] font-black uppercase tracking-widest text-red-500 hover:underline">All Purge</button>
                        <div class="h-4 w-px bg-white/10 mx-2"></div>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <span class="text-[9px] font-black uppercase tracking-widest opacity-40 group-hover:opacity-100 transition-opacity">Absolute Root Access</span>
                            <input type="checkbox" name="perms[all]" onchange="AdminApp.toggleMasterAccess(this)" class="w-4 h-4 rounded border-white/10 bg-white/5 text-primary focus:ring-primary">
                        </label>
                    </div>
                </div>

                <div id="privilege-matrix" class="grid grid-cols-1 gap-3 overflow-y-auto pr-2 custom-scrollbar">

                    <?php
                    $sections = [
                        'dashboard' => 'Ecosystem Overview',
                        'users' => 'Identity Vault',
                        'messages' => 'Moderation Cluster',
                        'channels' => 'Data Hubs',
                        'ads' => 'Marketing Streams',
                        'reports' => 'Governance Alerts',
                        'deletion' => 'Identity Erasure',
                        'analytics' => 'Intelligence Data',
                        'policy' => 'Ecosystem Laws',
                        'settings' => 'Protocol Control',
                        'logs' => 'System Telemetry',
                        'roles' => 'Security Governance'
                    ];

                    foreach ($sections as $key => $label):
                    ?>
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/5 flex items-center justify-between group hover:border-primary/20 transition-all">
                        <div>
                            <p class="text-xs font-black uppercase tracking-tight"><?= $label ?></p>
                            <p class="text-[9px] font-bold opacity-40 uppercase tracking-widest mt-0.5"><?= $key ?>.proto</p>
                        </div>
                        <div class="flex gap-4">
                            <label class="permission-toggle">
                                <input type="checkbox" name="perms[<?= $key ?>][view]" class="hidden peer">
                                <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg border border-white/5 opacity-30 peer-checked:opacity-100 peer-checked:border-primary/40 peer-checked:bg-primary/10 peer-checked:text-primary transition-all cursor-pointer">Read</span>
                            </label>
                            <label class="permission-toggle">
                                <input type="checkbox" name="perms[<?= $key ?>][manage]" class="hidden peer">
                                <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg border border-white/5 opacity-30 peer-checked:opacity-100 peer-checked:border-green-500/40 peer-checked:bg-green-500/10 peer-checked:text-green-500 transition-all cursor-pointer">Write</span>
                            </label>
                            <label class="permission-toggle">
                                <input type="checkbox" name="perms[<?= $key ?>][delete]" class="hidden peer">
                                <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg border border-white/5 opacity-30 peer-checked:opacity-100 peer-checked:border-red-500/40 peer-checked:bg-red-500/10 peer-checked:text-red-500 transition-all cursor-pointer">Purge</span>
                            </label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="p-8 border-t border-white/5 bg-white/5 flex gap-4">
                <button type="button" onclick="closeModal()" class="btn-secondary flex-1 !justify-center">Cancel</button>
                <button type="submit" class="btn-primary flex-1 !justify-center shadow-xl shadow-primary/20">
                    <i class="ph-bold ph-shield-check"></i>
                    Seal Protocol
                </button>
            </div>
        </form>
    </div>
</div>
