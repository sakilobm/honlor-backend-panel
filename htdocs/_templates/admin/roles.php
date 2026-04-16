<?php use Aether\Session; ?>
<!-- Section: Role Studio (RBAC) -->
<div id="section-roles" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Role <span class="gradient-text">Studio</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Architect security clusters and granular governance protocols.</p>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <button class="btn-primary !rounded-2xl flex-1 md:flex-none justify-center shadow-xl shadow-primary/20" onclick="AdminApp.newRole()">
                <i class="ph-bold ph-shield-plus text-lg"></i>
                New Cluster
            </button>
        </div>
    </div>

    <!-- Main Card Gallery -->
    <div id="roles-card-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 pb-12">
        <!-- Roles Dynamically Injected by JS -->
        <div class="col-span-full p-20 text-center">
            <p class="animate-pulse font-black text-[10px] uppercase tracking-widest opacity-40">Synchronizing Security Vault...</p>
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
