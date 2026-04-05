<?php use Aether\Session; ?>
<!-- Section: Channels (Network Graph) -->
<div id="section-channels" class="section active space-y-8 animate-in fade-in duration-500">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight mb-2">Network Architecture</h2>
            <p class="font-medium" style="color: var(--text-muted);">Manage channel hierarchies and message propagation nodes.</p>
        </div>
        <div class="flex gap-3">
            <button class="btn-primary">
                <i class="ph-bold ph-circles-three-plus"></i>
                Create Channel
            </button>
        </div>
    </div>

    <!-- Cluster Map Placeholder -->
    <div class="stat-card !p-0 min-h-[500px] flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-30 pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-primary/20 blur-3xl animate-pulse rounded-full"></div>
            <div class="absolute bottom-1/3 right-1/4 w-64 h-64 bg-blue-500/10 blur-[100px] rounded-full"></div>
        </div>
        <div class="text-center relative z-10 px-8">
            <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center text-primary mx-auto mb-6 border border-primary/20 shadow-[0_0_40px_rgba(124,106,255,0.2)] animate-bounce font-bold tracking-widest uppercase">
                <i class="ph ph-graph text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold mb-3 tracking-tight">Interactive Cluster Map</h3>
            <p class="max-w-md mx-auto leading-relaxed mb-8" style="color: var(--text-muted);">Visualize live message propagation across global nodes. Select a node to adjust shard allocation or view localized logs.</p>
            <div class="flex justify-center gap-4">
                <button class="btn-secondary !text-xs !py-1.5 !px-4">Show Heatmap</button>
                <button class="btn-secondary !text-xs !py-1.5 !px-4">Shard Overlay</button>
            </div>
        </div>
    </div>

    <!-- Active Propagation Table -->
    <div class="stat-card !p-0 overflow-hidden">
        <div class="p-6 border-b flex justify-between items-center" style="border-color: var(--border-color);">
            <h3 class="font-bold flex items-center gap-2">
                Priority Channels
                <span class="badge-neutral border-primary/20 text-primary">Live Optimization</span>
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b" style="border-color: var(--border-color); background-color: var(--glass-bg);">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Channel Name</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Type</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Members</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Created</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="channels-table-body" class="divide-y" style="border-color: var(--border-color);">
                    <!-- Rows injected by JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Channel Modal -->
    <div id="create-channel-modal" class="modal-overlay hidden">
        <div class="modal-card">
            <div class="flex items-center justify-between p-6 border-b border-white/5">
                <h3 class="text-xl font-bold text-white tracking-tight">Provision New Node</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-white transition-colors p-2"><i class="ph ph-x text-2xl"></i></button>
            </div>
            <form id="create-channel-form" class="p-8 space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Channel Name</label>
                    <input type="text" name="name" required class="w-full bg-transparent border rounded-2xl p-4 text-white focus:outline-none focus:border-primary transition-all font-medium" style="border-color: var(--border-color);" placeholder="e.g. engineering-alpha">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Propagation Type</label>
                    <select name="type" required class="w-full bg-[#111827] border rounded-2xl p-4 text-white focus:outline-none focus:border-primary transition-all font-medium" style="border-color: var(--border-color);">
                        <option value="public">Global Public</option>
                        <option value="private">Encrypted Private</option>
                    </select>
                </div>
                <div class="pt-6 flex gap-3">
                    <button type="button" onclick="closeModal()" class="btn-secondary flex-1 !justify-center py-4">Cancel</button>
                    <button type="submit" class="btn-primary flex-1 !justify-center py-4 text-lg">Initialize Node</button>
                </div>
            </form>
        </div>
    </div>
</div>
