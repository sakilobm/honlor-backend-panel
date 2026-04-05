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
                <tbody id="reports-table-body" class="divide-y" style="border-color: var(--border-color);">
                    <!-- Reports injected by JS -->
                    <tr>
                        <td colspan="4" class="p-12 text-center">
                            <div class="flex flex-col items-center gap-3 opacity-40">
                                <div class="w-8 h-8 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-primary">Monitoring Incidents...</p>
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
