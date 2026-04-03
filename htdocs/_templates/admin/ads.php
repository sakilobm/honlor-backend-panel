<main class="lg:pl-72 pt-24 pb-12 px-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-white mb-2">Ads Manager</h1>
            <p class="text-gray-500 font-medium">Control and monitor your advertising campaigns</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="btn-primary" onclick="openModal('create-ad-modal')">
                <i class="ph ph-plus text-lg"></i>
                Create Campaign
            </button>
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="bg-surface border border-white/5 rounded-[32px] overflow-hidden shadow-2xl">
        <table class="w-full text-left">
            <thead class="bg-white/5 border-b border-white/5">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Campaign Name</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Budget</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">CTR</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="ads-table-body">
                <!-- Data injected by JS -->
            </tbody>
        </table>
    </div>

    <!-- Create Campaign Modal -->
    <div id="create-ad-modal" class="modal-overlay hidden">
        <div class="modal-card">
            <div class="flex items-center justify-between p-6 border-b border-white/5">
                <h3 class="text-lg font-bold text-white">Create New Campaign</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-white transition-colors"><i class="ph ph-x text-xl"></i></button>
            </div>
            <form id="create-ad-form" class="p-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Campaign Name</label>
                    <input type="text" name="name" required class="w-full bg-white/5 border border-white/10 rounded-xl p-3 text-white focus:outline-none focus:border-primary transition-all">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Type</label>
                        <select name="type" required class="w-full bg-white/5 border border-white/10 rounded-xl p-3 text-white focus:outline-none">
                            <option value="Social">Social</option>
                            <option value="Search">Search</option>
                            <option value="Display">Display</option>
                            <option value="Email">Email</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Budget ($)</label>
                        <input type="number" step="0.01" name="budget" required class="w-full bg-white/5 border border-white/10 rounded-xl p-3 text-white focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Start Date</label>
                        <input type="date" name="start_date" required class="w-full bg-white/5 border border-white/10 rounded-xl p-3 text-white focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">End Date</label>
                        <input type="date" name="end_date" required class="w-full bg-white/5 border border-white/10 rounded-xl p-3 text-white focus:outline-none">
                    </div>
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeModal()" class="btn-secondary w-full">Cancel</button>
                    <button type="submit" class="btn-primary w-full">Create Campaign</button>
                </div>
            </form>
        </div>
    </div>
</main>
