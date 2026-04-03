<?php
use App\Settings;
use Aether\Session;

$allSettings = Settings::getAll();
?>
<!-- Section: Settings -->
<div id="section-settings" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight mb-2">System Control</h2>
            <p class="font-medium" style="color: var(--text-muted);">Manage global features, security protocols, and system states.</p>
        </div>
        <div class="flex gap-3">
            <button class="btn-secondary" onclick="location.reload()">
                <i class="ph-bold ph-arrow-clockwise"></i>
                Reset Values
            </button>
            <button class="btn-primary">
                <i class="ph-bold ph-floppy-disk"></i>
                Save Changes
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Settings Column -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Feature Toggles -->
            <div class="stat-card">
                <h3 class="text-xl font-bold mb-8 flex items-center gap-2">
                    <i class="ph-bold ph-toggle-left text-primary"></i>
                    Feature Governance
                </h3>
                
                <div class="space-y-6">
                    <!-- Toggle: Maintenance Mode -->
                    <div class="flex items-center justify-between p-6 rounded-[2rem] border hover:bg-white/5 transition-all" style="border-color: var(--border-color);">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-orange-500/10 flex items-center justify-center text-orange-400">
                                <i class="ph ph-warning-octagon text-2xl"></i>
                            </div>
                            <div>
                                <p class="font-bold text-base">Global Maintenance Mode</p>
                                <p class="text-xs font-medium text-gray-500">Redirect all public traffic to a holding page.</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" 
                                   onchange="AdminApp.toggleSetting('maintenance_mode', this.checked)"
                                   <?= Settings::get('maintenance_mode') === 'on' ? 'checked' : '' ?>>
                            <div class="w-14 h-7 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary shadow-[0_0_15px_rgba(124,106,255,0.3)]"></div>
                        </label>
                    </div>

                    <!-- Toggle: Public Registration -->
                    <div class="flex items-center justify-between p-6 rounded-[2rem] border hover:bg-white/5 transition-all" style="border-color: var(--border-color);">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400">
                                <i class="ph ph-user-plus text-2xl"></i>
                            </div>
                            <div>
                                <p class="font-bold text-base">Public Registration</p>
                                <p class="text-xs font-medium text-gray-500">Allow new users to create accounts without invite.</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" 
                                   onchange="AdminApp.toggleSetting('public_registration', this.checked)"
                                   <?= Settings::get('public_registration') === 'on' ? 'checked' : '' ?>>
                            <div class="w-14 h-7 bg-gray-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>

                    <!-- Toggle: Ad Serving -->
                    <div class="flex items-center justify-between p-6 rounded-[2rem] border hover:bg-white/5 transition-all" style="border-color: var(--border-color);">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-400">
                                <i class="ph ph-broadcast text-2xl"></i>
                            </div>
                            <div>
                                <p class="font-bold text-base">Global Ad Pipeline</p>
                                <p class="text-xs font-medium text-gray-500">Enable or disable all active ad campaigns system-wide.</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" 
                                   onchange="AdminApp.toggleSetting('ad_serving', this.checked)"
                                   <?= Settings::get('ad_serving') === 'on' ? 'checked' : '' ?>>
                            <div class="w-14 h-7 bg-gray-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Advanced Configuration -->
            <div class="stat-card">
                <h3 class="text-xl font-bold mb-8">Security & Performance</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Session Timeout (Seconds)</label>
                        <input type="number" value="<?= Settings::get('session_timeout', 3600) ?>" 
                               class="w-full bg-transparent border rounded-2xl p-4 text-white font-medium focus:border-primary outline-none transition-all" style="border-color: var(--border-color);">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Auth Retry Limit</label>
                        <input type="number" value="5" 
                               class="w-full bg-transparent border rounded-2xl p-4 text-white font-medium focus:border-primary outline-none transition-all" style="border-color: var(--border-color);">
                    </div>
                </div>
            </div>
            <div class="p-4 bg-primary/5 border border-primary/10 rounded-3xl flex items-center gap-4">
                 <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary">
                    <i class="ph ph-info font-bold text-xl"></i>
                 </div>
                 <p class="text-xs font-medium text-gray-400 leading-relaxed">Changes to feature toggles take effect immediately across all nodes. Advanced configuration requires a manual 'Save Changes' commitment.</p>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-8">
            <div class="stat-card bg-primary/10 border-primary/20 shadow-[0_20px_50px_rgba(124,106,255,0.15)]">
                <h4 class="font-bold text-primary mb-4 uppercase tracking-widest text-xs">System Health</h4>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold">Core Engine</span>
                    <span class="text-green-400 text-xs font-bold uppercase tracking-widest">v4.2.0-STABLE</span>
                </div>
                <div class="w-full h-1 bg-primary/20 rounded-full mb-6">
                    <div class="bg-primary h-full w-[100%] rounded-full shadow-[0_0_8px_rgba(124,106,255,0.5)]"></div>
                </div>
                <p class="text-[11px] font-medium leading-relaxed" style="color: var(--text-muted);">
                    The Honlor Core is running at optimal capacity. All feature modules are currently synchronized with the master database.
                </p>
                <button class="w-full mt-6 btn-primary !bg-white !text-primary !shadow-none !justify-center py-3 text-xs">
                    Run Performance Audit
                </button>
            </div>

            <div class="stat-card">
                 <h3 class="text-sm font-bold mb-6 uppercase tracking-widest" style="color: var(--text-muted);">Audit Trail</h3>
                 <div class="space-y-4">
                     <div class="flex gap-4">
                         <div class="w-1.5 h-1.5 rounded-full bg-primary mt-2"></div>
                         <div>
                             <p class="text-xs font-bold">Admin Marcus</p>
                             <p class="text-[10px] text-gray-500">Updated Ad Serving: ON</p>
                             <p class="text-[9px] text-gray-600 uppercase font-bold mt-0.5">14m ago</p>
                         </div>
                     </div>
                     <div class="flex gap-4">
                         <div class="w-1.5 h-1.5 rounded-full bg-primary mt-2"></div>
                         <div>
                             <p class="text-xs font-bold">System Root</p>
                             <p class="text-[10px] text-gray-500">Backup sequence complete</p>
                             <p class="text-[9px] text-gray-600 uppercase font-bold mt-0.5">1h ago</p>
                         </div>
                     </div>
                 </div>
            </div>
        </div>
    </div>
</div>
