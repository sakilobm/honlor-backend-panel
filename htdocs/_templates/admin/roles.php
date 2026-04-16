<?php use Aether\Session; ?>
<!-- Section: Role Studio (RBAC) -->
<div id="section-roles" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Role <span class="gradient-text">Studio</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Architect security clusters and granular governance protocols.</p>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <button class="btn-primary !rounded-2xl flex-1 md:flex-none justify-center shadow-xl shadow-primary/20" onclick="AdminApp.openModal('role-editor-modal')">
                <i class="ph-bold ph-shield-plus text-lg"></i>
                New Cluster
            </button>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="stat-card !p-0 overflow-hidden">
        <div class="p-8 border-b border-white/5 bg-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
               <h3 class="text-xl font-black uppercase tracking-tight">Security <span class="gradient-text">Matrix</span></h3>
               <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mt-1 leading-none">Global Privilege Distribution</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5" style="background-color: var(--glass-bg);">
                        <th class="py-6 px-8">Security Identification</th>
                        <th class="py-6 px-8">Active Privileges</th>
                        <th class="py-6 px-8 text-right">Orchestration</th>
                    </tr>
                </thead>
                <tbody id="roles-table-body" class="divide-y divide-white/5">
                    <!-- Roles Dynamically Injected by JS -->
                    <tr><td colspan="3" class="p-20 text-center"><p class="animate-pulse font-black text-[10px] uppercase tracking-widest opacity-40">Synchronizing Security Vault...</p></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Role Editor Modal (Hidden by default) -->
<div id="role-editor-modal" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4 sm:p-6 backdrop-blur-2xl bg-black/60 overflow-hidden" onclick="if(event.target === this) closeModal()">
    <div class="glass-card w-full max-w-2xl max-h-[90vh] flex flex-col animate-in zoom-in duration-300 shadow-[0_30px_100px_rgba(0,0,0,0.5)] overflow-hidden !p-0">

        <div class="p-6 border-b border-white/5 flex items-center justify-between bg-white/5">
            <h3 class="text-xl font-black uppercase tracking-tight">Security <span class="gradient-text">Architect</span></h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-lg hover:bg-white/10 flex items-center justify-center transition-colors"><i class="ph ph-x text-xl"></i></button>
        </div>
        <form id="save-role-form" class="flex flex-col h-full overflow-hidden">
            <div class="p-8 space-y-8 flex-grow overflow-y-auto custom-scrollbar">

            <input type="hidden" name="role_id" value="">
            
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Cluster Designation</label>
                <input type="text" name="role_name" placeholder="E.g. Content Moderator, Analyst" required
                    class="w-full bg-white/5 border border-white/5 rounded-xl py-4 px-6 outline-none focus:border-primary/50 transition-all font-bold text-sm">
            </div>

            <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <button type="button" onclick="AdminApp.toggleAllPermissions('view')" class="text-[9px] font-black uppercase tracking-widest text-primary hover:underline">All Read</button>
                        <button type="button" onclick="AdminApp.toggleAllPermissions('manage')" class="text-[9px] font-black uppercase tracking-widest text-green-500 hover:underline">All Write</button>
                        <button type="button" onclick="AdminApp.toggleAllPermissions('delete')" class="text-[9px] font-black uppercase tracking-widest text-red-500 hover:underline">All Purge</button>
                        <div class="h-4 w-px bg-white/10 mx-2"></div>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <span class="text-[9px] font-black uppercase tracking-widest opacity-40 group-hover:opacity-100 transition-opacity">Absolute Root Access</span>
                            <input type="checkbox" name="perms[all]" class="w-4 h-4 rounded border-white/10 bg-white/5 text-primary focus:ring-primary">
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

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
</style>
