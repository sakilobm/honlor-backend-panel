<?php use Aether\Session; ?>
<!-- Section: Deletion Requests -->
<div id="section-deletion_requests" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight mb-2">Account Deletion</h2>
            <p class="font-medium" style="color: var(--text-muted);">Review and process user account removal applications</p>
        </div>
        <div class="flex gap-3">
            <div class="glass-card !p-3 !px-5 flex items-center gap-4 shadow-xl">
                <div class="w-10 h-10 rounded-xl bg-orange-500/20 flex items-center justify-center text-orange-500 border border-orange-500/20">
                    <i class="ph-bold ph-warning-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-60">Pending Review</p>
                    <p class="text-2xl font-black" id="pending-deletion-count">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="stat-card !p-0 overflow-hidden">
        <div class="p-6 border-b flex justify-between items-center" style="border-color: var(--border-color);">
            <h3 class="font-bold flex items-center gap-2">
                Governance Applications
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b" style="border-color: var(--border-color); background-color: var(--glass-bg);">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">User Profile</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Reason / Justification</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Current Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Submitted</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody id="deletion-requests-table-body" class="divide-y font-medium" style="border-color: var(--border-color);">
                    <!-- Data injected by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>
