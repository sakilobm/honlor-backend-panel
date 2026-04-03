<?php use Aether\Session; ?>
<!-- Section: Incident Reports -->
<div id="section-reports" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight mb-2">Safety Center</h2>
            <p class="font-medium" style="color: var(--text-muted);">Review and moderate flagged content or user behavior reports.</p>
        </div>
        <div class="flex gap-3">
            <button class="btn-secondary">
                <i class="ph-bold ph-shield-check"></i>
                Policy Editor
            </button>
            <button class="btn-primary">
                <i class="ph-bold ph-eye"></i>
                Audit Log
            </button>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="stat-card !p-0 overflow-hidden">
        <div class="p-6 border-b flex justify-between items-center" style="border-color: var(--border-color);">
            <h3 class="font-bold flex items-center gap-2">
                Pending Incidents
                <span class="badge-danger">Action Required</span>
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b" style="border-color: var(--border-color); background-color: var(--glass-bg);">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Target Account</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Category</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Evidence Snippet</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Decide</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--border-color);">
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Toxic" class="w-10 h-10 rounded-xl bg-red-500/10 p-0.5" alt="Avatar">
                                <div>
                                    <p class="font-bold text-sm text-red-400">@toxic_user</p>
                                    <p class="text-[11px] text-gray-500 font-medium">3 previous warnings</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-bold text-white/80 uppercase tracking-widest">Verbal Abuse</td>
                        <td class="px-6 py-4 max-w-xs">
                            <p class="text-xs text-gray-400 italic truncate">"Keep waiting Sarah. Your slides are meaningless anyway..."</p>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="btn-primary !p-2 !rounded-xl !bg-red-600 hover:!bg-red-700 shadow-red-900/10" title="Ban Account"><i class="ph ph-prohibit"></i></button>
                                <button class="btn-secondary !p-2 !rounded-xl" title="Mark Resolved"><i class="ph ph-check"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Spam" class="w-10 h-10 rounded-xl bg-orange-500/10 p-0.5" alt="Avatar">
                                <div>
                                    <p class="font-bold text-sm text-orange-400">@crypto_bot_99</p>
                                    <p class="text-[11px] text-gray-500 font-medium">Bulk messaging detected</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-bold text-white/80 uppercase tracking-widest">Spam/Scam</td>
                        <td class="px-6 py-4 max-w-xs">
                            <p class="text-xs text-gray-400 italic truncate">"Check my profile for 500% returns guaranteed!"</p>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="btn-primary !p-2 !rounded-xl !bg-red-600 hover:!bg-red-700 shadow-red-900/10"><i class="ph ph-prohibit"></i></button>
                                <button class="btn-secondary !p-2 !rounded-xl"><i class="ph ph-check"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t flex items-center justify-center" style="border-color: var(--border-color);">
            <button class="text-xs font-bold text-primary hover:underline">View All Moderate History</button>
        </div>
    </div>
</div>
