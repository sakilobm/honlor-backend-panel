<?php use Aether\Session; ?>
<!-- Section: Users (Identity Vault) -->
<div id="section-users" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight mb-2">Identity Vault</h2>
            <p class="font-medium" style="color: var(--text-muted);">Manage global user accounts and security protocols.</p>
        </div>
        <div class="flex gap-3">
            <button class="btn-secondary">
                <i class="ph-bold ph-export"></i>
                Export CSV
            </button>
            <button class="btn-primary">
                <i class="ph-bold ph-user-plus"></i>
                Invite User
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="stat-card !p-0 overflow-hidden">
        <div class="p-6 border-b flex justify-between items-center" style="border-color: var(--border-color);">
            <h3 class="font-bold flex items-center gap-2">
                All Users
                <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] rounded-md">8.4k Total</span>
            </h3>
            <div class="flex gap-2">
                <input type="text" placeholder="Filter by username..." class="bg-transparent border rounded-xl px-4 py-1.5 text-xs outline-none focus:border-primary/50" style="border-color: var(--border-color);">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b" style="border-color: var(--border-color); background-color: var(--glass-bg);">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Identify</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Activity</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Joined</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table-body" class="divide-y" style="border-color: var(--border-color);">
                    <!-- Rows injected by JS -->
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t flex justify-between items-center" style="border-color: var(--border-color);">
            <p id="users-count-text" class="text-xs font-bold text-gray-500">Loading users...</p>
            <div class="flex gap-2">
                <button class="btn-secondary !py-1.5 !px-3 !text-xs !rounded-xl">Previous</button>
                <button class="btn-secondary !py-1.5 !px-3 !text-xs !rounded-xl !bg-primary/20 !text-primary !border-primary/20">Next</button>
            </div>
        </div>
    </div>
</div>
