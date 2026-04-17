<?php use Aether\Session; ?>
<!-- Section: Stream Moderation (Messages) -->
<div id="section-messages" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Stream <span class="gradient-text">Moderation</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Real-time telemetry monitoring and platform-level content orchestration.</p>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <div class="glass-card !p-4 !px-6 flex items-center gap-5 shadow-2xl shadow-blue-500/10 border-blue-500/20 group hover:shadow-blue-500/20 transition-all duration-500">
                <div class="w-12 h-12 rounded-2xl bg-blue-500/20 flex items-center justify-center text-blue-400 border border-blue-400/20 group-hover:bg-blue-400 group-hover:text-white transition-all scale-100 group-hover:scale-110 shadow-[0_0_20px_rgba(59,130,246,0)] group-hover:shadow-[0_0_20px_rgba(59,130,246,0.3)]">
                    <i class="ph-bold ph-activity text-2xl animate-pulse"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-0.5">Ingress Velocity</p>
                    <p class="text-3xl font-black tracking-tighter"><span id="metric-velocity">0.0</span> <span class="text-[10px] font-black opacity-30 ml-2">PKT/S</span></p>
                </div>
            </div>
            <div class="glass-card !p-4 !px-6 flex items-center gap-5 shadow-2xl shadow-emerald-500/10 border-emerald-500/20 group hover:shadow-emerald-500/20 transition-all duration-500">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 border border-emerald-400/20 group-hover:bg-emerald-400 group-hover:text-white transition-all scale-100 group-hover:scale-110 shadow-[0_0_20px_rgba(16,185,129,0)] group-hover:shadow-[0_0_20px_rgba(16,185,129,0.3)]">
                    <i class="ph-bold ph-shield-check text-2xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-0.5">Data Integrity</p>
                    <p class="text-3xl font-black tracking-tighter">99.8%</p>
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
        <button class="tab-btn relative" data-tab="flagged" onclick="AdminApp.switchTab('messages', 'flagged')">
            <span class="flex items-center gap-2">
                Isolation Chamber
                <div id="flagged-count-badge" class="hidden w-4 h-4 bg-orange-500 text-[8px] font-black items-center justify-center rounded-full text-white animate-bounce shadow-[0_0_10px_rgba(249,115,22,0.5)]">0</div>
            </span>
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: Global Ingress -->
    <div id="tab-content-global" class="tab-content space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="stat-card !p-0 overflow-hidden bg-white/[0.02] border-white/5 shadow-2xl">
            <div class="p-8 border-b border-white/5 bg-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                   <h3 class="text-xl font-black uppercase tracking-tight">Active <span class="gradient-text">Stream</span></h3>
                   <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mt-1">Real-time telemetry monitor across global node clusters</p>
                </div>
                <div class="flex gap-4 w-full md:w-auto">
                    <div class="relative flex-grow md:flex-none group">
                        <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-primary transition-colors"></i>
                        <input type="text" id="message-filter" placeholder="Filter Stream Packets..." 
                               class="w-full rounded-xl pl-11 pr-4 py-2.5 text-[10px] font-black uppercase tracking-widest focus:border-primary/50 outline-none transition-all placeholder:text-gray-500"
                               style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">
                    </div>
                    <button class="btn-secondary !rounded-xl !p-2.5 transition-all hover:bg-white/10" onclick="AdminApp.loadMessageList()">
                        <i class="ph ph-arrows-clockwise text-lg"></i>
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5" style="background-color: var(--glass-bg);">
                            <th class="py-6 px-8">Origin Hub</th>
                            <th class="py-6 px-8">Signal Content</th>
                            <th class="py-6 px-8 text-center">Safety Scan</th>
                            <th class="py-6 px-8">Synchronization</th>
                            <th class="py-6 px-8 text-right">Goverance</th>
                        </tr>
                    </thead>
                    <tbody id="messages-table-body" class="divide-y divide-white/[0.03]">
                        <!-- Message Streams Ingressed by JS -->
                        <tr><td colspan="5" class="p-24 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
                                <p class="font-black text-[10px] uppercase tracking-[0.3em] opacity-40">Decrypting Stream Packets...</p>
                            </div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Isolation Chamber -->
    <div id="tab-content-flagged" class="tab-content hidden space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Guideline Violations Overview -->
            <div class="stat-card border-orange-500/20 bg-gradient-to-br from-orange-500/[0.05] to-transparent relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-32 h-32 bg-orange-500/5 rounded-full blur-3xl group-hover:bg-orange-500/10 transition-all duration-700"></div>
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                    Identity Violations
                </h3>
                <div class="mt-4 p-8 rounded-[2.5rem] bg-white/[0.03] border border-white/5 text-center flex flex-col items-center backdrop-blur-md">
                      <div class="w-20 h-20 rounded-[2rem] bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-400 mb-8 shadow-2xl shadow-orange-500/10 group-hover:scale-110 transition-transform duration-500">
                           <i class="ph-bold ph-warning-circle text-4xl"></i>
                      </div>
                      <p class="text-3xl font-black uppercase tracking-tighter mb-2"><span id="chamber-count">00</span> <span class="gradient-text">Signals</span></p>
                      <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest leading-relaxed max-w-[200px]">Awaiting manual guideline adherence audit.</p>
                      <button class="btn-primary !rounded-[1.25rem] !justify-center !px-10 !py-3 mt-10 text-[9px] font-black tracking-[0.2em] uppercase shadow-xl shadow-primary/20 hover:shadow-primary/40 transition-all">Open Protocol Vault</button>
                 </div>
            </div>

            <!-- Auto-Resolution Ledger -->
            <div class="stat-card border-white/5 bg-white/[0.02] flex flex-col">
                 <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Resolution Ledger</h3>
                 <div class="space-y-4 flex-grow overflow-y-auto pr-2 custom-scrollbar max-h-[280px]" id="resolution-ledger">
                      <!-- Activity logs injected by JS -->
                      <div class="p-12 text-center opacity-20 italic text-[10px] font-black uppercase tracking-widest py-20">Scanning Ledger...</div>
                 </div>
                 <div class="mt-6 pt-6 border-t border-white/5 flex justify-between items-center text-[9px] font-black uppercase tracking-widest opacity-40">
                      <span>Total Resolutions Today</span>
                      <span class="text-emerald-400">14 Active</span>
                 </div>
            </div>
        </div>

        <div class="pt-8 border-t border-white/5">
            <h3 class="text-[11px] font-black uppercase tracking-[0.3em] mb-8 opacity-40 flex items-center gap-4">
                Active Isolation Packets
                <div class="h-[1px] flex-grow bg-white/5"></div>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="flagged-messages-grid">
                 <!-- Flagged Cards Dynamically Injected here -->
            </div>
        </div>
    </div>
</div>
