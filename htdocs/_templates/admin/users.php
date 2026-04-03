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
                <tbody class="divide-y" style="border-color: var(--border-color);">
                    <!-- Row 1 -->
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Marcus" class="w-10 h-10 rounded-xl bg-primary/10 p-0.5" alt="Avatar">
                                <div>
                                    <p class="font-bold text-sm">Marcus Aurelius</p>
                                    <p class="text-[11px] text-gray-500 font-medium">marcus@honlor.io</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="badge-success">Active</span></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-1.5 rounded-full bg-gray-800">
                                    <div class="bg-primary h-full w-[85%] rounded-full"></div>
                                </div>
                                <span class="text-[10px] font-bold text-gray-400">85%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-semibold text-gray-400">Oct 12, 2023</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-2 hover:bg-primary/10 hover:text-primary rounded-xl transition-all text-gray-500"><i class="ph ph-eye text-lg"></i></button>
                                <button class="p-2 hover:bg-orange-500/10 hover:text-orange-400 rounded-xl transition-all text-gray-500"><i class="ph ph-pencil text-lg"></i></button>
                                <button class="p-2 hover:bg-red-500/10 hover:text-red-400 rounded-xl transition-all text-gray-500"><i class="ph ph-prohibit text-lg"></i></button>
                            </div>
                        </td>
                    </tr>
                    <!-- Row 2 -->
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Elena" class="w-10 h-10 rounded-xl bg-pink-500/10 p-0.5" alt="Avatar">
                                <div>
                                    <p class="font-bold text-sm">Elena Sorrows</p>
                                    <p class="text-[11px] text-gray-500 font-medium">elena.s@web3.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="badge-warning">Flagged</span></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-1.5 rounded-full bg-gray-800">
                                    <div class="bg-orange-400 h-full w-[40%] rounded-full"></div>
                                </div>
                                <span class="text-[10px] font-bold text-gray-400">40%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-semibold text-gray-400">Nov 04, 2023</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-2 hover:bg-primary/10 hover:text-primary rounded-xl transition-all text-gray-500"><i class="ph ph-eye text-lg"></i></button>
                                <button class="p-2 hover:bg-orange-500/10 hover:text-orange-400 rounded-xl transition-all text-gray-500"><i class="ph ph-pencil text-lg"></i></button>
                                <button class="p-2 hover:bg-red-500/10 hover:text-red-400 rounded-xl transition-all text-gray-500"><i class="ph ph-prohibit text-lg"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t flex justify-between items-center" style="border-color: var(--border-color);">
            <p class="text-xs font-bold text-gray-500">Showing 1-10 of 8,421 users</p>
            <div class="flex gap-2">
                <button class="btn-secondary !py-1.5 !px-3 !text-xs !rounded-xl">Previous</button>
                <button class="btn-secondary !py-1.5 !px-3 !text-xs !rounded-xl !bg-primary/20 !text-primary !border-primary/20">Next</button>
            </div>
        </div>
    </div>
</div>
