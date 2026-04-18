<?php use Aether\Session; ?>
<!-- Section: Node Orchestration (Channels) -->
<div id="section-channels" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Channel <span class="gradient-text">Directory</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Coordinate active communication channels and high-fidelity messaging interfaces.</p>
        </div>
        <div class="flex gap-4 w-full md:w-auto">
             <div class="glass-card !p-3 !px-6 flex items-center gap-4 border-indigo-500/20 shadow-xl shadow-indigo-500/5">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 border border-indigo-400/20 shadow-[0_0_15px_rgba(99,102,241,0.2)]">
                    <i class="ph-bold ph-graph text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black uppercase tracking-widest opacity-40">Active Channels</p>
                    <p class="text-xl font-black tracking-tighter" id="metric-total-nodes">00</p>
                </div>
            </div>
            <button class="btn-primary !rounded-2xl !px-8 shadow-xl shadow-primary/20 flex-grow md:flex-none justify-center" onclick="AdminApp.openModal('create-channel-modal')">
                <i class="ph-bold ph-plus-circle text-lg"></i>
                Create Channel
            </button>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-8 border-b border-white/5" id="channels-tabs">
        <button class="tab-btn uppercase tracking-widest text-[10px] font-black" data-tab="list" onclick="AdminApp.switchTab('channels', 'list')">
            Browse Channels
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn uppercase tracking-widest text-[10px] font-black" id="tab-workspace-btn" data-tab="workspace" onclick="AdminApp.switchTab('channels', 'workspace')">
            Live Workspace
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn uppercase tracking-widest text-[10px] font-black relative" data-tab="protocols" onclick="AdminApp.switchTab('channels', 'protocols')">
            Channel Protocols
            <span class="absolute -top-1 -right-2 px-1.5 py-0.5 bg-primary text-[7px] text-white rounded-md animate-pulse">PRO</span>
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: Browse Channels -->
    <div id="tab-content-list" class="tab-content space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="stat-card !p-0 overflow-hidden bg-[var(--glass-bg)] border-[var(--border-color)] shadow-2xl">
            <div class="p-8 border-b border-[var(--border-color)] bg-[var(--glass-bg)] flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h3 class="text-xl font-black uppercase tracking-tight" style="color: var(--text-main);">Active <span class="gradient-text">Channels</span></h3>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mt-1 flex items-center gap-3" style="color: var(--text-muted);">
                        <span class="flex h-2 w-2 relative">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Global communication bandwidth operational
                    </p>
                </div>
                <div class="flex gap-4 w-full md:w-auto">
                    <button class="btn-secondary !rounded-xl !p-2.5 transition-all hover:bg-white/10" onclick="AdminApp.loadChannelList()">
                        <i class="ph ph-arrows-clockwise text-lg"></i>
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-[var(--border-color)]" style="background-color: var(--glass-bg);">
                            <th class="py-6 px-8">Cluster Identity</th>
                            <th class="py-6 px-8">Sync Protocol</th>
                            <th class="py-6 px-8 text-center">Load Factor</th>
                            <th class="py-6 px-8">Deployment</th>
                            <th class="py-6 px-8 text-right">Synchronization</th>
                        </tr>
                    </thead>
                    <tbody id="channels-table-body" class="divide-y divide-white/[0.03]">
                        <!-- Node Clusters Synced by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Content: Live Workspace (Chat Interface) -->
    <div id="tab-content-workspace" class="tab-content hidden animate-in fade-in duration-700 relative">
        <!-- Workspace Empty State Overlay -->
        <div id="ws-empty-overlay" class="absolute inset-0 z-50 flex items-center justify-center bg-[var(--surface)]/80 backdrop-blur-md rounded-[2.5rem] border border-[var(--border-color)]">
            <div class="text-center max-w-sm px-8">
                <div class="w-24 h-24 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center text-primary text-4xl mb-8 mx-auto shadow-2xl shadow-primary/10 animate-pulse">
                    <i class="ph-bold ph-chats-teardrop"></i>
                </div>
                <h3 class="text-2xl font-black uppercase tracking-tighter mb-4" style="color: var(--text-main);">Select a <span class="gradient-text">Channel</span></h3>
                <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest leading-relaxed mb-10">Choose an active communication cluster from the directory to begin live monitoring or team coordination.</p>
                <button onclick="AdminApp.switchTab('channels', 'list')" class="btn-primary !rounded-2xl !px-10 !py-4 uppercase text-[10px] font-black tracking-widest shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                    Browse Directory
                </button>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 min-h-[750px] max-h-[85vh]">
            
            <!-- Left Sidebar: Channel Metadata & Members -->
            <div class="w-full lg:w-96 flex flex-col gap-8 flex-shrink-0">
                
                <!-- Channel Identity Card -->
                <div class="stat-card group relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-32 h-32 bg-primary/5 rounded-full blur-3xl transition-all group-hover:bg-primary/10"></div>
                    <div class="flex items-center justify-between mb-8">
                        <div id="ws-channel-icon" class="w-16 h-16 rounded-[1.5rem] bg-primary/10 border border-primary/20 flex items-center justify-center text-primary text-2xl font-black shadow-2xl shadow-primary/10">
                            GC
                        </div>
                        <button class="px-4 py-2 rounded-xl bg-[var(--glass-bg)] border border-[var(--border-color)] text-[8px] font-black uppercase tracking-widest hover:bg-primary hover:text-white transition-all">Edit Channel</button>
                    </div>
                    <h3 id="ws-channel-name" class="text-2xl font-black uppercase tracking-tighter mb-4" style="color: var(--text-main);">Global Cluster</h3>
                    <p id="ws-channel-description" class="text-xs font-bold opacity-40 uppercase tracking-tight leading-relaxed mb-8 h-12 overflow-hidden line-clamp-2" style="color: var(--text-muted);">Coordinate global node clusters and high-frequency communication protocols.</p>
                    
                    <div class="space-y-4 pt-4 border-t border-[var(--border-color)]">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="ph-bold ph-lock-key-open text-primary opacity-40"></i>
                                <span class="text-[9px] font-black uppercase tracking-widest opacity-40" style="color: var(--text-muted);">Type</span>
                            </div>
                            <span id="ws-channel-type" class="px-3 py-1 bg-primary/5 text-primary border border-primary/10 rounded-lg text-[9px] font-black uppercase">Public</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="ph-bold ph-calendar text-primary opacity-40"></i>
                                <span class="text-[9px] font-black uppercase tracking-widest opacity-40" style="color: var(--text-muted);">Created</span>
                            </div>
                            <span id="ws-channel-date" class="text-[10px] font-black" style="color: var(--text-main);">Oct 12, 2023</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="ph-bold ph-user-focus text-primary opacity-40"></i>
                                <span class="text-[9px] font-black uppercase tracking-widest opacity-40" style="color: var(--text-muted);">Owner</span>
                            </div>
                            <span id="ws-channel-owner" class="text-[10px] font-black" style="color: var(--text-main);">Honlor Admin</span>
                        </div>
                    </div>
                </div>

                <!-- Member List Card -->
                <div class="stat-card flex-grow overflow-hidden flex flex-col !p-0">
                    <div class="p-8 border-b border-[var(--border-color)] flex items-center justify-between">
                        <h4 class="text-sm font-black uppercase tracking-widest" style="color: var(--text-main);">Channel <span class="gradient-text">Signals</span></h4>
                        <span id="ws-member-count" class="px-2 py-0.5 bg-primary/10 text-primary rounded-lg text-[9px] font-black uppercase tracking-widest">0 Total</span>
                    </div>
                    <div class="p-6">
                        <div class="relative mb-6">
                            <i class="ph-bold ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" class="w-full bg-[var(--glass-bg)] border border-[var(--border-color)] rounded-2xl p-3.5 pl-12 outline-none focus:border-primary/50 transition-all font-black text-[9px] uppercase tracking-widest" style="color: var(--text-main);" placeholder="Filter Agents...">
                        </div>
                        <div id="ws-member-list" class="space-y-4 max-h-[400px] overflow-y-auto custom-scrollbar pr-2">
                            <!-- Members injected by JS -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Chat Window -->
            <div class="flex-grow stat-card !p-0 flex flex-col overflow-hidden bg-[var(--bg-main)] shadow-2xl relative">
                
                <!-- Chat Header -->
                <div class="p-8 border-b border-[var(--border-color)] flex items-center justify-between z-10 backdrop-blur-md bg-[var(--surface-glass)]">
                    <div class="flex items-center gap-6">
                        <div class="flex -space-x-4">
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Admin" class="w-10 h-10 rounded-xl border-4 border-[var(--surface)] bg-[var(--glass-bg)]">
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah" class="w-10 h-10 rounded-xl border-4 border-[var(--surface)] bg-[var(--glass-bg)]">
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Marcus" class="w-10 h-10 rounded-xl border-4 border-[var(--surface)] bg-[var(--glass-bg)]">
                            <div class="w-10 h-10 rounded-xl border-4 border-[var(--surface)] bg-indigo-500 text-white flex items-center justify-center text-[10px] font-black">+12</div>
                        </div>
                        <div>
                            <h4 id="ws-chat-title" class="text-sm font-black uppercase tracking-tight" style="color: var(--text-main);">Live Preview</h4>
                            <p class="text-[8px] font-black uppercase tracking-widest text-emerald-500 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                42 Active Now
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <button class="btn-secondary !p-2.5 !rounded-xl transition-all hover:bg-white/10" title="Protocols">
                            <i class="ph-bold ph-gear-six text-lg opacity-40"></i>
                        </button>
                        <button class="btn-secondary !px-6 !py-2.5 !rounded-xl transition-all font-black text-[9px] uppercase tracking-widest flex items-center gap-3">
                            <i class="ph-bold ph-shield-check text-emerald-500"></i>
                            Moderation Mode
                        </button>
                    </div>
                </div>

                <!-- Chat Messages Area -->
                <div id="ws-chat-history" class="flex-grow overflow-y-auto p-10 space-y-8 flex flex-col custom-scrollbar bg-[var(--bg-main)]">
                    <div class="flex flex-col items-center gap-6 my-12 opacity-40">
                         <div class="px-6 py-2 rounded-2xl bg-[var(--glass-bg)] border border-[var(--border-color)] text-[8px] font-black uppercase tracking-[0.2em]" style="color: var(--text-muted);">Tuesday, October 24</div>
                    </div>
                    
                    <!-- Dynamic Messages Injected by JS -->
                    <div class="flex flex-col items-center justify-center p-24 opacity-20">
                        <i class="ph-bold ph-brackets-angle text-6xl mb-4"></i>
                        <p class="font-black text-[10px] uppercase tracking-widest">Initial Linkage Established</p>
                    </div>
                </div>

                <!-- Chat Input Area -->
                <div class="p-8 border-t border-[var(--border-color)] z-10 bg-[var(--surface-glass)] backdrop-blur-md">
                    <form id="ws-chat-form" onsubmit="event.preventDefault(); AdminApp.sendMessage();" class="relative group">
                        <div class="absolute left-6 top-1/2 -translate-y-1/2 flex items-center gap-4 text-gray-500">
                             <button type="button" class="hover:text-primary transition-colors"><i class="ph-bold ph-plus-circle text-xl"></i></button>
                             <button type="button" class="hover:text-primary transition-colors"><i class="ph-bold ph-smiley text-xl"></i></button>
                             <button type="button" class="hover:text-primary transition-colors"><i class="ph-bold ph-at text-xl"></i></button>
                        </div>
                        <input type="text" id="ws-chat-input" class="w-full bg-[var(--glass-bg)] border border-[var(--border-color)] rounded-[2rem] p-6 pl-32 pr-24 outline-none focus:border-primary transition-all font-bold text-sm tracking-tight" placeholder="Type a message to preview..." autocomplete="off">
                        <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-2xl bg-primary text-white flex items-center justify-center hover:scale-110 active:scale-95 transition-all shadow-xl shadow-primary/20">
                            <i class="ph-bold ph-paper-plane-right text-xl"></i>
                        </button>
                    </form>
                    <p class="text-[7px] font-black uppercase tracking-[0.2em] text-center mt-6 opacity-30" style="color: var(--text-muted);">
                        Moderator Mode Active: Messages sent here will be broadcasted to all active members.
                    </p>
                </div>

                <!-- Floating New Alert (Mock) -->
                <div id="ws-mod-alert" class="absolute bottom-32 right-12 z-20 animate-in fade-in slide-in-from-bottom-4 duration-500 hidden">
                    <div class="px-5 py-3 rounded-2xl bg-[var(--surface)] border border-[var(--border-color)] shadow-2xl flex items-center gap-4">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        <p class="text-[9px] font-black uppercase tracking-widest" style="color: var(--text-main);">New moderation alert</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content: Node Protocols -->
    <div id="tab-content-protocols" class="tab-content hidden space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="stat-card border-white/5 bg-white/[0.02] group">
                 <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60 flex items-center justify-between">
                     Global Standards
                     <i class="ph-bold ph-gear text-lg opacity-40 group-hover:rotate-90 transition-transform duration-500"></i>
                 </h3>
                 <div class="space-y-4">
                      <div class="flex items-center justify-between p-5 rounded-3xl bg-white/[0.03] border border-white/5 hover:border-emerald-500/30 transition-all duration-300">
                           <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 border border-emerald-500/20 shadow-xl shadow-emerald-500/5">
                                    <i class="ph-bold ph-shield-check text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest">TLS 1.3 Encryption</p>
                                    <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest mt-0.5">Mandatory Secure Tunnel</p>
                                </div>
                           </div>
                           <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 rounded-lg text-[8px] font-black uppercase tracking-widest">Active</span>
                      </div>
                      <div class="flex items-center justify-between p-5 rounded-3xl bg-white/[0.03] border border-white/5 hover:border-blue-500/30 transition-all duration-300">
                           <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20 shadow-xl shadow-blue-500/5">
                                    <i class="ph-bold ph-swap text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest">gRPC Data Pipeline</p>
                                    <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest mt-0.5">High-frequency Relay</p>
                                </div>
                           </div>
                           <span class="px-3 py-1 bg-blue-500/20 text-blue-400 border border-blue-500/20 rounded-lg text-[8px] font-black uppercase tracking-widest uppercase">Optimized</span>
                      </div>
                 </div>
            </div>

            <div class="stat-card border-primary/20 bg-gradient-to-br from-primary/[0.07] to-transparent relative overflow-hidden group">
                 <div class="absolute -right-4 -bottom-4 w-40 h-40 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-all duration-700"></div>
                 <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Channel Health Center</h3>
                 <div class="mt-4 p-8 rounded-[3rem] bg-white/[0.03] border border-white/5 text-center flex flex-col items-center backdrop-blur-md">
                      <div class="w-24 h-24 rounded-full border-4 border-dashed border-primary/20 flex items-center justify-center text-primary relative">
                           <div class="absolute inset-0 rounded-full border-4 border-primary border-t-transparent animate-spin opacity-40"></div>
                           <i class="ph-bold ph-lightning text-4xl animate-pulse text-indigo-400"></i>
                      </div>
                      <p class="text-2xl font-black uppercase tracking-tighter mt-8">System <span class="gradient-text">Synchronized</span></p>
                      <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest mt-2 leading-relaxed max-w-[280px]">Global channel directory is reporting 100% data integrity across all active communication clusters.</p>
                      <button class="btn-primary !rounded-[1.5rem] !justify-center !px-12 !py-4 mt-10 text-[9px] font-black tracking-[0.3em] uppercase shadow-2xl shadow-primary/20 hover:scale-[1.05] transition-all" onclick="AdminApp.deepScanNetwork()">Run Infrastructure Audit</button>
                 </div>
            </div>
        </div>
    </div>
</div>
