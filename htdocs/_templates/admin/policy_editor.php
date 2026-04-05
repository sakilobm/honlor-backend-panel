<?php use Aether\Session; ?>
<!-- Section: Policy Editor -->
<div id="section-policy-editor" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight mb-2 uppercase">Governance <span class="gradient-text">Studio</span></h2>
            <p class="font-medium" style="color: var(--text-muted);">Draft and deploy global platform policies and legal frameworks.</p>
        </div>
        <div class="flex gap-3">
            <button class="btn-secondary" onclick="AdminApp.previewPolicy()">
                <i class="ph-bold ph-eye"></i>
                Preview
            </button>
            <button class="btn-primary" onclick="AdminApp.submitPolicy()">
                <i class="ph-bold ph-rocket-launch"></i>
                Push to Production
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <div class="lg:col-span-3 space-y-6">
            <div class="glass-card !p-0 overflow-hidden">
                <div class="p-4 border-b flex justify-between items-center bg-white/5" style="border-color: var(--border-color);">
                    <div class="flex gap-4" id="policy-tabs">
                        <button id="policy-tab-privacy" onclick="AdminApp.switchPolicy('privacy')" class="text-xs font-bold uppercase tracking-widest text-primary border-b-2 border-primary pb-3 flex items-center gap-2">
                            <i class="ph-bold ph-shield-check text-blue-400"></i>
                            Privacy Policy
                        </button>
                        <button id="policy-tab-terms" onclick="AdminApp.switchPolicy('terms')" class="text-xs font-bold uppercase tracking-widest opacity-40 hover:opacity-100 transition-all pb-3 flex items-center gap-2">
                            <i class="ph-bold ph-scroll text-orange-400"></i>
                            Terms of Service
                        </button>
                        <button id="policy-tab-community" onclick="AdminApp.switchPolicy('community')" class="text-xs font-bold uppercase tracking-widest opacity-40 hover:opacity-100 transition-all pb-3 flex items-center gap-2">
                            <i class="ph-bold ph-users-three text-green-400"></i>
                            Community Guidelines
                        </button>
                    </div>
                    <span class="text-[10px] font-black uppercase opacity-40">Last Saved: 4h ago</span>
                </div>
                <div class="p-8">
                    <textarea id="policy-editor" class="w-full h-[600px] bg-transparent border-none outline-none font-medium leading-relaxed resize-none custom-scrollbar" style="color: var(--text-main);">
## Privacy & Data Protection Framework

### 1. Data Collection Protocols
The Aether ecosystem operates on a principle of radical transparency. We collect telemetry data only to ensure node stability and cross-chain verification.

### 2. Encryption Standards
All identity records in the Vault are encrypted using AES-256-GCM. Private keys are never stored on centralized edge clusters.

### 3. User Sovereignty
Users maintain 100% ownership of their data packets. Deletion requests are processed within a 24-hour governance window.

[--- Draft Content Below ---]
                    </textarea>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="stat-card">
                <h3 class="text-sm font-bold uppercase tracking-widest mb-6" style="color: var(--text-muted);">Draft Status</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium">Readability</span>
                        <span class="badge-success">Optimal</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium">Compliance</span>
                        <span class="badge-warning">Check Required</span>
                    </div>
                </div>
                <div class="mt-6 pt-6 border-t" style="border-color: var(--border-color);">
                    <p class="text-[10px] font-medium leading-relaxed opacity-60">Changes to the policy framework require a dual-signature confirmation from the Infrastructure lead.</p>
                </div>
            </div>

            <div class="stat-card bg-primary/10 border-primary/20">
                <h3 class="text-xs font-bold uppercase tracking-widest text-primary mb-4">Governance Tip</h3>
                <p class="text-[11px] font-medium leading-relaxed" style="color: var(--text-muted);">
                    Use markdown headers to ensure that policy sections are correctly indexed by our automated auditing nodes.
                </p>
            </div>
        </div>
    </div>
</div>
