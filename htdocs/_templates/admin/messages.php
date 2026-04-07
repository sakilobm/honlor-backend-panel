<?php use Aether\Session; ?>
<!-- Section: Stream Moderation (Messages) -->
<div id="section-messages" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Stream <span class="gradient-text">Moderation</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Real-time telemetry monitoring and platform-level content orchestration.</p>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <div class="glass-card !p-4 !px-6 flex items-center gap-5 shadow-2xl shadow-blue-500/10 border-blue-500/20 group">
                <div class="w-12 h-12 rounded-2xl bg-blue-500/20 flex items-center justify-center text-blue-400 border border-blue-400/20 group-hover:bg-blue-400 group-hover:text-white transition-all scale-100 group-hover:scale-110">
                    <i class="ph-bold ph-activity text-2xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-60">Ingress Velocity</p>
                    <p class="text-3xl font-black tracking-tighter">42.8 msg/s</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-8 border-b border-white/5" id="messages-tabs">
        <button class="tab-btn active" data-tab="global" onclick="AdminApp.switchTab('messages', 'global')">
            Global Ingress
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn" data-tab="flagged" onclick="AdminApp.switchTab('messages', 'flagged')">
            Isolation Chamber
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: Global Ingress -->
    <div id="tab-content-global" class="tab-content space-y-8 animate-in fade-in duration-700">
        <div class="stat-card !p-0 overflow-hidden">
            <div class="p-8 border-b border-white/5 bg-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                   <h3 class="text-xl font-black uppercase tracking-tight">Active <span class="gradient-text">Stream</span></h3>
                   <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mt-1">Real-time telemetry monitor across global node clusters</p>
                </div>
                <div class="flex gap-4 w-full md:w-auto">
                    <div class="relative flex-grow md:flex-none group">
                        <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-primary transition-colors"></i>
                        <input type="text" placeholder="Filter Stream Packets..." 
                               class="w-full rounded-xl pl-11 pr-4 py-2.5 text-[10px] font-black uppercase tracking-widest focus:border-primary/50 outline-none transition-all placeholder:text-gray-500"
                               style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5" style="background-color: var(--glass-bg);">
                            <th class="py-6 px-8">Source Hub</th>
                            <th class="py-6 px-8">Active Context</th>
                            <th class="py-6 px-8 text-center">Safety Scan</th>
                            <th class="py-6 px-8">Synchronization</th>
                            <th class="py-6 px-8 text-right">Synchronization</th>
                        </tr>
                    </thead>
                    <tbody id="messages-table-body" class="divide-y divide-white/5 font-medium">
                        <!-- Message Streams Ingressed by JS -->
                        <tr><td colspan="5" class="p-20 text-center"><p class="animate-pulse font-black text-[10px] uppercase tracking-widest opacity-40">Decrypting Stream Packets...</p></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Isolation Chamber -->
    <div id="tab-content-flagged" class="tab-content hidden space-y-8 animate-in fade-in duration-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="stat-card border-orange-500/20 bg-gradient-to-br from-orange-500/10 to-transparent">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Guideline Violations</h3>
                <div class="mt-4 p-8 rounded-3xl bg-white/5 border border-white/5 text-center flex flex-col items-center">
                      <div class="w-20 h-20 rounded-full bg-orange-500/20 border border-orange-500/30 flex items-center justify-center text-orange-400">
                           <i class="ph ph-warning-circle text-4xl"></i>
                      </div>
                      <p class="text-xl font-black uppercase tracking-tighter mt-8">Manual <span class="gradient-text">Verification Required</span></p>
                      <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest mt-2 leading-relaxed">08 isolation packets awaiting guideline adherence audit.</p>
                      <button class="btn-primary !rounded-[1.25rem] !justify-center !px-10 !py-3 mt-10 text-[9px] font-black tracking-[0.2em] uppercase">Open Isolation Chamber</button>
                 </div>
            </div>

            <div class="stat-card">
                 <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Auto-Resolution Ledger</h3>
                 <div class="space-y-6">
                      <div class="flex gap-4 p-4 rounded-2xl bg-white/5 border border-white/5">
                           <div class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center text-green-400"><i class="ph ph-check"></i></div>
                           <div><p class="text-[10px] font-black uppercase tracking-widest">Protocol #8492 Resolved</p><p class="text-[8px] opacity-40 mt-0.5">Automated safety scan success</p></div>
                      </div>
                      <div class="flex gap-4 p-4 rounded-2xl bg-white/5 border border-white/5">
                           <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center text-red-500"><i class="ph ph-trash"></i></div>
                           <div><p class="text-[10px] font-black uppercase tracking-widest text-red-400">Packet #8488 Purged</p><p class="text-[8px] opacity-40 mt-0.5">Malicious code injection blocked</p></div>
                      </div>
                 </div>
            </div>
        </div>
    </div>
</div>
