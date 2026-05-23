<?php
use Aether\Session;
?>
<!-- Section: App Version Control (Governance & Shard Deployment) -->
<div id="section-app_config" class="section active space-y-8 animate-in fade-in duration-500">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">App Version <span class="gradient-text">Control</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Manage platform client updates, mandatory security migrations, and binary source targets.</p>
        </div>
        <div class="flex gap-4 w-full md:w-auto">
            <button class="glass-card !bg-[var(--surface-2)] !p-4 !px-8 flex items-center gap-3 border border-[var(--border-color)] hover:border-primary/50 transition-all group" onclick="AdminApp.initAppConfig()">
                <i class="ph-bold ph-arrow-clockwise text-lg group-hover:rotate-180 transition-transform duration-500"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Reload Registry</span>
            </button>
            <button class="btn-primary !p-4 !px-10 !rounded-2xl shadow-xl shadow-primary/20 hover:scale-[1.05] transition-all flex items-center gap-3" onclick="AdminApp.openPublishReleaseModal()">
                <i class="ph-bold ph-plus-circle text-lg"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Publish Release</span>
            </button>
        </div>
    </div>

    <!-- Orchestration Telemetry Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8" id="appconfig-stats-row">
        <!-- Stat Card: Target Build -->
        <div class="stat-card border-primary/20 bg-gradient-to-br from-primary/[0.05] to-transparent group">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-[2.5rem] bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shadow-2xl group-hover:scale-110 transition-transform">
                    <i class="ph-bold ph-git-branch text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Target Active Build</p>
                    <p class="text-3xl font-black tracking-tighter mt-1" id="stat-active-version">—</p>
                </div>
            </div>
        </div>

        <!-- Stat Card: Enforcement Density -->
        <div class="stat-card border-red-500/20 bg-gradient-to-br from-red-500/[0.05] to-transparent group">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-[2.5rem] bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20 shadow-2xl group-hover:rotate-12 transition-transform">
                    <i class="ph-bold ph-warning-octagon text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Enforcement Density</p>
                    <p class="text-3xl font-black tracking-tighter mt-1" id="stat-enforcement-ratio">—</p>
                </div>
            </div>
        </div>

        <!-- Stat Card: Sync Status -->
        <div class="stat-card border-emerald-400/20 bg-gradient-to-br from-emerald-400/[0.05] to-transparent group">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-[2.5rem] bg-emerald-400/10 flex items-center justify-center text-emerald-400 border border-emerald-400/20 shadow-2xl group-hover:-rotate-12 transition-transform">
                    <i class="ph-bold ph-lightning text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Registry Status</p>
                    <p class="text-3xl font-black tracking-tighter mt-1 text-emerald-400" id="stat-sync-status">—</p>
                </div>
            </div>
        </div>

        <!-- Stat Card: Shard Latency -->
        <div class="stat-card border-indigo-400/20 bg-gradient-to-br from-indigo-400/[0.05] to-transparent group">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-[2.5rem] bg-indigo-400/10 flex items-center justify-center text-indigo-400 border border-indigo-400/20 shadow-2xl group-hover:scale-110 transition-transform">
                    <i class="ph-bold ph-arrows-left-right text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Registered Shards</p>
                    <p class="text-3xl font-black tracking-tighter mt-1" id="stat-total-releases">—</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Registry Database View -->
    <div class="stat-card !p-0 overflow-hidden shadow-2xl" style="background-color: var(--glass-bg); border: 1px solid var(--border-color);">
        <!-- Table Header Panel -->
        <div class="p-8 border-b border-[var(--border-color)] bg-[var(--surface-2)] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] opacity-60">Version Upgrades Registry</h3>
                <p class="text-[10px] opacity-40 uppercase tracking-widest mt-1">Live active build configurations deployed across all nodes</p>
            </div>
            <span class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-lg text-[8px] font-black text-emerald-400 uppercase tracking-widest flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> SQL SYNCHRONIZED
            </span>
        </div>

        <!-- Responsive Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b" style="border-color: var(--border-color); background-color: rgba(255,255,255,0.01);">
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest opacity-60">Target Build / Tag</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest opacity-60">Enforcement Target</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest opacity-60">Update Target Source (URL)</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest opacity-60">Release Description / Notes</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest opacity-60">Broadcast Timestamp</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest opacity-60 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="appconfig-table-body" class="divide-y" style="border-color: var(--border-color);">
                    <!-- Dynamic Rows Injected Here -->
                    <tr>
                        <td colspan="6" class="p-20 text-center animate-pulse">
                            <p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-30">Loading Shard Deployments...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Failsafe Dynamic Info Card -->
    <div class="stat-card border-primary/10 bg-primary/[0.02] flex items-center gap-8 py-10 px-10">
        <div class="w-20 h-20 rounded-[2.5rem] bg-primary/10 flex items-center justify-center text-primary border border-primary/20 relative shrink-0">
            <i class="ph-bold ph-shield-check text-4xl"></i>
            <div class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 border-4 border-[var(--bg-main)]"></div>
        </div>
        <div>
            <h4 class="text-xl font-black uppercase tracking-tight" style="color: var(--text-main);">Automatic Client Notification Guard</h4>
            <p class="text-[11px] font-medium opacity-60 leading-relaxed max-w-4xl mt-2" style="color: var(--text-muted);">
                The active target version controls client application initialization gates. When a user runs a client build older than the active build, the system intercepts the execution. If <span class="text-red-400 font-bold">Mandatory</span> is toggled, users are locked from accessing the app until they upgrade via the target URL. If optional, a subtle update dialog is displayed, allowing them to proceed or download.
            </p>
        </div>
    </div>

    <!-- Modal: Publish Version Configuration (Local Scope) -->
    <div id="publish-release-modal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-6 bg-[#030407]/80 backdrop-blur-xl animate-in fade-in zoom-in-95 duration-300">
        <div class="w-full max-w-xl rounded-[2.5rem] overflow-hidden shadow-[0_32px_64px_-16px_rgba(0,0,0,0.8)] border border-white/10" style="background-color:var(--surface-glass); backdrop-blur-2xl;">
            <div class="p-8 border-b flex items-center justify-between" style="border-color:var(--border-color);">
                <div>
                    <h3 class="text-xl font-black uppercase tracking-tight">Publish <span class="gradient-text">App Upgrade</span></h3>
                    <p class="text-[9px] font-bold opacity-30 uppercase tracking-[0.2em] mt-1">Configure client update triggers & deployment targets</p>
                </div>
                <button onclick="window.closeModal()" class="w-10 h-10 rounded-2xl bg-white/5 hover:bg-red-500/10 hover:text-red-400 transition-all flex items-center justify-center border border-white/5 shadow-inner">
                    <i class="ph-bold ph-x text-sm"></i>
                </button>
            </div>
            <div class="p-8 space-y-6 max-h-[80vh] overflow-y-auto custom-scrollbar">
                <!-- Version Identifier input -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest opacity-60">Version Identifier (Semantic)</label>
                    <input type="text" id="ac-new-version" placeholder="e.g. v1.2.3" class="w-full bg-white/[0.03] border border-white/10 rounded-2xl p-4 font-bold text-sm focus:border-primary outline-none transition-all" style="color:var(--text-main);">
                </div>

                <!-- Update URL target -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest opacity-60">Target Deployment Source (URL)</label>
                    <input type="url" id="ac-new-url" placeholder="https://play.google.com/store/apps/details?id=..." class="w-full bg-white/[0.03] border border-white/10 rounded-2xl p-4 font-bold text-sm focus:border-primary outline-none transition-all font-mono" style="color:var(--text-main);">
                </div>

                <!-- Mandatory Toggle Switch -->
                <div class="p-6 bg-white/5 rounded-2xl border border-white/5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-tight" style="color: var(--text-main);">Force Mandatory Upgrade</p>
                        <p class="text-[10px] font-bold opacity-30 mt-1 uppercase tracking-widest">Locks app access until client is updated</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="ac-new-mandatory" class="sr-only peer">
                        <div class="w-14 h-7 bg-white/10 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-red-500 shadow-lg"></div>
                    </label>
                </div>

                <!-- Notes / Description -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest opacity-60">Release Description & Shard Notes</label>
                    <textarea id="ac-new-description" placeholder="e.g. Security fix resolving session hijack exploit." rows="4" class="w-full bg-white/[0.03] border border-white/10 rounded-2xl p-4 font-bold text-sm focus:border-primary outline-none transition-all" style="color:var(--text-main);"></textarea>
                </div>

                <!-- Form Trigger -->
                <button onclick="AdminApp.publishNewVersion()" class="w-full btn-primary !p-5 !rounded-2xl flex items-center justify-center gap-3 shadow-xl shadow-primary/30 mt-4 group">
                    <i class="ph-bold ph-rocket text-lg group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Broadcast Upgrade Target</span>
                </button>
            </div>
        </div>
    </div>
</div>
