<?php
use App\Settings;
use Aether\Session;

$allSettings = Settings::getAll();
?>
<!-- Section: Settings (Control Center) -->
<div id="section-settings" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter mb-2 uppercase">Control <span class="gradient-text">Center</span></h2>
            <p class="font-bold text-sm opacity-60 tracking-tight uppercase" style="color: var(--text-muted);">Platform orchestration and global governance protocols.</p>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <button class="btn-secondary flex-1 md:flex-none justify-center" onclick="AdminApp.switchSection('settings')">
                <i class="ph-bold ph-arrow-clockwise text-lg"></i>
                Reset
            </button>
            <button class="btn-primary flex-1 md:flex-none justify-center" onclick="AdminApp.saveSettings()">
                <i class="ph-bold ph-floppy-disk text-lg"></i>
                Synchronize
            </button>
        </div>
    </div>

    <!-- Premium Module Tabs -->
    <div class="flex gap-8 border-b border-white/5" id="settings-tabs">
        <button class="tab-btn active" data-tab="general" onclick="AdminApp.switchTab('settings', 'general')">
            General Operations
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn" data-tab="security" onclick="AdminApp.switchTab('settings', 'security')">
            Security Governance
            <div class="tab-underline"></div>
        </button>
        <button class="tab-btn" data-tab="system" onclick="AdminApp.switchTab('settings', 'system')">
            System Intelligence
            <div class="tab-underline"></div>
        </button>
    </div>

    <!-- Tab Content: General -->
    <div id="tab-content-general" class="tab-content space-y-8 animate-in fade-in duration-700">
        <div class="stat-card">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Global Feature Toggles</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Maintenance Mode -->
                <div class="flex items-center justify-between p-6 rounded-[2rem] bg-white/5 border border-white/5 hover:border-primary/30 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-orange-500/10 flex items-center justify-center text-orange-500 border border-orange-500/20 group-hover:bg-orange-500 group-hover:text-white transition-all">
                            <i class="ph-bold ph-warning-octagon text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-black text-sm uppercase tracking-tight">Maintenance Protocol</p>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-0.5">Redirect public traffic</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" onchange="AdminApp.toggleSetting('maintenance_mode', this.checked)" <?= Settings::get('maintenance_mode') === 'on' ? 'checked' : '' ?>>
                        <div class="w-14 h-7 bg-white/10 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary shadow-lg"></div>
                    </label>
                </div>

                <!-- Registration -->
                <div class="flex items-center justify-between p-6 rounded-[2rem] bg-white/5 border border-white/5 hover:border-primary/30 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-400/20 group-hover:bg-blue-400 group-hover:text-white transition-all">
                            <i class="ph-bold ph-user-plus text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-black text-sm uppercase tracking-tight">Public Auth Stream</p>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-0.5">Open user registration</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" onchange="AdminApp.toggleSetting('public_registration', this.checked)" <?= Settings::get('public_registration') === 'on' ? 'checked' : '' ?>>
                        <div class="w-14 h-7 bg-white/10 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary shadow-lg"></div>
                    </label>
                </div>

                <!-- Ad Serving -->
                <div class="flex items-center justify-between p-6 rounded-[2rem] bg-white/5 border border-white/5 hover:border-primary/30 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-400 border border-purple-400/20 group-hover:bg-purple-500 group-hover:text-white transition-all">
                            <i class="ph-bold ph-broadcast text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-black text-sm uppercase tracking-tight">Ad Pipeline</p>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-0.5">Global stream orchestration</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" onchange="AdminApp.toggleSetting('ad_serving', this.checked)" <?= Settings::get('ad_serving') === 'on' ? 'checked' : '' ?>>
                        <div class="w-14 h-7 bg-white/10 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary shadow-lg"></div>
                    </label>
                </div>
            </div>
        </div>

        <div class="p-8 rounded-[2.5rem] bg-primary/5 border border-primary/10 flex items-center gap-6">
            <div class="w-14 h-14 rounded-2xl bg-primary/20 flex items-center justify-center text-primary border border-primary/20">
                <i class="ph-bold ph-info text-2xl"></i>
            </div>
            <div>
                <h4 class="font-black text-sm uppercase tracking-tight">Governance Notice</h4>
                <p class="text-[11px] font-bold opacity-60 leading-relaxed max-w-2xl mt-1">Modifying global feature toggles affects the entire Aether ecosystem in real-time. Please ensure all synchronization protocols are verified before deployment.</p>
            </div>
        </div>
    </div>

    <!-- Tab Content: Security -->
    <div id="tab-content-security" class="tab-content hidden space-y-8 animate-in fade-in duration-700">
        <div class="stat-card">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8 opacity-60">Throttling & Session Persistence</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Session Lifetime (Seconds)</label>
                    <input type="number" id="session_timeout" value="<?= Settings::get('session_timeout', 3600) ?>" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 font-bold text-sm focus:border-primary outline-none transition-all">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Rate Limit (Req/Min)</label>
                    <input type="number" id="rate_limit" value="<?= Settings::get('rate_limit', 60) ?>" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 font-bold text-sm focus:border-primary outline-none transition-all">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Auth Entropy Threshold</label>
                    <input type="number" id="auth_retry_limit" value="<?= Settings::get('auth_retry_limit', 5) ?>" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 font-bold text-sm focus:border-primary outline-none transition-all">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Packet Blocklist (IP CSV)</label>
                    <input type="text" id="ip_blocklist" value="<?= Settings::get('ip_blocklist', '127.0.0.1') ?>" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 font-bold text-sm focus:border-primary outline-none transition-all">
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content: System -->
    <div id="tab-content-system" class="tab-content hidden grid grid-cols-1 lg:grid-cols-3 gap-8 animate-in fade-in duration-700">
        <div class="lg:col-span-1 glass-card border-primary/20 bg-gradient-to-br from-primary/10 to-transparent">
            <h3 class="text-xl font-black uppercase tracking-tight mb-8">Node <span class="gradient-text">Health</span></h3>
            <div class="space-y-6">
                <div class="flex justify-between items-center group">
                    <span class="text-xs font-black uppercase tracking-widest opacity-60 flex items-center gap-3">
                        <i class="ph ph-cpu text-lg text-primary"></i> Core Engine
                    </span>
                    <span class="text-xs font-black">v4.2.0-STABLE</span>
                </div>
                <div class="w-full h-1.5 bg-primary/10 rounded-full relative overflow-hidden">
                    <div class="absolute inset-0 bg-primary w-[98%] shadow-[0_0_10px_#7c6aff]"></div>
                </div>
                <p class="text-[10px] font-bold opacity-60 leading-relaxed uppercase tracking-widest">Master clusters are synchronized.</p>
                
                <button class="w-full mt-6 btn-primary !bg-white !text-primary !shadow-none !justify-center py-3 text-[10px] font-black uppercase tracking-widest border border-primary/20 hover:!bg-primary hover:!text-white transition-all" onclick="toast.info('Diagnostic Boot', 'Initializing performance audit sequence...')">
                    Run Performance Audit
                </button>
            </div>
        </div>

        <div class="lg:col-span-2 stat-card !p-0 overflow-hidden">
            <div class="p-8 border-b border-white/5 bg-white/5">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] opacity-60">Activity Audit Trail</h3>
            </div>
            <div class="p-8 space-y-6 max-h-[300px] overflow-y-auto custom-scrollbar">
                <?php
                $db = Aether\Database::getConnection();
                $stmt = $db->query("SELECT l.*, a.username FROM logs l LEFT JOIN auth a ON l.user_id = a.id WHERE l.action LIKE 'Updated setting%' ORDER BY l.created_at DESC LIMIT 10");
                $auditLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($auditLogs):
                    foreach ($auditLogs as $log):
                ?>
                <div class="flex gap-5 group">
                    <div class="w-1.5 h-1.5 rounded-full bg-primary mt-2 group-hover:scale-150 transition-all"></div>
                    <div>
                        <div class="flex items-center gap-3">
                            <p class="text-[10px] font-black uppercase tracking-widest text-primary"><?= htmlspecialchars($log['username'] ?? 'System') ?></p>
                            <span class="text-white/20 text-[10px]">|</span>
                            <span class="text-[9px] font-bold text-gray-500 tracking-widest"><?= date('M d, H:i', strtotime($log['created_at'])) ?></span>
                        </div>
                        <p class="text-xs font-bold mt-1 opacity-80"><?= htmlspecialchars($log['action']) ?></p>
                    </div>
                </div>
                <?php endforeach; else: ?>
                <p class="p-10 text-center text-[10px] font-black uppercase tracking-widest opacity-40 italic">Sync ledger is currently empty</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
