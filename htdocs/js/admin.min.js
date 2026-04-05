/**
 * Honlor Admin App
 * ================
 * Handles interactive logic for Dashboard, Identity Vault, Channels, and Moderation.
 * Optimized for High-Fidelity UI Overhaul (v10).
 */

const AdminApp = {
    chart: null,
    currentRange: 7,

    init: function() {
        console.log("AdminApp Initializing...");
        const params = new URLSearchParams(window.location.search);
        const page = params.get('page') || 'dashboard';

        switch(page) {
            case 'dashboard':
                this.initDashboard();
                break;
            case 'users':
                this.initUsers();
                break;
            case 'channels':
                this.initChannels();
                break;
            case 'messages':
                this.initMessages();
                break;
            case 'ads':
                this.initAdsManager();
                break;
            case 'deletion_requests':
                this.initDeletionRequests();
                break;
            case 'reports':
                this.initReports();
                break;
            case 'logs':
                this.initLogs();
                break;
        }
        
        // Handle notifications dropdown close on click outside
        window.onclick = (event) => {
            if (!event.target.matches('.ph-bell') && !event.target.closest('.notification-pane')) {
                const pane = document.getElementById('notification-pane');
                if (pane) pane.classList.add('hidden');
            }
        };
    },

    toggleNotifications: function() {
        const pane = document.getElementById('notification-pane');
        if (pane) {
            pane.classList.toggle('hidden');
        }
    },

    switchSection: function(section) {
        window.location.href = `/admin?page=${section}`;
    },

    /**
     * Dashboard Logic
     */
    initDashboard: function(range = 7) {
        this.currentRange = range;
        
        ApiClient.get('dashboard', 'metrics', { range: range }).then(data => {
            if (document.getElementById('stat-total-users')) {
                document.getElementById('stat-total-users').innerText = data.total_users.toLocaleString();
            }
            if (document.getElementById('stat-today-messages')) {
                document.getElementById('stat-today-messages').innerText = data.messages_today.toLocaleString();
            }
            if (document.getElementById('stat-active-ads')) {
                document.getElementById('stat-active-ads').innerText = data.active_ads || 0;
            }
            if (document.getElementById('stat-active-channels')) {
                document.getElementById('stat-active-channels').innerText = data.active_channels || 0;
            }

            this.renderGrowthChart(data.growth_data);
            this.loadRecentActivity();
            this.loadRecentMembers();
        }).catch(err => {
            console.error(err);
        });
    },

    loadRecentActivity: function() {
        const target = document.getElementById('recent-activity-list');
        if (!target) return;

        ApiClient.get('dashboard', 'activity').then(data => {
            let html = '';
            data.activity.forEach(item => {
                html += `
                    <div class="flex items-start gap-4 p-4 rounded-2xl hover:bg-white/5 transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <i class="ph-bold ph-activity text-lg"></i>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm font-bold truncate">${item.action}</p>
                            <p class="text-[11px] text-gray-500 font-medium">${item.username || 'System'} • ${item.created_at}</p>
                        </div>
                    </div>
                `;
            });
            target.innerHTML = html || '<p class="text-center text-gray-500 py-8">No recent activity</p>';
        });
    },

    loadRecentMembers: function() {
        // The recent members grid is the last grid in the overview section
        const target = document.querySelector('#section-overview .grid-cols-2.md\\:grid-cols-4.lg\\:grid-cols-6');
        if (!target) return;

        ApiClient.get('users', 'recent').then(data => {
            let html = '';
            data.users.forEach(user => {
                const statusBadge = user.active == 1 ? 'badge-success' : 'badge-neutral';
                const statusText = user.active == 1 ? 'Online' : 'Offline';
                const name = user.firstname ? user.firstname : user.username;
                
                html += `
                    <div class="border p-4 rounded-3xl text-center group transition-all hover:bg-glass-white" style="border-color: var(--border-color); background-color: var(--surface);">
                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=${user.username}" class="w-16 h-16 mx-auto rounded-2xl mb-3 bg-blue-500/10 p-1 group-hover:scale-105 transition-transform" alt="Avatar">
                        <p class="font-bold text-sm truncate">${name}</p>
                        <span class="${statusBadge} mt-2 inline-block">${statusText}</span>
                    </div>
                `;
            });
            target.innerHTML = html || '<p class="text-center text-gray-500 py-8 col-span-full">No recent members</p>';
        });
    },

    renderGrowthChart: function(growthData) {
        const canvas = document.getElementById('growthChart');
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        if (this.chart) this.chart.destroy();

        const isLight = document.body.classList.contains('light');
        const primaryColor = '#7c6aff';

        this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: growthData.labels,
                datasets: [{
                    label: 'New Users',
                    data: growthData.data,
                    borderColor: primaryColor,
                    backgroundColor: 'rgba(124, 106, 255, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: primaryColor,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: isLight ? '#fff' : '#111827',
                        titleColor: isLight ? '#111827' : '#fff',
                        bodyColor: isLight ? '#4b5563' : '#9ca3af',
                        borderColor: 'rgba(124, 106, 255, 0.2)',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: isLight ? 'rgba(0,0,0,0.05)' : 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#9ca3af', font: { family: 'Outfit' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#9ca3af', font: { family: 'Outfit' } }
                    }
                }
            }
        });
    },

    /**
     * Users (Identity Vault) Logic
     */
    initUsers: function() {
        this.userPage = 1;
        this.loadUserList();
        
        const filterInput = document.getElementById('user-filter');
        if (filterInput) {
            let timeout;
            filterInput.oninput = () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    this.userPage = 1;
                    this.loadUserList(1, filterInput.value);
                }, 300);
            };
        }
    },

    changeUserPage: function(delta) {
        const newPage = this.userPage + delta;
        if (newPage < 1) return;
        this.userPage = newPage;
        const filter = document.getElementById('user-filter')?.value || '';
        this.loadUserList(this.userPage, filter);
    },

    loadUserList: function(page = 1, filter = '') {
        const tbody = document.getElementById('users-table-body');
        if (!tbody) return;

        ApiClient.get('users', 'list', { page, filter }).then(data => {
            let html = '';
            data.users.forEach(user => {
                const badge = user.blocked == 1 ? 'badge-danger' : (user.active == 1 ? 'badge-success' : 'badge-neutral');
                const status = user.blocked == 1 ? 'Blocked' : (user.active == 1 ? 'Active' : 'Inactive');
                
                html += `
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=${user.username}" class="w-10 h-10 rounded-xl bg-primary/10 p-0.5" alt="Avatar">
                                <div>
                                    <p class="font-bold text-sm">${user.firstname || user.username} ${user.lastname || ''}</p>
                                    <p class="text-[11px] text-gray-500 font-medium">${user.email}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="${badge}">${status}</span></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-1.5 rounded-full bg-gray-800">
                                    <div class="bg-primary h-full w-[${user.active == 1 ? 85 : 10}%] rounded-full"></div>
                                </div>
                                <span class="text-[10px] font-bold text-gray-400">${user.active == 1 ? 85 : 10}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-semibold text-gray-400">${new Date(user.created_at).toLocaleDateString()}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="AdminApp.openDrawer('user', '${user.id}')" class="p-2 hover:bg-primary/10 hover:text-primary rounded-xl transition-all text-gray-500"><i class="ph ph-eye text-lg"></i></button>
                                <button onclick="AdminApp.executeAction('toggle_block', '${user.id}')" class="p-2 hover:bg-red-500/10 hover:text-red-400 rounded-xl transition-all text-gray-500"><i class="ph ph-prohibit text-lg"></i></button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || '<tr><td colspan="5" class="p-8 text-center text-gray-500">No users found</td></tr>';
            
            if (document.getElementById('users-count-text')) {
                const start = (page - 1) * 10 + 1;
                const end = Math.min(page * 10, data.total);
                document.getElementById('users-count-text').innerText = `Showing ${start}-${end} of ${data.total} identities`;
            }
        });
    },

    /**
     * Channels Logic
     */
    initChannels: function() {
        this.loadChannelList();
        
        const form = document.getElementById('create-channel-form');
        if (form) {
            form.onsubmit = (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                const payload = Object.fromEntries(formData.entries());

                ApiClient.post('channels', 'create', payload).then(res => {
                    toast.success('Success', res.message);
                    closeModal();
                    form.reset();
                    this.loadChannelList();
                }).catch(err => {
                    toast.error('Error', err.error || 'Failed to create channel.');
                });
            };
        }
    },

    loadChannelList: function() {
        const tbody = document.getElementById('channels-table-body');
        if (!tbody) return;

        ApiClient.get('channels', 'list').then(data => {
            let html = '';
            data.channels.forEach(ch => {
                html += `
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 font-bold text-sm text-primary"># ${ch.name}</td>
                        <td class="px-6 py-4"><span class="badge-neutral uppercase">${ch.type}</span></td>
                        <td class="px-6 py-4 text-xs font-semibold text-gray-400">${ch.member_count} Members</td>
                        <td class="px-6 py-4 text-xs font-semibold text-gray-400">${new Date(ch.created_at).toLocaleDateString()}</td>
                        <td class="px-6 py-4 text-right">
                            <button onclick="AdminApp.deleteChannel('${ch.id}')" class="p-2 hover:bg-red-500/10 hover:text-red-400 rounded-xl transition-all text-gray-500"><i class="ph ph-trash text-lg"></i></button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || '<tr><td colspan="5" class="p-8 text-center text-gray-500">No channels found</td></tr>';
        });
    },

    deleteChannel: function(id) {
        if (!confirm('Permanently decommission this node?')) return;
        ApiClient.post('channels', 'delete', { id }).then(res => {
            toast.success('Confirmed', 'Node removed from network.');
            this.loadChannelList();
        });
    },

    /**
     * Messages Logic
     */
    initMessages: function() {
        this.loadMessageList();
    },

    loadMessageList: function() {
        const tbody = document.getElementById('messages-table-body');
        if (!tbody) return;

        ApiClient.get('messages', 'list').then(data => {
            let html = '';
            data.messages.forEach(msg => {
                const statusBadge = msg.status === 'flagged' ? 'badge-warning' : (msg.status === 'deleted' ? 'badge-danger' : 'badge-success');
                
                html += `
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 text-[11px] font-bold text-primary"># ${msg.channel_name}</td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-sm mb-0.5">${msg.username}</p>
                            <p class="text-xs text-gray-400 font-medium truncate max-w-xs">${msg.content}</p>
                        </td>
                        <td class="px-6 py-4"><span class="${statusBadge} uppercase">${msg.status}</span></td>
                        <td class="px-6 py-4 text-xs font-semibold text-gray-400">${new Date(msg.created_at).toLocaleString()}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="AdminApp.executeAction('flag_message', '${msg.id}')" class="p-2 hover:bg-orange-500/10 hover:text-orange-400 rounded-xl transition-all text-gray-500"><i class="ph ph-flag text-lg"></i></button>
                                <button onclick="AdminApp.executeAction('delete_message', '${msg.id}')" class="p-2 hover:bg-red-500/10 hover:text-red-400 rounded-xl transition-all text-gray-500"><i class="ph ph-trash text-lg"></i></button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || '<tr><td colspan="5" class="p-8 text-center text-gray-500">No messages found</td></tr>';
        });
    },

    /**
     * Safety Center (Reports) Logic
     */
    initReports: function() {
        this.loadReportList();
    },

    loadReportList: function() {
        const tbody = document.getElementById('reports-table-body');
        if (!tbody) return;

        ApiClient.get('messages', 'list', { filter: 'flagged' }).then(data => {
            let html = '';
            data.messages.forEach(msg => {
                html += `
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=${msg.username}" class="w-10 h-10 rounded-xl bg-primary/10 p-0.5" alt="Avatar">
                                <div>
                                    <p class="font-bold text-sm text-primary">@${msg.username}</p>
                                    <p class="text-[11px] text-gray-500 font-medium">Flagged by community</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-black uppercase tracking-widest opacity-80">${msg.flag_reason || 'Unknown'}</td>
                        <td class="px-6 py-4 max-w-xs">
                            <p class="text-xs text-gray-400 italic truncate">"${msg.content}"</p>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="AdminApp.executeAction('toggle_block', '${msg.user_id}')" class="btn-primary !p-2 !rounded-xl !bg-red-600 hover:!bg-red-700 shadow-red-900/10" title="Suspend Account"><i class="ph ph-prohibit"></i></button>
                                <button onclick="AdminApp.executeAction('resolve_flag', '${msg.id}')" class="btn-secondary !p-2 !rounded-xl" title="Mark Resolved"><i class="ph ph-check"></i></button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || '<tr><td colspan="4" class="p-12 text-center text-gray-500">No active incidents found. The system is secure.</td></tr>';
        });
    },

    /**
     * Deletion Requests Logic
     */
    initDeletionRequests: function() {
        this.loadDeletionRequests();
    },

    loadDeletionRequests: function() {
        const tbody = document.getElementById('deletion-requests-table-body');
        if (!tbody) return;

        ApiClient.get('users', 'deletion_requests').then(data => {
            let html = '';
            data.requests.forEach(req => {
                const statusBadge = req.status === 'pending' ? 'badge-warning' : (req.status === 'approved' ? 'badge-success' : 'badge-danger');
                
                html += `
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-sm">${req.username}</p>
                            <p class="text-[11px] text-gray-500 font-medium">${req.email}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-gray-400 italic max-w-xs truncate">${req.reason || 'No reason provided'}</p>
                        </td>
                        <td class="px-6 py-4"><span class="${statusBadge} uppercase">${req.status}</span></td>
                        <td class="px-6 py-4 text-xs font-semibold text-gray-400">${new Date(req.created_at).toLocaleDateString()}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="AdminApp.handleDeletion('${req.id}', 'approved')" class="p-2 hover:bg-green-500/10 hover:text-green-500 rounded-xl transition-all text-gray-500" title="Approve"><i class="ph ph-check-circle text-lg"></i></button>
                                <button onclick="AdminApp.handleDeletion('${req.id}', 'rejected')" class="p-2 hover:bg-red-500/10 hover:text-red-400 rounded-xl transition-all text-gray-500" title="Reject"><i class="ph ph-prohibit text-lg"></i></button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || '<tr><td colspan="5" class="p-8 text-center text-gray-500">No pending requests</td></tr>';
            
            if (document.getElementById('pending-deletion-count')) {
                document.getElementById('pending-deletion-count').innerText = data.stats.pending;
            }
        });
    },

    handleDeletion: function(id, status) {
        if (!confirm(`Are you sure you want to ${status} this deletion request?`)) return;

        ApiClient.post('users', 'process_deletion', { id, status }).then(res => {
            toast.success('Governance', res.message);
            this.loadDeletionRequests();
        }).catch(err => {
            toast.error('Error', err.error || 'Failed to process request.');
        });
    },

    /**
     * Logs Logic
     */
    initLogs: function() {
        this.loadLogList();
    },

    loadLogList: function() {
        const tbody = document.getElementById('logs-table-body');
        if (!tbody) return;

        ApiClient.get('logs', 'list').then(data => {
            let html = '';
            data.logs.forEach(log => {
                const levelBadge = log.level === 'error' ? 'badge-danger' : (log.level === 'warning' ? 'badge-warning' : 'badge-neutral');
                
                html += `
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4 text-xs font-mono text-gray-500">#${log.id}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="${levelBadge} uppercase !px-1.5 !text-[9px]">${log.level}</span>
                                <span class="text-sm font-bold">${log.action}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-400 font-medium">${log.username || 'SYSTEM'}</td>
                        <td class="px-6 py-4 text-[11px] font-mono text-gray-500">${log.ip || '0.0.0.0'}</td>
                        <td class="px-6 py-4 text-xs font-semibold text-gray-400">${new Date(log.created_at).toLocaleString()}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || '<tr><td colspan="5" class="p-8 text-center text-gray-500">No logs found</td></tr>';
        });
    },

    /**
     * Ads Manager Logic
     */
    initAdsManager: function() {
        this.loadAdList();

        const form = document.getElementById('create-ad-form');
        if (form) {
            form.onsubmit = (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                const payload = Object.fromEntries(formData.entries());
                const isEdit = payload.id && payload.id !== '';

                const endpoint = isEdit ? 'update' : 'create';

                ApiClient.post('ads', endpoint, payload).then(res => {
                    toast.success('Marketing', res.message);
                    closeModal();
                    form.reset();
                    this.loadAdList();
                }).catch(err => {
                    toast.error('Error', err.error || 'Failed to process campaign.');
                });
            };
        }
    },

    loadAdList: function() {
        const tbody = document.getElementById('ads-table-body');
        if (!tbody) return;

        ApiClient.get('ads', 'list').then(data => {
            let html = '';
            data.ads.forEach(ad => {
                html += `
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-sm">${ad.name}</p>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">${ad.type} ${ad.ad_code ? '• CODE SET' : ''}</p>
                        </td>
                        <td class="px-6 py-4"><span class="badge-success">${ad.status}</span></td>
                        <td class="px-6 py-4 text-sm font-bold">$${parseFloat(ad.budget).toLocaleString()}</td>
                        <td class="px-6 py-4 text-center text-xs font-bold text-gray-400">${((ad.clicks/ad.impressions)*100 || 0).toFixed(2)}%</td>
                        <td class="px-6 py-4 text-right">
                             <div class="flex justify-end gap-2">
                                <button onclick="AdminApp.openDrawer('ad', '${ad.id}')" class="p-2 hover:bg-primary/10 hover:text-primary rounded-xl transition-all text-gray-500"><i class="ph ph-note-pencil text-lg"></i></button>
                                <button onclick="AdminApp.deleteAd('${ad.id}')" class="p-2 hover:bg-red-500/10 hover:text-red-400 rounded-xl transition-all text-gray-500"><i class="ph ph-trash text-lg"></i></button>
                             </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || '<tr><td colspan="5" class="p-8 text-center text-gray-500">No campaigns found</td></tr>';
            
            if (document.getElementById('ads-count-badge')) {
                document.getElementById('ads-count-badge').innerText = `${data.ads.length} Total`;
            }
        });
    },

    deleteAd: function(id) {
        if (!confirm('Permanently delete this campaign?')) return;
        ApiClient.post('ads', 'delete', { id }).then(res => {
            toast.success('Confirmed', 'Campaign removed.');
            this.loadAdList();
        });
    },

    /**
     * Settings Logic
     */
    toggleSetting: function(key, isChecked) {
        const value = isChecked ? 'on' : 'off';
        
        ApiClient.post('settings', 'update', { key: key, value: value }).then(res => {
            toast.success('Updated', `Setting '${key}' is now ${value}.`);
        }).catch(err => {
            toast.error('System Error', err.error || 'Could not update setting.');
        });
    },

    saveSettings: function() {
        const sessionTimeout = document.getElementById('session_timeout').value;
        const authRetryLimit = document.getElementById('auth_retry_limit').value;

        const p1 = ApiClient.post('settings', 'update', { key: 'session_timeout', value: sessionTimeout });
        const p2 = ApiClient.post('settings', 'update', { key: 'auth_retry_limit', value: authRetryLimit });

        Promise.all([p1, p2]).then(() => {
            toast.success('Success', 'Advanced configuration saved.');
        }).catch(err => {
            toast.error('Error', 'Failed to save some settings.');
        });
    },

    /**
     * Global Modals & Drawers
     */
    openModal: function(id) {
        const container = document.getElementById(id);
        if (container) container.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    },

    openDrawer: function(type, id) {
        const drawer = document.getElementById('side-drawer');
        const target = document.getElementById('drawer-content-target');
        if (!drawer || !target) return;

        // Show loading state immediately to fix "empty UI" bug
        target.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full space-y-4 opacity-50">
                <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-primary">Fetching Intelligence...</p>
            </div>
        `;
        drawer.classList.remove('translate-x-full');

        if (type === 'user') {
            ApiClient.get('users', 'details', { id }).then(data => {
                const user = data.user;
                target.innerHTML = `
                    <div class="p-8 h-full flex flex-col">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-xl font-bold uppercase tracking-widest text-primary">Identity Profile</h3>
                            <button onclick="closeDrawer()" class="p-2 hover:bg-white/10 rounded-xl transition-all">
                                <i class="ph ph-x text-2xl"></i>
                            </button>
                        </div>
                        <form id="profile-edit-form" class="space-y-6 flex-grow overflow-y-auto pr-2 custom-scrollbar">
                            <input type="hidden" name="id" value="${user.id}">
                            <div class="text-center p-6 bg-white/5 rounded-[2.5rem] border border-white/5 mb-6">
                                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=${user.username}" class="w-24 h-24 rounded-3xl mx-auto mb-4 border-2 border-primary/20">
                                <h4 class="text-2xl font-bold">${user.username}</h4>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">First Name</label>
                                        <input type="text" name="firstname" value="${user.firstname || ''}" class="w-full bg-white/5 border border-white/5 rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Last Name</label>
                                        <input type="text" name="lastname" value="${user.lastname || ''}" class="w-full bg-white/5 border border-white/5 rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Private E-mail</label>
                                    <input type="email" name="email" value="${user.email}" class="w-full bg-white/5 border border-white/5 rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Professional Bio</label>
                                    <textarea name="bio" rows="4" class="w-full bg-white/5 border border-white/5 rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all resize-none">${user.bio || ''}</textarea>
                                </div>
                            </div>
                        </form>
                        <div class="pt-6 border-t border-white/5 space-y-3">
                            <button onclick="AdminApp.submitProfileEdit()" class="w-full btn-primary !justify-center py-4">Save Identity Changes</button>
                            ${user.id != window.adminId ? `<button class="w-full btn-secondary !justify-center py-4 text-red-500 border-red-500/20 hover:bg-red-500/10" onclick="AdminApp.executeAction('toggle_block', '${user.id}')">${user.blocked ? 'Unblock User' : 'Suspend Account'}</button>` : ''}
                        </div>
                    </div>
                `;
            });
        } else if (type === 'ad') {
            // Fetch ad details (Assume ApiClient.get('ads', 'details', { id }) works or similar)
            ApiClient.get('ads', 'list').then(data => {
                const ad = data.ads.find(a => a.id == id) || {};
                target.innerHTML = `
                    <div class="p-8 h-full flex flex-col">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-xl font-bold uppercase tracking-widest text-primary">Creative Console</h3>
                            <button onclick="closeDrawer()" class="p-2 hover:bg-white/10 rounded-xl transition-all">
                                <i class="ph ph-x text-2xl"></i>
                            </button>
                        </div>
                        <form id="ad-edit-form" class="space-y-6 flex-grow overflow-y-auto pr-2 custom-scrollbar">
                            <input type="hidden" name="id" value="${ad.id}">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Campaign Reference</label>
                                <input type="text" name="name" value="${ad.name}" class="w-full bg-transparent border rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all" style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Daily Limit ($)</label>
                                    <input type="number" step="0.01" name="budget" value="${ad.budget}" class="w-full bg-transparent border rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all" style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Strategy</label>
                                    <select name="type" class="w-full bg-transparent border rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all" style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">
                                        <option value="Social" ${ad.type === 'Social' ? 'selected' : ''}>Social</option>
                                        <option value="Search" ${ad.type === 'Search' ? 'selected' : ''}>Search</option>
                                        <option value="Display" ${ad.type === 'Display' ? 'selected' : ''}>Display</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Injection Logic (Ads Code)</label>
                                <textarea name="ad_code" rows="10" class="w-full bg-transparent border rounded-2xl p-4 text-xs font-mono focus:border-primary outline-none transition-all resize-none" style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);" placeholder="<!-- Paste raw ad script here -->">${ad.ad_code || ''}</textarea>
                            </div>
                        </form>
                        <div class="pt-6 border-t border-white/5">
                            <button onclick="AdminApp.submitAdEdit()" class="w-full btn-primary !justify-center py-4">Apply Media Updates</button>
                        </div>
                    </div>
                `;
            });
        }
    },

    submitProfileEdit: function() {
        const form = document.getElementById('profile-edit-form');
        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        ApiClient.post('users', 'update_profile', payload).then(res => {
            toast.success('Identity', res.message);
            closeDrawer();
            this.loadUserList();
        }).catch(err => {
            toast.error('Error', err.error || 'Failed to update profile.');
        });
    },

    submitAdEdit: function() {
        const form = document.getElementById('ad-edit-form');
        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        ApiClient.post('ads', 'update', payload).then(res => {
            toast.success('Marketing', res.message);
            closeDrawer();
            this.loadAdList();
        }).catch(err => {
            toast.error('Error', err.error || 'Failed to update campaign.');
        });
    },

    executeAction: function(action, id) {
        toast.info('Processing', `Executing ${action}...`);
        
        let ns = 'users', method = 'status', payload = { id: id, action: action };
        
        if (action === 'flag_message' || action === 'delete_message' || action === 'resolve_flag') {
            ns = 'messages';
            method = 'flag';
            payload.action = action.replace('_message', '').replace('_flag', '');
        }

        ApiClient.post(ns, method, payload).then(res => {
            toast.success('Confirmed', res.message);
            if (ns === 'users') this.loadUserList();
            if (ns === 'messages') this.loadMessageList();
            if (window.closeDrawer) closeDrawer();
        }).catch(err => {
            toast.error('Failed', err.error || 'Action could not be completed.');
        });
    }
};

document.addEventListener('DOMContentLoaded', () => AdminApp.init());

/** Global Helpers **/
window.closeModal = function() {
    document.querySelectorAll('.modal-overlay').forEach(m => m.classList.add('hidden'));
    document.body.style.overflow = 'auto';
};

window.closeDrawer = function() {
    const drawer = document.getElementById('side-drawer');
    if (drawer) drawer.classList.add('translate-x-full');
};

window.openModal = function(id) {
    AdminApp.openModal(id);
};
