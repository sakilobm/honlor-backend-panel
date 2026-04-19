<?php use Aether\Session; ?>

<!-- Global Modal: Create Ad Campaign -->
<div id="create-ad-modal" class="modal-overlay hidden">
    <div class="modal-card">
        <div class="flex items-center justify-between p-6 border-b" style="border-color: var(--border-color);">
            <h3 class="text-xl font-bold tracking-tight">Create New Campaign</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-primary transition-colors p-2"><i
                    class="ph ph-x text-2xl"></i></button>
        </div>
        <form id="create-ad-form" class="flex flex-col overflow-hidden h-full">
            <input type="hidden" name="id" id="ad-edit-id">

            <!-- Scrollable Body -->
            <div class="p-6 space-y-4 overflow-y-auto flex-grow custom-scrollbar">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Campaign Reference
                        Name</label>
                    <input type="text" name="name" required
                        class="w-full bg-transparent border rounded-2xl p-4 focus:outline-none focus:border-primary transition-all font-medium"
                        style="border-color: var(--border-color); color: var(--text-main);"
                        placeholder="e.g. Winter Sale 2026">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Strategy
                            Type</label>
                        <select name="type" required
                            class="w-full bg-transparent border rounded-2xl p-4 focus:outline-none focus:border-primary transition-all font-medium"
                            style="border-color: var(--border-color); color: var(--text-main);">
                            <option value="Social">Social Broad</option>
                            <option value="Search">Search Target</option>
                            <option value="Display">Visual Display</option>
                            <option value="Email">Email Outreach</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Daily Budget
                            ($)</label>
                        <input type="number" step="0.01" name="budget" required
                            class="w-full bg-transparent border rounded-2xl p-4 font-medium focus:outline-none focus:border-primary transition-all"
                            style="border-color: var(--border-color); color: var(--text-main);">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Launch Date</label>
                        <input type="date" name="start_date" required
                            class="w-full bg-transparent border rounded-2xl p-4 font-medium focus:outline-none"
                            style="border-color: var(--border-color); color: var(--text-main);">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">End Date</label>
                        <input type="date" name="end_date" required
                            class="w-full bg-transparent border rounded-2xl p-4 font-medium focus:outline-none"
                            style="border-color: var(--border-color); color: var(--text-main);">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Media Injection (Ads
                        Code)</label>
                    <textarea name="ad_code"
                        class="w-full bg-transparent border rounded-2xl p-4 font-mono text-xs focus:outline-none focus:border-primary transition-all resize-none"
                        rows="12" style="border-color: var(--border-color); color: var(--text-main);"
                        placeholder="<!-- Paste your ad scripts here -->"></textarea>
                </div>
            </div>

            <!-- Fixed Footer -->
            <div class="p-6 border-t flex gap-3" style="border-color: var(--border-color);">
                <button type="button" onclick="closeModal()"
                    class="btn-secondary flex-1 !justify-center py-4">Cancel</button>
                <button type="submit" id="ad-submit-btn" class="btn-primary flex-1 !justify-center py-4 text-lg">Save
                    Campaign</button>
            </div>
        </form>
    </div>
</div>

<!-- Global Modal: Custom Range Picker -->
<div id="custom-range-modal" class="modal-overlay hidden">
    <div class="modal-card !max-w-md">
        <div class="flex items-center justify-between p-6 border-b" style="border-color: var(--border-color);">
            <h3 class="text-xl font-bold tracking-tight">Timeline Selection</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-primary transition-colors p-2"><i
                    class="ph ph-x text-2xl"></i></button>
        </div>
        <form class="p-6 space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Start Date</label>
                <input type="date" name="start" required
                    class="w-full bg-transparent border rounded-2xl p-4 font-medium focus:outline-none focus:border-primary transition-all"
                    style="border-color: var(--border-color); color: var(--text-main);">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">End Date</label>
                <input type="date" name="end" required
                    class="w-full bg-transparent border rounded-2xl p-4 font-medium focus:outline-none focus:border-primary transition-all"
                    style="border-color: var(--border-color); color: var(--text-main);">
            </div>
            <button type="button" onclick="AdminApp.initDashboard(30); closeModal();"
                class="w-full btn-primary !justify-center py-4 text-xs font-black uppercase tracking-widest">Apply
                Range</button>
        </form>
    </div>
</div>

<!-- Global Modal: Invite User -->
<div id="invite-user-modal" class="modal-overlay hidden">
    <div class="modal-card !max-w-md !overflow-visible">
        <div class="flex items-center justify-between p-6 border-b" style="border-color: var(--border-color);">
            <h3 class="text-xl font-bold tracking-tight">Identity Invitation</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-primary transition-colors p-2"><i
                    class="ph ph-x text-2xl"></i></button>
        </div>
        <form class="p-6 space-y-6 !overflow-visible">
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">E-mail Address</label>
                <input type="email" name="email" required placeholder="agent@aether.net"
                    class="w-full bg-transparent border rounded-2xl p-4 font-medium focus:outline-none focus:border-primary transition-all"
                    style="border-color: var(--border-color); color: var(--text-main);">
            </div>
            <div class="space-y-4">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Access
                    Authorization</label>
                <?php
                $db = \Aether\Database::getConnection();
                $roles_inv = $db->query("SELECT * FROM roles ORDER BY name ASC")->fetchAll();
                ?>
                <div class="relative">
                    <!-- Hidden State Registry -->
                    <input type="hidden" id="invite-role-id" name="role_id" value="0">

                    <!-- Premium Trigger Button -->
                    <button type="button" id="invite-role-trigger"
                        onclick="event.stopPropagation(); AdminApp.toggleInviteRoleDropdown()"
                        class="w-full bg-white/5 border border-white/5 rounded-3xl p-5 pr-12 flex items-center justify-between group focus:border-primary/50 focus:ring-4 focus:ring-primary/5 transition-all shadow-inner relative overflow-hidden">
                        <div class="flex items-center gap-3">
                            <i class="ph-bold ph-shield-check text-primary text-lg"></i>
                            <span id="invite-role-display-name"
                                class="font-black text-[10px] uppercase tracking-[0.1em] text-slate-700 dark:text-slate-200">Select
                                Security Cluster</span>
                        </div>
                        <i class="ph-bold ph-caret-down text-slate-400 group-hover:text-primary transition-colors text-lg"
                            id="invite-role-chevron"></i>
                    </button>

                    <!-- Glassmorphic Options Menu -->
                    <div id="invite-role-menu"
                        class="hidden absolute top-full left-0 w-full mt-3 z-[110] glass-card !p-3 shadow-[0_25px_70px_rgba(0,0,0,0.6)] border-white/10 animate-in fade-in slide-in-from-top-4 duration-300">
                        <ul class="space-y-1 max-h-[220px] overflow-y-auto custom-scrollbar pr-1">
                            <?php foreach ($roles_inv as $ri): ?>
                                <li>
                                    <button type="button"
                                        onclick="AdminApp.selectInviteRole(<?= $ri['id'] ?>, '<?= htmlspecialchars($ri['name']) ?>')"
                                        class="w-full text-left p-4 rounded-2xl hover:bg-primary/5 flex items-center gap-4 group transition-all">
                                        <div
                                            class="w-2 h-2 rounded-full bg-slate-500 group-hover:bg-primary transition-colors">
                                        </div>
                                        <span
                                            class="text-[10px] font-black text-slate-400 group-hover:text-primary uppercase tracking-wider"><?= htmlspecialchars($ri['name']) ?></span>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <button type="button"
                onclick="toast.success('Invitation Sent', 'Security link delivered to recipient.'); closeModal();"
                class="w-full btn-primary !justify-center py-4 text-xs font-black uppercase tracking-widest">Authorize &
                Deliver</button>
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
            <button onclick="closeModal()"
                class="w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center hover:bg-white/10 transition-colors">
                <i class="ph-bold ph-x text-lg"></i>
            </button>
        </div>
        <div class="p-10 overflow-y-auto custom-scrollbar max-h-[60vh]">
            <article id="policy-preview-content"
                class="prose prose-invert max-w-none text-sm font-medium leading-relaxed"
                style="color: var(--text-main);">
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
    <div class="modal-card !max-w-4xl shadow-2xl transition-colors duration-500"
        style="background-color: var(--surface); border-color: var(--border-color);">
        <div class="flex items-center justify-between p-6 border-b transition-colors"
            style="border-color: var(--border-color); background-color: var(--glass-bg);">
            <div class="flex items-center gap-3">
                <div class="flex gap-1.5 opacity-80">
                    <div class="w-2.5 h-2.5 rounded-full bg-red-500/60 shadow-[0_0_8px_#ef4444]"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/60 shadow-[0_0_8px_#eab308]"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-green-500/60 shadow-[0_0_8px_#22c55e]"></div>
                </div>
                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] ml-6 opacity-60">Aether Kernel Console</h3>
            </div>
            <button onclick="closeModal()"
                class="text-gray-400 hover:text-primary transition-all p-2 hover:scale-110"><i
                    class="ph ph-x text-2xl"></i></button>
        </div>
        <div class="p-10 font-mono text-sm min-h-[450px] flex flex-col transition-all duration-500"
            style="background-color: var(--bg-main);">
            <div id="terminal-output" class="flex-grow space-y-2 overflow-y-auto custom-scrollbar pr-4">
                <!-- CLI output injected by JS -->
            </div>
            <div class="flex items-center gap-4 mt-8 border-t pt-8 transition-colors"
                style="border-color: var(--border-color);">
                <span class="text-primary font-black tracking-tight">root@aether:~#</span>
                <input type="text" class="bg-transparent border-none outline-none flex-grow font-black tracking-wide"
                    style="color: var(--text-main);" placeholder="Enter system command..." autofocus>
            </div>
        </div>
    </div>
</div>

<!-- Role Editor Modal (Global Registry) -->
<div id="role-editor-modal"
    class="fixed inset-0 z-[1000] hidden items-center justify-center p-4 sm:p-6 backdrop-blur-3xl bg-black/60 overflow-hidden"
    onclick="if(event.target === this) closeModal()">
    <div
        class="glass-card w-full max-w-4xl max-h-[95vh] flex flex-col animate-in zoom-in duration-500 shadow-[0_40px_100px_rgba(0,0,0,0.8)] overflow-hidden !p-0 border-white/5 bg-gradient-to-b from-white/5 to-transparent">

        <div class="p-8 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
            <div class="flex items-center gap-5">
                <div
                    class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary text-2xl shadow-[0_0_20px_rgba(124,106,255,0.1)] border border-primary/20">
                    <i class="ph-bold ph-shield-chevron"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black uppercase tracking-tighter">Security <span
                            class="text-primary italic">Orchestrator</span></h3>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Define Identity Protocol &
                        Authority Blueprints</p>
                </div>
            </div>
            <button onclick="closeModal()"
                class="w-12 h-12 rounded-2xl hover:bg-white/10 flex items-center justify-center transition-all hover:scale-110 active:scale-95"><i
                    class="ph ph-x text-2xl"></i></button>
        </div>

        <form id="save-role-form" class="flex flex-col h-full overflow-hidden"
            onsubmit="event.preventDefault(); AdminApp.saveRole();">
            <div class="p-8 space-y-10 flex-grow overflow-y-auto custom-scrollbar">

                <input type="hidden" name="role_id" value="">

                <!-- Identification & Authority Cluster -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500">Access
                            Identity</label>
                        <input type="text" name="role_name" placeholder="e.g. System Architect" required
                            class="w-full bg-white/5 border border-white/5 rounded-3xl py-6 px-8 outline-none focus:border-primary/50 transition-all font-black text-xl uppercase tracking-tight placeholder:opacity-20 shadow-inner">
                    </div>

                    <div class="space-y-4 pt-1">
                        <div class="flex justify-between items-center px-1">
                            <label class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500">Authority
                                Distribution</label>
                            <span id="authority-marker"
                                class="text-[9px] font-black px-2 py-0.5 bg-primary/20 text-primary border border-primary/40 rounded-lg uppercase tracking-widest leading-none">Restricted</span>
                        </div>
                        <div
                            class="h-3 bg-white/5 rounded-2xl overflow-hidden p-0.5 border border-white/5 shadow-inner">
                            <div id="authority-meter"
                                class="h-full bg-primary transition-all duration-700 w-0 rounded-full shadow-[0_0_15px_#7c6aff]">
                            </div>
                        </div>
                        <div class="flex justify-between items-center opacity-30">
                            <span class="text-[8px] font-black uppercase tracking-widest italic">L2 Protocol</span>
                            <span class="text-[8px] font-black uppercase tracking-widest italic">Absolute Root
                                (L0)</span>
                        </div>
                    </div>
                </div>

                <!-- Global Override & Blueprints -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-500">Security
                            Blueprints (Quick Start)</label>
                        <div class="flex flex-wrap gap-2">
                            <?php
                            $prototypes = ['moderator' => 'Moderator', 'curator' => 'Curator', 'analyst' => 'Analyst', 'security' => 'Architect', 'support' => 'Support', 'developer' => 'Developer'];
                            foreach ($prototypes as $key => $label): ?>
                                <button type="button" onclick="AdminApp.applyBlueprint('<?= $key ?>')"
                                    class="px-5 py-3 rounded-2xl border border-white/10 bg-white/5 text-[10px] font-black uppercase tracking-tight hover:border-primary/50 hover:bg-primary/5 transition-all"><?= $label ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div
                        class="glass-card !p-5 border-amber-500/10 bg-amber-500/[0.02] flex items-center justify-between group overflow-hidden relative">
                        <div
                            class="absolute inset-x-0 bottom-0 h-[2px] bg-amber-500/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                        </div>
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 text-lg">
                                <i class="ph-fill ph-shield-star"></i>
                            </div>
                            <div class="text-left">
                                <h4
                                    class="font-black text-gray-500 dark:text-white uppercase tracking-tight text-[11px]">
                                    Perfect Authority</h4>
                                <p class="text-[8px] text-gray-500 font-bold uppercase tracking-tight">System-Wide
                                    Override</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="perms[all]" onchange="AdminApp.toggleMasterAccess(this)"
                                class="sr-only peer">
                            <div
                                class="w-12 h-6 bg-white/5 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-gray-400 after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500">
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Permission Cluster Matrix -->
                <div id="privilege-matrix"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 transition-all duration-500">
                    <?php
                    $clusters = [
                        'Identity' => [
                            'icon' => 'ph-identification-badge',
                            'items' => ['users' => 'Vault', 'roles' => 'Governance']
                        ],
                        'Broadcast' => [
                            'icon' => 'ph-megaphone',
                            'items' => ['ads' => 'Streams', 'channels' => 'Data Hubs']
                        ],
                        'Intelligence' => [
                            'icon' => 'ph-chart-bar',
                            'items' => ['analytics' => 'Telemetry', 'logs' => 'Audit Trails']
                        ],
                        'Infrastructure' => [
                            'icon' => 'ph-gear',
                            'items' => ['settings' => 'Protocols', 'policy' => 'Ecosystem Laws']
                        ],
                        'Moderation' => [
                            'icon' => 'ph-shield-check',
                            'items' => ['messages' => 'Dialogues', 'reports' => 'Grievances']
                        ],
                        'Security' => [
                            'icon' => 'ph-lock-key',
                            'items' => ['deletion' => 'Erasure', 'login' => 'Access']
                        ]
                    ];

                    foreach ($clusters as $name => $data):
                        ?>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 opacity-40">
                                <i class="ph-bold <?= $data['icon'] ?> text-lg"></i>
                                <h4 class="text-[10px] font-black uppercase tracking-[0.2em]"><?= $name ?> Systems</h4>
                            </div>
                            <div class="space-y-3">
                                <?php foreach ($data['items'] as $key => $label): ?>
                                    <div
                                        class="p-5 rounded-3xl bg-white/[0.03] border border-white/5 flex flex-col gap-4 group hover:border-primary/40 transition-all">
                                        <span
                                            class="text-[10px] font-black text-gray-500 uppercase tracking-widest leading-none"><?= $label ?></span>
                                        <div class="flex flex-wrap gap-2">
                                            <?php
                                            $actionMeta = [
                                                'view' => ['label' => 'Audit', 'color' => 'peer-checked:bg-primary/20 peer-checked:border-primary/40 peer-checked:text-primary'],
                                                'manage' => ['label' => 'Write', 'color' => 'peer-checked:bg-green-500/20 peer-checked:border-green-500/40 peer-checked:text-green-500'],
                                                'delete' => ['label' => 'Purge', 'color' => 'peer-checked:bg-red-500/20 peer-checked:border-red-500/40 peer-checked:text-red-500']
                                            ];
                                            foreach ($actionMeta as $action => $meta): ?>
                                                <label class="flex-1">
                                                    <input type="checkbox" name="perms[<?= $key ?>][<?= $action ?>]"
                                                        onchange="AdminApp.updateAuthorityMeter()" class="hidden peer">
                                                    <div
                                                        class="text-center p-2 rounded-xl border border-white/5 bg-white/5 text-[8px] font-black uppercase tracking-widest cursor-pointer hover:bg-white/10 <?= $meta['color'] ?> transition-all shadow-sm">
                                                        <?= $meta['label'] ?>
                                                    </div>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-8 border-t border-white/5 bg-white/[0.05] flex gap-5">
                <button type="button" onclick="closeModal()"
                    class="btn-secondary flex-1 !rounded-3xl !py-5 uppercase font-black tracking-widest text-xs">Abort
                    Protocol</button>
                <button type="submit"
                    class="btn-primary flex-1 !rounded-3xl !py-5 uppercase font-black tracking-widest text-xs shadow-2xl shadow-primary/30">
                    <i class="ph-bold ph-shield-check ml-[-4px] mr-1"></i>
                    Activate Blueprint
                </button>
            </div>
        </form>
    </div>
</div>
<!-- Handshake Authorization Modal (Isolated Registry) -->
<div id="handshake-authorize-modal" class="modal-overlay hidden">
    <div class="glass-card !max-w-md !overflow-visible">
        <div class="flex items-center justify-between p-6 border-b" style="border-color: var(--border-color);">
            <h3 class="text-xl font-bold tracking-tight">Handshake Authorization</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-primary transition-colors p-2"><i
                    class="ph ph-x text-2xl"></i></button>
        </div>
        <form class="p-6 space-y-6 !overflow-visible"
            onsubmit="event.preventDefault(); AdminApp.confirmHandshakeAuthorization();">
            <input type="hidden" id="auth-request-user-id">
            <div class="space-y-4">
                <label class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500">Security Cluster
                    Assignment</label>
                <?php
                $db = \Aether\Database::getConnection();
                $roles_list = $db->query("SELECT * FROM roles ORDER BY name ASC")->fetchAll();
                ?>
                <div class="relative">
                    <!-- Hidden State Registry -->
                    <input type="hidden" id="auth-request-role-id" value="0">

                    <!-- Premium Trigger Button -->
                    <button type="button" onclick="event.stopPropagation(); AdminApp.toggleHandshakeRoleDropdown()"
                        class="w-full bg-white/5 border border-white/5 rounded-3xl p-6 pr-12 flex items-center justify-between group focus:border-primary/50 focus:ring-4 focus:ring-primary/5 transition-all shadow-inner relative overflow-hidden">
                        <div class="flex items-center gap-3">
                            <i class="ph-bold ph-shield-chevron text-primary text-lg"></i>
                            <span id="auth-role-display-name"
                                class="font-black text-xs uppercase tracking-[0.1em] text-slate-700 dark:text-slate-200">---
                                Default (Observer Cluster) ---</span>
                        </div>
                        <i class="ph-bold ph-caret-down text-slate-400 group-hover:text-primary transition-colors text-lg"
                            id="auth-role-chevron"></i>
                    </button>

                    <!-- Glassmorphic Options Menu -->
                    <div id="handshake-role-menu"
                        class="hidden absolute top-full left-0 w-full mt-3 z-[110] glass-card !p-3 shadow-[0_25px_70px_rgba(0,0,0,0.6)] border-white/10 animate-in fade-in slide-in-from-top-4 duration-300">
                        <div class="px-5 py-3 mb-2">
                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-[0.25em]">Identity
                                Prototypes</p>
                        </div>
                        <ul class="space-y-1 max-h-[220px] overflow-y-auto custom-scrollbar pr-1">
                            <li>
                                <button type="button"
                                    onclick="AdminApp.selectHandshakeRole(0, 'Default (Observer Cluster)')"
                                    class="w-full text-left p-4 rounded-2xl hover:bg-primary/5 flex items-center gap-4 group transition-all">
                                    <div
                                        class="w-2 h-2 rounded-full bg-slate-500 group-hover:bg-primary transition-colors">
                                    </div>
                                    <span
                                        class="text-[11px] font-black text-slate-400 group-hover:text-primary uppercase tracking-wider">---
                                        Default (Observer) ---</span>
                                </button>
                            </li>
                            <div class="h-[1px] bg-white/5 mx-4 my-2"></div>
                            <?php foreach ($roles_list as $rl): ?>
                                <li>
                                    <button type="button"
                                        onclick="AdminApp.selectHandshakeRole(<?= $rl['id'] ?>, '<?= htmlspecialchars($rl['name']) ?>')"
                                        class="w-full text-left p-4 rounded-2xl hover:bg-primary/5 flex items-center gap-4 group transition-all">
                                        <div
                                            class="w-2 h-2 rounded-full bg-slate-500 group-hover:bg-primary transition-colors">
                                        </div>
                                        <span
                                            class="text-[11px] font-black text-slate-400 group-hover:text-primary uppercase tracking-wider"><?= htmlspecialchars($rl['name']) ?></span>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <p
                        class="mt-4 text-[9px] font-bold text-slate-500 uppercase tracking-widest px-1 flex items-center gap-2">
                        <i class="ph-fill ph-info text-primary"></i>
                        Baseline read-only access will be granted by default.
                    </p>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" onclick="closeModal()"
                    class="btn-secondary flex-1 !justify-center py-4 text-[10px] font-black uppercase tracking-widest">Abort</button>
                <button type="submit"
                    class="btn-primary flex-1 !justify-center py-4 text-[10px] font-black uppercase tracking-widest shadow-xl shadow-primary/20">Finalize
                    Identity</button>
            </div>
        </form>
    </div>
</div>

<!-- Global Modal: Create Channel Wizard -->
<div id="create-channel-modal" class="modal-overlay hidden">
    <div
        class="modal-card !max-w-4xl w-[95%] md:w-auto !h-auto md:min-h-[600px] !p-0 overflow-hidden flex flex-col md:flex-row bg-[var(--surface)] border-[var(--border-color)] backdrop-blur-3xl shadow-[var(--card-shadow)] m-4 md:m-0">

        <!-- Sidebar Navigation -->
        <div class="w-full md:w-80 bg-[var(--glass-bg)] border-b md:border-b-0 md:border-r border-[var(--border-color)] p-6 md:p-10 flex flex-col">
            <div>
                <h3 class="text-xl md:text-2xl font-black tracking-tighter uppercase mb-6 md:mb-12" style="color: var(--text-main);">Create
                    <span class="gradient-text">Channel</span>
                </h3>

                <div class="flex flex-row md:flex-col justify-between md:justify-start gap-4 md:gap-8 relative">
                    <!-- Progress Line Tracks (Desktop Only) -->
                    <div class="absolute left-6 top-6 bottom-[24px] w-0.5 z-0 hidden md:block">
                        <div class="w-full h-full bg-[var(--border-color)]"></div>
                        <div id="wizard-progress-bar" class="absolute top-0 left-0 w-full bg-primary transition-all duration-500" style="height: 0%"></div>
                    </div>

                    <div class="wizard-step flex flex-col md:flex-row items-center gap-2 md:gap-6 group z-10 relative" data-step="1">
                        <div
                            class="step-num w-10 h-10 md:w-12 md:h-12 rounded-full border-2 border-[var(--border-color)] bg-[var(--surface)] flex items-center justify-center font-black text-xs md:text-sm transition-all duration-300 group-[.active]:border-primary group-[.active]:bg-primary group-[.active]:text-white group-[.active]:shadow-[0_0_20px_rgba(124,106,255,0.4)] group-[.completed]:bg-emerald-500 group-[.completed]:border-emerald-500 group-[.completed]:text-white group-[.completed]:shadow-[0_0_20px_rgba(16,185,129,0.3)] text-gray-400 group-[.active]:!text-white group-[.completed]:!text-white">
                            1</div>
                        <div class="hidden md:block">
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] mb-0.5"
                                style="color: var(--text-muted); opacity: 0.6;">Step 01</p>
                            <p class="font-black text-[11px] uppercase tracking-tight" style="color: var(--text-main);">
                                Channel Type</p>
                        </div>
                    </div>

                    <div class="wizard-step flex flex-col md:flex-row items-center gap-2 md:gap-6 group z-10 relative" data-step="2">
                        <div
                            class="step-num w-10 h-10 md:w-12 md:h-12 rounded-full border-2 border-[var(--border-color)] bg-[var(--surface)] flex items-center justify-center font-black text-xs md:text-sm transition-all duration-300 group-[.active]:border-primary group-[.active]:bg-primary group-[.active]:text-white group-[.completed]:bg-emerald-500 group-[.completed]:border-emerald-500 group-[.completed]:text-white text-gray-400 group-[.active]:!text-white group-[.completed]:!text-white">
                            2</div>
                        <div class="hidden md:block">
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] mb-0.5"
                                style="color: var(--text-muted); opacity: 0.6;">Step 02</p>
                            <p class="font-black text-[11px] uppercase tracking-tight" style="color: var(--text-main);">
                                Basic Info</p>
                        </div>
                    </div>

                    <div class="wizard-step flex flex-col md:flex-row items-center gap-2 md:gap-6 group z-10 relative" data-step="3">
                        <div
                            class="step-num w-10 h-10 md:w-12 md:h-12 rounded-full border-2 border-[var(--border-color)] bg-[var(--surface)] flex items-center justify-center font-black text-xs md:text-sm transition-all duration-300 group-[.active]:border-primary group-[.active]:bg-primary group-[.active]:text-white group-[.completed]:bg-emerald-500 group-[.completed]:border-emerald-500 group-[.completed]:text-white text-gray-400 group-[.active]:!text-white group-[.completed]:!text-white">
                            3</div>
                        <div class="hidden md:block">
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] mb-0.5"
                                style="color: var(--text-muted); opacity: 0.6;">Step 03</p>
                            <p class="font-black text-[11px] uppercase tracking-tight" style="color: var(--text-main);">
                                Add Members</p>
                        </div>
                    </div>

                    <div class="wizard-step flex flex-col md:flex-row items-center gap-2 md:gap-6 group z-10 relative" data-step="4">
                        <div
                            class="step-num w-10 h-10 md:w-12 md:h-12 rounded-full border-2 border-[var(--border-color)] bg-[var(--surface)] flex items-center justify-center font-black text-xs md:text-sm transition-all duration-300 group-[.active]:border-primary group-[.active]:bg-primary group-[.active]:text-white group-[.completed]:bg-emerald-500 group-[.completed]:border-emerald-500 group-[.completed]:text-white text-gray-400 group-[.active]:!text-white group-[.completed]:!text-white">
                            4</div>
                        <div class="hidden md:block">
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] mb-0.5"
                                style="color: var(--text-muted); opacity: 0.6;">Step 04</p>
                            <p class="font-black text-[11px] uppercase tracking-tight" style="color: var(--text-main);">
                                Permissions</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 mx-4 p-6 rounded-[2rem] bg-primary/5 border border-primary/10 mb-8 shadow-xl shadow-primary/[0.04] hidden md:block">
                <p class="text-[9px] font-black uppercase tracking-[0.1em] text-primary mb-2">Quick Tip:</p>
                <p class="text-[10px] font-bold text-gray-500 leading-relaxed uppercase tracking-tight"
                    style="color: var(--text-muted);">Public channels are searchable by everyone in your organization
                    cluster.</p>
            </div>
        </div>

        <!-- Content Area -->
        <div class="flex-grow flex flex-col bg-[var(--surface)]" style="background-color: var(--surface);">
            <div class="flex-grow p-6 md:p-12 overflow-y-auto custom-scrollbar" id="wizard-content">

                <!-- Step 1: Type selection -->
                <div class="wizard-pane active animate-in fade-in slide-in-from-right-8 duration-500" data-step="1">
                    <h2 class="text-2xl md:text-3xl font-black tracking-tight mb-2 uppercase" style="color: var(--text-main);">
                        Select <span class="gradient-text">Channel Type</span></h2>
                    <p class="font-bold text-xs md:text-sm uppercase tracking-tight mb-8 md:mb-12" style="color: var(--text-muted);">Choose
                        how you want your team to interact and discover this space.</p>

                    <div class="space-y-4">
                        <label class="block cursor-pointer">
                            <input type="radio" name="channel_type" value="public" checked class="hidden peer">
                            <div
                                class="p-8 rounded-[2.5rem] border-2 border-[var(--border-color)] bg-[var(--glass-bg)] peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex items-center gap-6 group">
                                <div class="w-16 h-16 rounded-2xl bg-[var(--glass-bg)] flex items-center justify-center group-hover:text-primary peer-checked:text-primary transition-colors"
                                    style="color: var(--text-muted);">
                                    <i class="ph-bold ph-globe text-3xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-black uppercase tracking-tight mb-1"
                                        style="color: var(--text-main);">Public Channel</h4>
                                    <p class="text-[10px] font-bold uppercase tracking-widest"
                                        style="color: var(--text-muted);">Open for anyone in the organization to join
                                        and search. Best for general updates.</p>
                                </div>
                            </div>
                        </label>

                        <label class="block cursor-pointer">
                            <input type="radio" name="channel_type" value="private" class="hidden peer">
                            <div
                                class="p-8 rounded-[2.5rem] border-2 border-[var(--border-color)] bg-[var(--glass-bg)] peer-checked:border-primary peer-checked:bg-primary/5 transition-all flex items-center gap-6 group">
                                <div class="w-16 h-16 rounded-2xl bg-[var(--glass-bg)] flex items-center justify-center group-hover:text-primary transition-colors"
                                    style="color: var(--text-muted);">
                                    <i class="ph-bold ph-lock-key text-3xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-black uppercase tracking-tight mb-1"
                                        style="color: var(--text-main);">Private Channel</h4>
                                    <p class="text-[10px] font-bold uppercase tracking-widest"
                                        style="color: var(--text-muted);">By invitation only. Conversations are
                                        confidential and hidden from search results.</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Step 2: Info -->
                <div class="wizard-pane hidden" data-step="2">
                    <h2 class="text-2xl md:text-3xl font-black tracking-tight mb-2 uppercase" style="color: var(--text-main);">
                        Channel <span class="gradient-text">Identity</span></h2>
                    <p class="font-bold text-xs md:text-sm uppercase tracking-tight mb-8 md:mb-12" style="color: var(--text-muted);">Define
                        the name and purpose of this communication node.</p>

                    <div class="space-y-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] ml-1"
                                style="color: var(--text-muted);">Registry Name</label>
                            <input type="text" id="wizard-name"
                                class="w-full bg-[var(--glass-bg)] border border-[var(--border-color)] rounded-3xl p-6 px-8 outline-none focus:border-primary/50 transition-all font-black text-xl uppercase tracking-tight"
                                style="color: var(--text-main);" placeholder="e.g. CORE-OPERATIONS">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] ml-1"
                                style="color: var(--text-muted);">Namespace Slug</label>
                            <div
                                class="flex items-center bg-[var(--glass-bg)] border border-[var(--border-color)] rounded-3xl p-6 px-8 transition-all">
                                <span class="font-bold text-xs uppercase mr-2 tracking-widest"
                                    style="color: var(--text-muted);">#</span>
                                <input type="text" id="wizard-slug"
                                    class="bg-transparent border-none outline-none flex-grow font-black text-xl uppercase tracking-tight"
                                    style="color: var(--text-main);" placeholder="core-ops">
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] ml-1"
                                style="color: var(--text-muted);">Manifesto (Description)</label>
                            <textarea id="wizard-description"
                                class="w-full bg-[var(--glass-bg)] border border-[var(--border-color)] rounded-3xl p-6 px-8 outline-none focus:border-primary/50 transition-all font-bold text-sm uppercase tracking-tight resize-none"
                                style="color: var(--text-main);" rows="4"
                                placeholder="BRIEF DESCRIPTION OF THIS CLUSTER'S MISSION..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Members -->
                <div class="wizard-pane hidden" data-step="3">
                    <h2 class="text-2xl md:text-3xl font-black tracking-tight mb-2 uppercase" style="color: var(--text-main);">
                        Identity <span class="gradient-text">Linkage</span></h2>
                    <p class="font-bold text-xs md:text-sm uppercase tracking-tight mb-8 md:mb-12" style="color: var(--text-muted);">
                        Authorize agents and assign authority protocols to this cluster.</p>

                    <div class="space-y-8">
                        <div class="relative">
                            <i class="ph-bold ph-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-xl"
                                style="color: var(--text-muted);"></i>
                            <input type="text" id="wizard-user-search" onkeyup="AdminApp.searchWizardUsers(this.value)"
                                class="w-full bg-[var(--glass-bg)] border border-[var(--border-color)] rounded-3xl p-6 pl-16 outline-none focus:border-primary/50 transition-all font-bold text-xs uppercase tracking-widest"
                                style="color: var(--text-main);" placeholder="SEARCH AGENT DATABASE...">
                            <div id="wizard-search-results"
                                class="absolute top-full left-0 w-full mt-2 z-50 glass-card hidden max-h-60 overflow-y-auto custom-scrollbar">
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] ml-1"
                                style="color: var(--text-muted);">Authorized Cluster</label>
                            <div id="wizard-selected-members" class="space-y-2">
                                <!-- Members injected by JS -->
                                <div
                                    class="p-6 rounded-[1.5rem] border border-dashed border-[var(--border-color)] text-center opacity-40">
                                    <p class="font-black text-[10px] uppercase tracking-widest"
                                        style="color: var(--text-muted);">No agents authorized yet.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Permissions -->
                <div class="wizard-pane hidden" data-step="4">
                    <h2 class="text-2xl md:text-3xl font-black tracking-tight mb-2 uppercase" style="color: var(--text-main);">
                        Protocol <span class="gradient-text">Governors</span></h2>
                    <p class="font-bold text-xs md:text-sm uppercase tracking-tight mb-8 md:mb-12" style="color: var(--text-muted);">
                        Calibrate security levels and finalize integration blueprints.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div
                            class="glass-card !p-8 border-indigo-500/10 bg-indigo-500/5 group hover:bg-indigo-500/10 transition-all">
                            <div class="flex items-center justify-between mb-6">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                    <i class="ph-bold ph-shield-check text-2xl"></i>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="wizard-privacy-toggle" class="sr-only peer">
                                    <div
                                        class="w-14 h-7 bg-[var(--toggle-off)] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-gray-400 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500 shadow-inner">
                                    </div>
                                </label>
                            </div>
                            <h4 class="font-black text-lg uppercase tracking-tight mb-2"
                                style="color: var(--text-main);">Registry Privacy</h4>
                            <p class="text-[10px] font-bold text-gray-500 leading-relaxed uppercase tracking-widest">
                                Toggle absolute invisibility across the global node index.</p>
                        </div>

                        <div
                            class="glass-card !p-8 border-emerald-500/10 bg-emerald-500/5 group hover:bg-emerald-500/10 transition-all">
                            <div class="flex items-center justify-between mb-6">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                                    <i class="ph-bold ph-paper-plane-tilt text-2xl"></i>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="wizard-allow-invites" checked class="sr-only peer">
                                    <div
                                        class="w-14 h-7 bg-[var(--toggle-off)] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-gray-400 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner">
                                    </div>
                                </label>
                            </div>
                            <h4 class="font-black text-lg uppercase tracking-tight mb-2"
                                style="color: var(--text-main);">Guest Handshakes</h4>
                            <p class="text-[10px] font-bold text-gray-500 leading-relaxed uppercase tracking-widest">
                                Allow authorized agents to distribute invitation protocols.</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Buttons -->
            <div class="p-6 md:p-10 border-t border-[var(--border-color)] flex items-center justify-between">
                <button type="button" onclick="AdminApp.closeWizard()"
                    class="font-black text-[10px] uppercase tracking-[0.2em] transition-colors hover:text-primary"
                    style="color: var(--text-muted);">Cancel</button>
                <div class="flex gap-4">
                    <button type="button" id="wizard-prev" onclick="AdminApp.navWizard(-1)"
                        class="btn-secondary !rounded-[1.25rem] !px-8 hidden uppercase text-[10px] font-black tracking-widest">Back</button>
                    <button type="button" id="wizard-next" onclick="AdminApp.navWizard(1)"
                        class="btn-primary !rounded-[1.25rem] !px-8 !py-5 uppercase text-[10px] font-black tracking-widest shadow-2xl shadow-primary/30">Next
                        Step <i class="ph-bold ph-arrow-right ml-2 opacity-60"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

        </div>
    </div>
</div>

<!-- Global Modal: Universal Confirmation Protocol -->
<div id="global-confirm-modal" class="modal-overlay hidden bg-slate-950/40 backdrop-blur-3xl">
    <div id="global-confirm-card" class="modal-card !max-w-lg transition-all duration-500 border-white/5 bg-gradient-to-b from-white/[0.05] to-transparent shadow-[0_50px_100px_rgba(0,0,0,0.4)]">
        <div class="p-10 text-center">
            <!-- Dynamic Glowing Header -->
            <div class="flex justify-center mb-10">
                <div id="global-confirm-icon-box" class="w-24 h-24 rounded-[2.5rem] flex items-center justify-center border transition-all duration-500">
                    <i id="global-confirm-icon" class="text-5xl"></i>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="space-y-4 mb-12">
                <h3 id="global-confirm-title" class="text-3xl font-black uppercase tracking-tighter" style="color: var(--text-main);">Confirm Protocol</h3>
                <p id="global-confirm-subtitle" class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-500 opacity-60">System Authority Verification</p>
                <div id="global-confirm-divider" class="h-[1px] w-12 mx-auto my-6 opacity-30"></div>
                <p id="global-confirm-message" class="text-sm font-bold leading-relaxed px-4" style="color: var(--text-main);">---</p>
            </div>

            <!-- Animation Layer (Optional) -->
            <div id="global-confirm-progress-container" class="hidden mb-10 px-8 space-y-3">
                <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest text-primary animate-pulse">
                    <span id="global-confirm-progress-label">Processing...</span>
                    <span id="global-confirm-progress-pct">0%</span>
                </div>
                <div class="h-1.5 bg-white/5 rounded-full overflow-hidden p-0.5 border border-white/5">
                    <div id="global-confirm-progress-bar" class="h-full bg-primary w-0 rounded-full shadow-[0_0_15px_rgba(124,106,255,0.5)] transition-all duration-300"></div>
                </div>
            </div>

            <!-- Action Cluster -->
            <div class="grid grid-cols-2 gap-4">
                <button type="button" id="global-confirm-abort" class="btn-secondary !rounded-3xl !py-5 uppercase font-black tracking-widest text-[10px] group transition-all">
                    <span class="opacity-60 group-hover:opacity-100 transition-opacity">Abort</span>
                </button>
                <button type="button" id="global-confirm-execute" class="btn-primary !rounded-3xl !py-5 uppercase font-black tracking-widest text-[10px] transition-all active:scale-95 shadow-2xl">
                    Execute
                </button>
            </div>
        </div>
    </div>
</div>