<?php
use App\Settings;
use Aether\Session;

$allSettings = Settings::getAll();
?>
<!-- Section: Command Orchestration (Control Center) -->
<div id="section-settings" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Command <span class="gradient-text">Orchestration</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Manage global platform protocols, security governance, and system-wide synchronization.</p>
        </div>
        <div class="flex gap-4 w-full md:w-auto">
            <button class="glass-card !bg-[var(--surface-2)] !p-4 !px-8 flex items-center gap-3 border border-[var(--border-color)] hover:border-primary/50 transition-all group" onclick="AdminApp.switchSection('settings')">
                <i class="ph-bold ph-arrow-clockwise text-lg group-hover:rotate-180 transition-transform duration-500"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Reset State</span>
            </button>
            <button class="btn-primary !p-4 !px-10 !rounded-2xl shadow-xl shadow-primary/20 hover:scale-[1.05] transition-all flex items-center gap-3" id="save-settings-btn" onclick="AdminApp.saveSettings()">
                <i class="ph-bold ph-floppy-disk text-lg"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Synchronize Protocols</span>
            </button>
        </div>
    </div>

    <!-- Orchestration Telemetry Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="stat-card border-emerald-400/20 bg-gradient-to-br from-emerald-400/[0.05] to-transparent group">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-[2.5rem] bg-emerald-400/10 flex items-center justify-center text-emerald-400 border border-emerald-400/20 shadow-2xl group-hover:scale-110 transition-transform">
                    <i class="ph-bold ph-shield-check text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Platform Integrity</p>
                    <p class="text-3xl font-black tracking-tighter mt-1">99.8%</p>
                </div>
            </div>
        </div>

        <div class="stat-card border-primary/20 bg-gradient-to-br from-primary/[0.05] to-transparent group">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-[2.5rem] bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shadow-2xl group-hover:rotate-12 transition-transform">
                    <i class="ph-bold ph-lightning text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Sync Latency</p>
                    <p class="text-3xl font-black tracking-tighter mt-1">12ms</p>
                </div>
            </div>
        </div>

        <div class="stat-card border-indigo-400/20 bg-gradient-to-br from-indigo-400/[0.05] to-transparent group">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-[2.5rem] bg-indigo-400/10 flex items-center justify-center text-indigo-400 border border-indigo-400/20 shadow-2xl group-hover:-rotate-12 transition-transform">
                    <i class="ph-bold ph-hard-drive text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Auth Gateway</p>
                    <p class="text-3xl font-black tracking-tighter mt-1">Secure</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-10 border-b border-[var(--border-color)] overflow-x-auto" id="settings-tabs">
        <button class="tab-btn active uppercase tracking-[0.2em] text-[10px] font-black whitespace-nowrap" data-tab="general" onclick="AdminApp.switchTab('settings', 'general')">
            General Operations
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn uppercase tracking-[0.2em] text-[10px] font-black whitespace-nowrap" data-tab="security" onclick="AdminApp.switchTab('settings', 'security')">
            Security Governance
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn uppercase tracking-[0.2em] text-[10px] font-black whitespace-nowrap" data-tab="system" onclick="AdminApp.switchTab('settings', 'system')">
            System Intelligence
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn uppercase tracking-[0.2em] text-[10px] font-black whitespace-nowrap" data-tab="webhooks" onclick="AdminApp.switchTab('settings', 'webhooks'); AdminApp.initWebhookHub();">
            <span class="flex items-center gap-2">Webhook Hub <span id="webhook-error-badge" class="hidden w-2 h-2 rounded-full bg-red-500 animate-pulse inline-block"></span></span>
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: General -->
    <div id="tab-content-general" class="tab-content space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="stat-card" style="background-color: var(--glass-bg); border: 1px solid var(--border-color);">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-10 opacity-60 text-primary">Global Protocol Toggles</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Maintenance Mode -->
                <div class="group p-6 rounded-[2.5rem] border border-[var(--border-color)] bg-[var(--surface-2)] hover:border-orange-500/30 hover:bg-orange-500/[0.03] transition-all flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <div class="w-14 h-14 rounded-2xl bg-orange-500/10 flex items-center justify-center text-orange-500 border border-orange-500/20 group-hover:scale-110 transition-transform">
                            <i class="ph-bold ph-warning-octagon text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black uppercase tracking-tight" style="color: var(--text-main);">Maintenance</p>
                            <p class="text-[10px] font-bold opacity-30 mt-1 uppercase tracking-widest">Public Redirect</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" onchange="AdminApp.toggleSetting('maintenance_mode', this.checked)" <?= Settings::get('maintenance_mode') === 'on' ? 'checked' : '' ?>>
                        <div class="w-14 h-7 bg-white/10 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-orange-500 shadow-lg"></div>
                    </label>
                </div>

                <!-- Registration -->
                <div class="group p-6 rounded-[2.5rem] border border-[var(--border-color)] bg-[var(--surface-2)] hover:border-blue-400/30 hover:bg-blue-400/[0.03] transition-all flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <div class="w-14 h-14 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-400/20 group-hover:scale-110 transition-transform">
                            <i class="ph-bold ph-user-plus text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black uppercase tracking-tight" style="color: var(--text-main);">Public Auth</p>
                            <p class="text-[10px] font-bold opacity-30 mt-1 uppercase tracking-widest">Open Registration</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" onchange="AdminApp.toggleSetting('public_registration', this.checked)" <?= Settings::get('public_registration') === 'on' ? 'checked' : '' ?>>
                        <div class="w-14 h-7 bg-white/10 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-500 shadow-lg"></div>
                    </label>
                </div>

                <!-- Ad Serving -->
                <div class="group p-6 rounded-[2.5rem] border border-[var(--border-color)] bg-[var(--surface-2)] hover:border-primary/30 hover:bg-primary/[0.03] transition-all flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20 group-hover:scale-110 transition-transform">
                            <i class="ph-bold ph-broadcast text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black uppercase tracking-tight" style="color: var(--text-main);">Ad Pipeline</p>
                            <p class="text-[10px] font-bold opacity-30 mt-1 uppercase tracking-widest">Global Serving</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" onchange="AdminApp.toggleSetting('ad_serving', this.checked)" <?= Settings::get('ad_serving') === 'on' ? 'checked' : '' ?>>
                        <div class="w-14 h-7 bg-white/10 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary shadow-lg"></div>
                    </label>
                </div>
            </div>
        </div>

        <div class="stat-card border-primary/10 bg-primary/[0.02] flex items-center gap-8 py-10 px-10">
            <div class="w-20 h-20 rounded-[2.5rem] bg-primary/10 flex items-center justify-center text-primary border border-primary/20 relative">
                <i class="ph-bold ph-gear-six text-4xl animate-spin-slow"></i>
                <div class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 border-4 border-[var(--bg-main)]"></div>
            </div>
            <div>
                <h4 class="text-xl font-black uppercase tracking-tight" style="color: var(--text-main);">Governance Real-Time Sync</h4>
                <p class="text-[11px] font-medium opacity-60 leading-relaxed max-w-2xl mt-2" style="color: var(--text-muted);">The Command Orchestration module provides 1:1 synchronization with the global ledger. All modifications are broadcasted to decentralized vault shards and CDN edge locations instantly upon authorization.</p>
            </div>
        </div>
    </div>

    <!-- Tab Content: Security -->
    <div id="tab-content-security" class="tab-content hidden space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="stat-card" style="background-color: var(--glass-bg); border: 1px solid var(--border-color);">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-12 opacity-60 text-indigo-400">Security Gateways & Persistence</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2 flex items-center gap-2">
                        <i class="ph ph-clock text-xs"></i> Session Lifetime (Seconds)
                    </label>
                    <input type="number" id="session_timeout" value="<?= Settings::get('session_timeout', 3600) ?>" 
                           class="w-full bg-[var(--surface-2)] border border-[var(--border-color)] rounded-3xl p-6 font-bold text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all shadow-inner" style="color: var(--text-main);">
                </div>
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2 flex items-center gap-2">
                        <i class="ph ph-lightning text-xs"></i> Rate Limit (Requests/Min)
                    </label>
                    <input type="number" id="rate_limit" value="<?= Settings::get('rate_limit', 60) ?>" 
                           class="w-full bg-[var(--surface-2)] border border-[var(--border-color)] rounded-3xl p-6 font-bold text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all shadow-inner" style="color: var(--text-main);">
                </div>
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2 flex items-center gap-2">
                        <i class="ph ph-password text-xs"></i> Auth Entropy Threshold
                    </label>
                    <input type="number" id="auth_retry_limit" value="<?= Settings::get('auth_retry_limit', 5) ?>" 
                           class="w-full bg-[var(--surface-2)] border border-[var(--border-color)] rounded-3xl p-6 font-bold text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all shadow-inner" style="color: var(--text-main);">
                </div>
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2 flex items-center gap-2">
                        <i class="ph ph-globe-hemisphere-west text-xs"></i> Global Packet Blocklist (IPs)
                    </label>
                    <input type="text" id="ip_blocklist" value="<?= Settings::get('ip_blocklist', '127.0.0.1') ?>" 
                           class="w-full bg-[var(--surface-2)] border border-[var(--border-color)] rounded-3xl p-6 font-bold text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all shadow-inner" style="color: var(--text-main);">
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content: System -->
    <div id="tab-content-system" class="tab-content hidden grid grid-cols-1 lg:grid-cols-3 gap-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="lg:col-span-1 stat-card border-primary/20 bg-gradient-to-br from-primary/[0.05] to-transparent flex flex-col items-center justify-center py-16 text-center">
            <div class="relative mb-10">
                <div class="w-40 h-40 rounded-full border-4 border-primary/10 flex items-center justify-center p-4">
                    <div class="w-full h-full rounded-full border-4 border-primary border-t-transparent animate-spin-slow shadow-[0_0_30px_rgba(124,106,255,0.4)]"></div>
                </div>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <p class="text-4xl font-black tracking-tighter" style="color: var(--text-main);">98%</p>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-40">Healthy</p>
                </div>
            </div>
            <h3 class="text-xl font-black uppercase tracking-tight mb-2">Node <span class="gradient-text">Efficiency</span></h3>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40 mb-10">CORE ENGINE v4.2.0-STABLE</p>
            
            <button class="w-full btn-primary !p-4 !rounded-2xl !bg-[var(--surface-2)] !border-[var(--border-color)] !text-[var(--text-secondary)] !font-black !uppercase tracking-[0.2em] hover:!bg-primary hover:!text-white transition-all shadow-none" onclick="AdminApp.initControlCenterTelemetry()">
                Refresh Diagnostics
            </button>
        </div>

        <div class="lg:col-span-2 stat-card !p-0 overflow-hidden shadow-2xl" style="background-color: var(--glass-bg); border: 1px solid var(--border-color);">
            <div class="p-8 border-b border-[var(--border-color)] bg-[var(--surface-2)] flex justify-between items-center">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] opacity-60">Protocol Audit Ledger</h3>
                <span class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-lg text-[8px] font-black text-emerald-400 uppercase tracking-widest flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Synchronized
                </span>
            </div>
            <div class="p-8 space-y-8 max-h-[460px] overflow-y-auto custom-scrollbar">
                <?php
                $db = Aether\Database::getConnection();
                $stmt = $db->query("SELECT l.*, a.username FROM logs l LEFT JOIN auth a ON l.user_id = a.id WHERE l.action LIKE 'Updated setting%' ORDER BY l.created_at DESC LIMIT 12");
                $auditLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($auditLogs):
                    foreach ($auditLogs as $log):
                ?>
                <div class="flex gap-6 group">
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-primary group-hover:scale-[1.5] transition-all duration-300"></div>
                        <div class="w-px flex-grow bg-[var(--border-color)] group-last:hidden"></div>
                    </div>
                    <div class="pb-6">
                        <div class="flex items-center gap-4">
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=<?= $log['username'] ?? 'System' ?>" class="w-6 h-6 rounded-lg bg-primary/10 shadow-lg" alt="User">
                            <p class="text-[10px] font-black uppercase tracking-widest text-primary"><?= htmlspecialchars($log['username'] ?? 'System') ?></p>
                            <span class="text-white/10">•</span>
                            <span class="text-[9px] font-bold text-gray-500 tracking-widest"><?= date('M d, H:i:s', strtotime($log['created_at'])) ?></span>
                        </div>
                        <p class="text-xs font-medium mt-2 opacity-80 leading-relaxed" style="color: var(--text-main);"><?= htmlspecialchars($log['action']) ?></p>
                    </div>
                </div>
                <?php endforeach; else: ?>
                <div class="p-20 text-center space-y-4">
                     <i class="ph-bold ph-read-cv-logo text-5xl opacity-10"></i>
                     <p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-30 italic">Protocol ledger is currently empty</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tab Content: Webhooks -->
    <div id="tab-content-webhooks" class="tab-content hidden space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <!-- Header Row -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h3 class="text-2xl font-black uppercase tracking-tight">Webhook <span class="gradient-text">Hub</span></h3>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40 mt-1">Monitor outbound hook health, inspect errors, and manage delivery pipelines</p>
            </div>
            <button onclick="AdminApp.openAddWebhookModal()" class="btn-primary !px-8 !py-4 !rounded-2xl shadow-xl shadow-primary/20 hover:scale-[1.05] transition-all whitespace-nowrap">
                <i class="ph-bold ph-plus-circle text-lg"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Register Hook</span>
            </button>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6" id="webhook-stats-row">
            <div class="stat-card !p-6 border-emerald-400/20 bg-gradient-to-br from-emerald-400/[0.05] to-transparent">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2">Healthy</p>
                <p class="text-3xl font-black tracking-tighter text-emerald-400" id="wh-stat-healthy">—</p>
            </div>
            <div class="stat-card !p-6 border-red-400/20 bg-gradient-to-br from-red-400/[0.05] to-transparent">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2">Failing</p>
                <p class="text-3xl font-black tracking-tighter text-red-400" id="wh-stat-failing">—</p>
            </div>
            <div class="stat-card !p-6 border-amber-400/20 bg-gradient-to-br from-amber-400/[0.05] to-transparent">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2">Paused</p>
                <p class="text-3xl font-black tracking-tighter text-amber-400" id="wh-stat-paused">—</p>
            </div>
            <div class="stat-card !p-6 border-primary/20 bg-gradient-to-br from-primary/[0.05] to-transparent">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40 mb-2">Total Hooks</p>
                <p class="text-3xl font-black tracking-tighter text-primary" id="wh-stat-total">—</p>
            </div>
        </div>

        <!-- Hook List -->
        <div class="stat-card !p-0 overflow-hidden" style="background-color: var(--glass-bg); border: 1px solid var(--border-color);">
            <div class="p-6 border-b flex items-center justify-between" style="border-color: var(--border-color); background-color: var(--surface-glass);">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-primary rounded-full animate-pulse shadow-[0_0_8px_#7c6aff]"></div>
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em]">Registered Endpoints</h4>
                </div>
                <button onclick="AdminApp.initWebhookHub()" class="p-2 rounded-xl bg-white/5 hover:bg-primary hover:text-white transition-all border border-white/5" title="Refresh all">
                    <i class="ph-bold ph-arrow-clockwise text-sm"></i>
                </button>
            </div>
            <div id="webhook-list-container" class="divide-y" style="border-color: var(--border-color);">
                <div class="p-20 text-center animate-pulse">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-30">Initializing Hook Registry...</p>
                </div>
            </div>
        </div>

        <!-- Add Webhook Modal (inline) -->
        <div id="add-webhook-modal" class="hidden fixed inset-0 z-[200] flex items-center justify-center p-6 bg-black/70 backdrop-blur-md">
            <div class="w-full max-w-lg rounded-[2.5rem] overflow-hidden shadow-2xl" style="background-color: var(--surface); border: 1px solid var(--border-color);">
                <div class="p-8 border-b flex items-center justify-between" style="border-color: var(--border-color);">
                    <h3 class="text-lg font-black uppercase tracking-tight">Register <span class="gradient-text">Webhook</span></h3>
                    <button onclick="document.getElementById('add-webhook-modal').classList.add('hidden')" class="w-9 h-9 rounded-xl bg-white/5 hover:bg-red-500/10 hover:text-red-400 transition-all flex items-center justify-center border border-white/5">
                        <i class="ph-bold ph-x text-sm"></i>
                    </button>
                </div>
                <div class="p-8 space-y-6">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest opacity-60">Hook Name</label>
                        <input type="text" id="wh-new-name" placeholder="e.g. Slack Notifier" class="w-full bg-[var(--surface-2)] border border-[var(--border-color)] rounded-2xl p-5 font-bold text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all" style="color: var(--text-main);">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest opacity-60">Endpoint URL</label>
                        <input type="url" id="wh-new-url" placeholder="https://hooks.example.com/..." class="w-full bg-[var(--surface-2)] border border-[var(--border-color)] rounded-2xl p-5 font-bold text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all font-mono" style="color: var(--text-main);">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest opacity-60">Events to Trigger</label>
                        <div class="grid grid-cols-2 gap-3" id="wh-event-checkboxes">
                            <?php foreach (['user.created','user.deleted','message.flagged','channel.created','report.submitted','login.failed'] as $evt): ?>
                            <label class="flex items-center gap-3 p-3 rounded-2xl border border-[var(--border-color)] hover:border-primary/40 cursor-pointer transition-all bg-[var(--surface-2)]">
                                <input type="checkbox" class="wh-event-check accent-primary" value="<?= $evt ?>">
                                <span class="text-[10px] font-black uppercase tracking-widest"><?= $evt ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest opacity-60">Secret Key (HMAC)</label>
                        <input type="text" id="wh-new-secret" placeholder="Optional signing secret..." class="w-full bg-[var(--surface-2)] border border-[var(--border-color)] rounded-2xl p-5 font-bold text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all font-mono" style="color: var(--text-main);">
                    </div>
                    <button onclick="AdminApp.saveNewWebhook()" class="w-full btn-primary !p-5 !rounded-2xl shadow-xl shadow-primary/20 flex items-center justify-center gap-3 hover:scale-[1.02] transition-all">
                        <i class="ph-bold ph-webhook text-lg"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Register & Start Monitoring</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Error Details Drawer -->
        <div id="webhook-error-drawer" class="hidden fixed inset-y-0 right-0 w-full max-w-lg z-[200] shadow-2xl flex flex-col" style="background-color: var(--surface); border-left: 1px solid var(--border-color);">
            <div class="p-8 border-b flex items-center justify-between shrink-0" style="border-color: var(--border-color);">
                <div>
                    <h3 class="text-lg font-black uppercase tracking-tight text-red-400">Hook <span class="text-white">Error Log</span></h3>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mt-1" id="wh-drawer-name">—</p>
                </div>
                <button onclick="AdminApp.closeWebhookErrorDrawer()" class="w-9 h-9 rounded-xl bg-white/5 hover:bg-red-500/10 hover:text-red-400 transition-all flex items-center justify-center border border-white/5">
                    <i class="ph-bold ph-x text-sm"></i>
                </button>
            </div>
            <div class="p-8 flex-grow overflow-y-auto custom-scrollbar space-y-4" id="wh-drawer-errors">
                <p class="text-center opacity-30 text-[10px] uppercase font-black tracking-widest py-20">No errors loaded.</p>
            </div>
            <div class="p-6 border-t shrink-0" style="border-color: var(--border-color);">
                <button onclick="AdminApp.retryWebhook(AdminApp._drawerHookId)" class="w-full btn-primary !p-4 !rounded-2xl flex items-center justify-center gap-3">
                    <i class="ph-bold ph-arrow-clockwise text-lg"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Retry All Failed Deliveries</span>
                </button>
            </div>
        </div>
    </div>
</div>
