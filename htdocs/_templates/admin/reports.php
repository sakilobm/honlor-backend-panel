<?php use Aether\Session; ?>
<!-- Section: Safety Center (Compliance) -->
<div id="section-reports" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Safety <span class="gradient-text">Center</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Moderate global interactions and enforce ecosystem safety protocols.</p>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <button class="btn-secondary !rounded-2xl flex-1 md:flex-none justify-center" onclick="AdminApp.switchSection('policy_editor')">
                <i class="ph-bold ph-read-cv-logo text-lg"></i>
                Policy Studio
            </button>
            <button class="btn-primary !rounded-2xl flex-1 md:flex-none justify-center shadow-xl shadow-primary/20" onclick="toast.info('Audit Mode', 'Analyzing historical safety synchronization...')">
                <i class="ph-bold ph-shield-check text-lg"></i>
                Audit Ledger
            </button>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-8 border-b border-white/5" id="reports-tabs">
        <button class="tab-btn active" data-tab="incidents" onclick="AdminApp.switchTab('reports', 'incidents')">
            Active Incident Queue
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn" data-tab="audit" onclick="AdminApp.switchTab('reports', 'audit')">
            Resolution History
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: Incidents -->
    <div id="tab-content-incidents" class="tab-content space-y-8 animate-in fade-in duration-700">
        <div class="stat-card !p-0 overflow-hidden">
            <div class="p-8 border-b border-white/5 bg-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h3 class="text-xl font-black uppercase tracking-tight">Active <span class="gradient-text">Incidents</span></h3>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mt-1">Real-time incident monitoring across global nodes</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="badge-danger uppercase !px-3 !py-1.5 !text-[9px] font-black tracking-widest animate-pulse">Action Required</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5" style="background-color: var(--glass-bg);">
                            <th class="py-6 px-8">Target Identity</th>
                            <th class="py-6 px-8">Safety Category</th>
                            <th class="py-6 px-8">Evidence Context</th>
                            <th class="py-6 px-8 text-right">Synchronization</th>
                        </tr>
                    </thead>
                    <tbody id="reports-table-body" class="divide-y divide-white/5">
                        <!-- Incident Packets Syncing by JS -->
                        <tr>
                            <td colspan="4" class="p-20 text-center">
                                <div class="flex flex-col items-center gap-5 opacity-40">
                                    <div class="w-8 h-8 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-primary">Monitoring Global Safety Ingress...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Audit -->
    <div id="tab-content-audit" class="tab-content hidden space-y-8 animate-in fade-in duration-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="stat-card border-green-500/20 bg-gradient-to-br from-green-500/10 to-transparent">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Resolution Velocity</h3>
                <div class="flex items-center gap-8 p-6">
                     <div class="text-center">
                          <p class="text-4xl font-black text-green-400 tracking-tighter">98.2%</p>
                          <p class="text-[9px] font-black uppercase tracking-widest opacity-40 mt-1 leading-tight">Sync Success</p>
                     </div>
                     <div class="flex-grow space-y-4">
                          <div class="space-y-1.5">
                                <div class="flex justify-between text-[9px] font-black uppercase tracking-widest"><span>Manual Review</span><span>12%</span></div>
                                <div class="h-1 bg-white/5 rounded-full"><div class="h-full bg-green-500 w-[12%]"></div></div>
                          </div>
                          <div class="space-y-1.5">
                                <div class="flex justify-between text-[9px] font-black uppercase tracking-widest"><span>Auto-Resolved</span><span>88%</span></div>
                                <div class="h-1 bg-white/5 rounded-full"><div class="h-full bg-primary w-[88%] shadow-[0_0_8px_#7c6aff]"></div></div>
                          </div>
                     </div>
                </div>
            </div>

            <div class="stat-card">
                 <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Safety Ledger History</h3>
                 <div class="space-y-6">
                      <div class="flex gap-4 group">
                           <div class="w-1.5 h-1.5 rounded-full bg-blue-400 mt-2"></div>
                           <div class="flex-grow">
                                <p class="text-xs font-black uppercase tracking-widest opacity-60">Identity Resolve #8402</p>
                                <p class="text-[10px] font-bold mt-1 leading-relaxed">Account "CyberAgent_94" was cleared of behavior policy violations. Node cluster 04 synchronization success.</p>
                                <p class="text-[8px] font-black uppercase tracking-widest text-gray-500 mt-1">Processed 14m ago</p>
                           </div>
                      </div>
                      <div class="flex gap-4 group">
                           <div class="w-1.5 h-1.5 rounded-full bg-red-400 mt-2"></div>
                           <div class="flex-grow">
                                <p class="text-xs font-black uppercase tracking-widest opacity-60 text-red-400">Suspension Synced #8395</p>
                                <p class="text-[10px] font-bold mt-1 leading-relaxed">Account "Unknown_User_2" restricted globally for 72 hours due to Content Policy X.2 violation.</p>
                                <p class="text-[8px] font-black uppercase tracking-widest text-gray-500 mt-1">Processed 42m ago</p>
                           </div>
                      </div>
                 </div>
                 <button class="w-full mt-10 btn-secondary !py-3 !text-[10px] !font-black !uppercase tracking-widest !justify-center !rounded-[1.25rem]">Access Historical Packets</button>
            </div>
        </div>
    </div>
</div>
