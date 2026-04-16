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


<?php
// Note: Role Editor Modal has been relocated to _templates/admin/_modals.php 
// for global stacking context and full viewport coverage.
?>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
</style>
