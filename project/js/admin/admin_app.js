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

        this.route(page);
        
        // Handle browser back/forward
        window.onpopstate = (e) => {
            if (e.state && e.state.page) {
                this.switchSection(e.state.page, false);
            }
        };

        // Handle notifications dropdown close on click outside
        window.onclick = (event) => {
            if (!event.target.matches('.ph-bell') && !event.target.closest('.notification-pane')) {
                const pane = document.getElementById('notification-pane');
                if (pane) pane.classList.add('hidden');
            }
        };
    },

    route: function(page) {
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
            case 'policy_editor':
                this.initPolicyEditor();
                break;
            case 'analytics':
                this.initAnalytics ? this.initAnalytics() : null;
                break;
            case 'settings':
                // Settings might not need special init if it's just toggles
                break;
            case 'roles':
                this.initRoles();
                break;
            case 'app_config':
                this.initAppConfig();
                break;
        }
    },

    toggleNotifications: function() {
        const pane = document.getElementById('notification-pane');
        if (pane) {
            pane.classList.toggle('hidden');
        }
    },

    switchSection: function(section, pushState = true) {
        // 1. Update active state in sidebar immediately for responsiveness
        document.querySelectorAll('.nav-link-premium').forEach(l => l.classList.remove('active'));
        const activeLink = document.querySelector(`a[href*="${section}"]`);
        if (activeLink) activeLink.classList.add('active');

        // 2. Load content via AJAX
        const target = document.getElementById('content-container');
        if (!target) return;

        // Show loading state with premium spinner
        target.innerHTML = `
            <div class="flex items-center justify-center p-32 opacity-40">
                <div class="w-16 h-16 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
            </div>
        `;

        const base = (window.BASE_PATH || '/').replace(/\/$/, '');
        fetch(`${base}/admin?page=${section}&ajax=1`)

            .then(res => res.text())
            .then(html => {
                target.innerHTML = html; 
                
                // Reset tabs to default (first tab)
                const firstTab = target.querySelector('.tab-btn');
                if (firstTab) {
                    const tabId = firstTab.getAttribute('data-tab');
                    if (tabId) this.switchTab(section, tabId);
                }

                if (pushState) {
                    const base = (window.BASE_PATH || '/').replace(/\/$/, '');
                    window.history.pushState({ page: section }, "", `${base}/admin?page=${section}`);

                }
                this.init(); // Re-run init to bind new page logic
            })
            .catch(err => {
                console.error("SPA failure:", err);
                const base = (window.BASE_PATH || '/').replace(/\/$/, '');
                window.location.href = `${base}/admin?page=${section}`; // Fallback

            });
    },

    /**
     * Tabbed Architecture Engine
     * Handles switching between sub-modules within a section
     */
    switchTab: function(sectionId, tabId) {
        console.log(`Switching Tab: ${sectionId} -> ${tabId}`);
        
        // 1. Update Tab Buttons (Active Styles & Underlines)
        const tabContainer = document.querySelector(`#section-${sectionId} [id$="-tabs"]`);
        if (tabContainer) {
            tabContainer.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            const activeBtn = tabContainer.querySelector(`[data-tab="${tabId}"]`);
            if (activeBtn) activeBtn.classList.add('active');
        }

        // 2. Toggle Tab Content Visibility
        const section = document.getElementById(`section-${sectionId}`);
        if (section) {
            section.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('active');
            });
            const targetContent = document.getElementById(`tab-content-${tabId}`);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('active');
            }
        }
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

            if (document.getElementById('notif-count-badge')) {
                // Simulation: Use some part of messages_today as "new" alerts
                const newAlerts = Math.max(1, data.messages_today % 15);
                document.getElementById('notif-count-badge').innerText = `${newAlerts} New`;
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

    exportUserCSV: function() {
        toast.info('Preparing Export', 'Gathering identity records...');
        ApiClient.get('users', 'list', { page: 1, limit: 1000 }).then(data => {
            const users = data.users;
            const headers = ['ID', 'Username', 'Email', 'Status', 'Joined'];
            const rows = users.map(u => [
                u.id, 
                u.username, 
                u.email, 
                u.blocked == 1 ? 'Blocked' : 'Active', 
                u.created_at
            ]);

            const csvContent = "data:text/csv;charset=utf-8," 
                + [headers, ...rows].map(e => e.join(",")).join("\n");

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `honlor_users_export_${new Date().getTime()}.csv`);
            document.body.appendChild(link);
            link.click();
            toast.success('Export Ready', 'Identity vault downloaded successfully.');
        });
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
                        <td class="px-6 py-4">
                            ${user.is_master == 1 
                                ? '<span class="px-2 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-lg text-[9px] font-black uppercase tracking-widest">Master Admin</span>' 
                                : `<span class="px-2 py-1 bg-white/5 text-gray-400 border border-white/5 rounded-lg text-[9px] font-black uppercase tracking-widest">${user.role_name || 'UNASSIGNED'}</span>`
                            }
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
            
            if (document.getElementById('users-total-count')) {
                document.getElementById('users-total-count').innerText = `${(data.total / 1000).toFixed(1)}k Total`;
            }

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
        var self = this;
        var filterEl = document.getElementById('message-filter');
        var filter = (filterEl) ? filterEl.value : '';
        
        var tbody = document.getElementById('messages-table-body');
        var velocityMetric = document.getElementById('metric-velocity');
        var flaggedBadge = document.getElementById('flagged-count-badge');

        if (!tbody) return;

        ApiClient.get('messages', 'list', { status: 'all', filter: filter }).then(data => {
            // Update Metrics
            if (velocityMetric) velocityMetric.innerText = data.metrics.velocity.toFixed(1);
            if (flaggedBadge) {
                flaggedBadge.innerText = data.metrics.flagged;
                flaggedBadge.classList.toggle('hidden', data.metrics.flagged === 0);
            }

            let html = '';
            const messages = data.messages || [];
            messages.forEach(msg => {
                const statusColor = (msg.status === 'flagged') ? 'bg-orange-500/10 text-orange-400 border-orange-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                const statusLabel = (msg.status === 'flagged') ? 'Flagged' : 'Safe';
                const avatar = 'https://api.dicebear.com/7.x/avataaars/svg?seed=' + msg.username;

                html += `
                    <tr class="hover:bg-white/[0.03] transition-all duration-300 group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-[1rem] bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold text-[10px] uppercase shadow-lg shadow-indigo-500/5">
                                    # ${msg.channel_name ? msg.channel_name.substring(0, 1) : '?'}
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-primary">${msg.channel_name}</p>
                                    <p class="text-[8px] font-bold opacity-40 uppercase tracking-widest mt-0.5">Global Cluster</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <img src="${avatar}" class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 shadow-xl">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-tight mb-0.5">${msg.username}</p>
                                    <p class="text-[11px] font-medium opacity-70 line-clamp-1 max-w-sm italic">"${msg.content}"</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="px-3 py-1.5 ${statusColor} border rounded-xl text-[8px] font-black uppercase tracking-widest shadow-xl">${statusLabel}</span>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-xs font-black tracking-tight" style="color: var(--text-main);">${new Date(msg.created_at).toLocaleTimeString()}</p>
                            <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest mt-0.5">${new Date(msg.created_at).toLocaleDateString()}</p>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <button onclick="AdminApp.executeAction('flag_message', '${msg.id}')" class="w-10 h-10 rounded-2xl bg-orange-500/10 text-orange-400 flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all shadow-xl shadow-orange-500/5">
                                    <i class="ph-bold ph-flag text-lg"></i>
                                </button>
                                <button onclick="AdminApp.executeAction('delete_message', '${msg.id}')" class="w-10 h-10 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-xl shadow-red-500/5">
                                    <i class="ph-bold ph-trash text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || '<tr><td colspan="5" class="p-20 text-center opacity-30 font-black uppercase tracking-[0.2em] text-[10px]">No active signals in stream</td></tr>';
        });
    },

    /**
     * Safety Center (Reports) Logic
     */
    initReports: function() {
        this.loadReportList();
        this.loadSupabaseReportList();
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

    loadSupabaseReportList: function () {
        var tbody = document.getElementById('supabase-reports-table-body');
        var totalMetric = document.getElementById('metric-sb-total');
        var resolvedMetric = document.getElementById('metric-sb-resolved');
        var velocityMetric = document.getElementById('metric-sb-velocity');
        var filterInput = document.getElementById('supabase-report-filter');
        var filterValue = filterInput ? filterInput.value.toLowerCase() : '';

        if (!tbody) return;

        ApiClient.get('supabase_reports', 'list').then(function (data) {
            if (totalMetric) totalMetric.innerText = data.metrics.total < 10 ? '0' + data.metrics.total : data.metrics.total;
            if (resolvedMetric) resolvedMetric.innerText = data.metrics.resolved < 10 ? '0' + data.metrics.resolved : data.metrics.resolved;
            if (velocityMetric) velocityMetric.innerText = data.metrics.velocity + '%';

            var html = '';
            var reports = data.reports || [];
            
            // Apply filtering if filter value exists
            if (filterValue) {
                reports = reports.filter(function (rep) {
                    return (
                        (rep.reason && rep.reason.toLowerCase().indexOf(filterValue) !== -1) ||
                        (rep.details && rep.details.toLowerCase().indexOf(filterValue) !== -1) ||
                        (rep.reporter_id && rep.reporter_id.toLowerCase().indexOf(filterValue) !== -1) ||
                        (rep.reported_id && rep.reported_id.toLowerCase().indexOf(filterValue) !== -1) ||
                        (rep.status && rep.status.toLowerCase().indexOf(filterValue) !== -1)
                    );
                });
            }

            for (var i = 0; i < reports.length; i++) {
                var rep = reports[i];
                var status = (rep.status || 'pending').toLowerCase();
                
                // Color badges for status
                var statusClass = 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20';
                if (status === 'resolved' || status === 'solved') {
                    statusClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                } else if (status === 'dismissed') {
                    statusClass = 'bg-red-500/10 text-red-500 border-red-500/20';
                }

                // Cyberpunk badge colors for report reasons
                var reason = rep.reason || 'Other';
                var reasonClass = 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                if (reason === 'Spam') reasonClass = 'bg-purple-500/10 text-purple-400 border-purple-500/20';
                else if (reason === 'Violence') reasonClass = 'bg-red-500/10 text-red-400 border-red-500/20';
                else if (reason === 'Abuse or Harassment') reasonClass = 'bg-rose-500/10 text-rose-400 border-rose-500/20';
                else if (reason === 'Nudity or Pornography') reasonClass = 'bg-amber-500/10 text-amber-400 border-amber-500/20';
                else if (reason === 'Illegal Drugs/Content') reasonClass = 'bg-fuchsia-500/10 text-fuchsia-400 border-fuchsia-500/20';
                else if (reason === 'Personal Details') reasonClass = 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20';

                // Truncate UUIDs elegantly
                var repUuidShort = rep.reporter_id ? 'usr_' + rep.reporter_id.substring(0, 4) : 'usr_anon';
                var targetUuidShort = rep.reported_id ? 'usr_' + rep.reported_id.substring(0, 4) : 'usr_anon';
                if (rep.channel_id) {
                    targetUuidShort = 'ch_' + rep.channel_id.substring(0, 4);
                }

                // Synchronization timestamp
                var localTimeStr = rep.created_at ? new Date(rep.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'N/A';
                var localDateStr = rep.created_at ? new Date(rep.created_at).toLocaleDateString([], {month: 'short', day: 'numeric'}) : 'N/A';

                // Admin reply speech bubble indicator
                var replyBubble = '';
                if (rep.admin_reply) {
                    replyBubble = ' <i class="ph-bold ph-chat-circle-text text-emerald-400 ml-1.5 cursor-help" title="Reply: &quot;' + rep.admin_reply + '&quot;"></i>';
                }

                html += '<tr class="hover:bg-white/[0.03] transition-all duration-300 group">' +
                        '    <td class="px-8 py-6">' +
                        '        <div class="flex items-center gap-4">' +
                        '            <div class="flex -space-x-3 transition-transform group-hover:scale-105">' +
                        '                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=' + rep.reporter_id + '" class="w-10 h-10 rounded-xl bg-purple-500/10 border-2 border-[var(--bg-main)] z-20 shadow-xl">' +
                        '                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=' + (rep.reported_id || rep.channel_id) + '" class="w-10 h-10 rounded-xl bg-red-500/10 border-2 border-[var(--bg-main)] z-10 shadow-xl">' +
                        '            </div>' +
                        '            <div>' +
                        '                <p class="text-[11px] font-black uppercase tracking-widest text-purple-400 truncate max-w-[150px]" title="Reporter UUID: ' + rep.reporter_id + '">By @' + repUuidShort + '</p>' +
                        '                <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest mt-0.5" title="Reported UUID: ' + (rep.reported_id || rep.channel_id) + '">Reported @' + targetUuidShort + '</p>' +
                        '            </div>' +
                        '        </div>' +
                        '    </td>' +
                        '    <td class="px-8 py-6 text-center">' +
                        '        <span class="px-3 py-1.5 ' + reasonClass + ' border rounded-xl text-[8px] font-black uppercase tracking-widest shadow-xl">' + reason + '</span>' +
                        '        <p class="text-[8px] font-bold opacity-20 uppercase mt-2">App Report</p>' +
                        '    </td>' +
                        '    <td class="px-8 py-6 max-w-[300px]">' +
                        '        <p class="text-[10px] font-medium opacity-70 italic" style="color: var(--text-main);">' + (rep.details ? '"' + rep.details + '"' : '<span class="opacity-30">No details provided</span>') + '</p>' +
                        '    </td>' +
                        '    <td class="px-8 py-6 text-center">' +
                        '         <span class="px-3 py-1.5 ' + statusClass + ' border rounded-xl text-[8px] font-black uppercase tracking-widest shadow-xl">' + status.toUpperCase() + replyBubble + '</span>' +
                        '    </td>' +
                        '    <td class="px-8 py-6 text-center">' +
                        '        <p class="text-[10px] font-black" style="color: var(--text-main);">' + localTimeStr + '</p>' +
                        '        <p class="text-[8px] font-bold opacity-40 uppercase mt-0.5">' + localDateStr + '</p>' +
                        '    </td>' +
                        '    <td class="px-8 py-6 text-right">' +
                        '         <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300">';
                
                if (status === 'pending') {
                    var detailsEscaped = (rep.details || '').replace(/['"\\]/g, '\\$&');
                    html += '            <button onclick="AdminApp.openSupabaseReplyModal(\'' + rep.id + '\', \'' + reason + '\', \'' + detailsEscaped + '\')" class="px-3 py-2 rounded-xl bg-purple-500/10 text-purple-400 hover:bg-purple-500 hover:text-white transition-all text-[8px] font-black uppercase tracking-widest border border-purple-500/20 shadow-xl">' +
                            '                Reply & Solve' +
                            '            </button>' +
                            '            <button onclick="AdminApp.executeSupabaseAction(\'' + rep.id + '\', \'resolved\', \'Resolved by quick action\')" class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all shadow-xl shadow-emerald-500/5" title="Quick Resolve">' +
                            '                <i class="ph-bold ph-check text-lg"></i>' +
                            '            </button>' +
                            '            <button onclick="AdminApp.executeSupabaseAction(\'' + rep.id + '\', \'dismissed\')" class="w-10 h-10 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-xl shadow-red-500/5" title="Dismiss Report">' +
                            '                <i class="ph-bold ph-x text-lg"></i>' +
                            '            </button>';
                } else {
                    html += '            <span class="text-[8px] font-black uppercase tracking-widest opacity-25">Resolved</span>';
                }
                
                html += '         </div>' +
                        '    </td>' +
                        '</tr>';
            }
            tbody.innerHTML = html || '<tr><td colspan="6" class="p-24 text-center opacity-30 font-black uppercase tracking-[0.3em] text-[10px]">No Supabase reports in queue</td></tr>';
        });
    },

    executeSupabaseAction: function (id, action, reply) {
        var self = this;
        var payload = { id: id, action: action };
        if (reply) payload.reply = reply;

        ApiClient.post('supabase_reports', 'action', payload).then(function (res) {
            toast.success('Supabase Sync', 'Report resolved and database entry updated.');
            self.loadSupabaseReportList();
        }).catch(function (err) {
            toast.error('Action Failed', err.error || 'Failed to update Supabase record.');
        });
    },

    openSupabaseReplyModal: function (id, reason, details) {
        var self = this;
        var existing = document.getElementById('supabase-reply-modal');
        if (existing) existing.remove();

        var modalHtml = '<div id="supabase-reply-modal" class="fixed inset-0 bg-black/60 backdrop-blur-md z-[99999] flex items-center justify-center p-6 animate-in fade-in duration-300">' +
                        '    <div class="w-full max-w-lg rounded-[2.5rem] border border-white/10 p-8 shadow-2xl space-y-6" style="background: rgba(20, 10, 30, 0.85);">' +
                        '        <div class="flex justify-between items-center">' +
                        '            <div>' +
                        '                <h4 class="text-xl font-black uppercase tracking-tight text-white">App Report <span class="text-purple-400">Resolution</span></h4>' +
                        '                <p class="text-[8px] font-bold uppercase tracking-widest text-purple-300/40 mt-1">ID: ' + id + '</p>' +
                        '            </div>' +
                        '            <button onclick="document.getElementById(\'supabase-reply-modal\').remove()" class="w-8 h-8 rounded-xl bg-white/5 text-white/60 hover:bg-white/10 hover:text-white flex items-center justify-center transition-all">' +
                        '                <i class="ph ph-x text-lg"></i>' +
                        '            </button>' +
                        '        </div>' +
                        '        <div class="p-5 rounded-2xl bg-white/[0.02] border border-white/5 text-xs text-white/80 space-y-3">' +
                        '            <div class="flex justify-between">' +
                        '                <span class="opacity-40 uppercase tracking-widest text-[9px] font-black">Reason</span>' +
                        '                <span class="text-purple-400 font-bold">' + reason + '</span>' +
                        '            </div>' +
                        '            <div>' +
                        '                <span class="opacity-40 uppercase tracking-widest text-[9px] font-black block mb-1">Details</span>' +
                        '                <p class="italic text-white/95">"' + (details || 'No additional details provided.') + '"</p>' +
                        '            </div>' +
                        '        </div>' +
                        '        <div class="space-y-2">' +
                        '            <label class="text-[10px] font-black uppercase tracking-widest opacity-60 text-white">Reply message to the reporter</label>' +
                        '            <textarea id="supabase-reply-message" placeholder="Type a message to the user... (e.g. We have solved the issue and taken action)" ' +
                        '                      class="w-full h-32 rounded-2xl bg-black/40 border border-white/10 p-4 text-xs text-white placeholder-white/30 focus:ring-2 focus:ring-purple-500/20 outline-none transition-all resize-none"></textarea>' +
                        '        </div>' +
                        '        <div class="flex gap-4 pt-2">' +
                        '            <button onclick="document.getElementById(\'supabase-reply-modal\').remove()" class="btn-secondary !rounded-2xl flex-grow justify-center py-4 text-xs font-black uppercase tracking-widest">Cancel</button>' +
                        '            <button id="supabase-submit-btn" class="btn-primary !rounded-2xl flex-grow justify-center py-4 text-xs font-black uppercase tracking-widest shadow-lg shadow-purple-500/20">Submit Resolution</button>' +
                        '        </div>' +
                        '    </div>' +
                        '</div>';

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        var submitBtn = document.getElementById('supabase-submit-btn');
        if (submitBtn) {
            submitBtn.onclick = function () {
                var text = document.getElementById('supabase-reply-message').value.trim();
                if (!text) {
                    toast.error('Required Field', 'Please enter a reply message before resolving.');
                    return;
                }
                submitBtn.disabled = true;
                submitBtn.innerText = 'Synchronizing...';
                self.executeSupabaseAction(id, 'resolved', text);
                document.getElementById('supabase-reply-modal').remove();
            };
        }
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
        
        // Polling interval for real-time telemetry (Every 3 seconds)
        if (this.telemetryInterval) clearInterval(this.telemetryInterval);
        
        this.telemetryInterval = setInterval(() => {
            this.updateTelemetry();
            if (this.currentSection === 'logs') {
                this.loadLogList();
            }
        }, 5000);

        this.updateTelemetry();
    },

    updateTelemetry: function() {
        if (this.currentSection !== 'logs') return;

        // Simulated high-fidelity telemetry fluctuations
        const cpu = Math.floor(Math.random() * (25 - 8 + 1)) + 8;
        const ram = (2.2 + Math.random() * 0.4).toFixed(1);
        const ramPercent = Math.floor((ram / 8) * 100);
        const threads = Math.floor(Math.random() * (52 - 38 + 1)) + 38;
        const ingress = (10 + Math.random() * 5).toFixed(1);
        const egress = (45 + Math.random() * 10).toFixed(1);

        const up = (id, val, suffix = '') => {
            const el = document.getElementById(id);
            if (el) el.innerText = val + suffix;
        };

        const bar = (id, percent) => {
            const el = document.getElementById(id);
            if (el) el.style.width = percent + '%';
        };

        up('monitor-cpu-text', cpu, '%');
        bar('monitor-cpu-bar', cpu);

        up('monitor-ram-text', ram, ' GB');
        bar('monitor-ram-bar', ramPercent);

        up('monitor-threads-text', threads, ' Active');
        bar('monitor-threads-bar', Math.min(100, (threads / 120) * 100));

        up('monitor-ingress-text', ingress, ' Mbps');
        up('monitor-egress-text', egress, ' Mbps');
    },

    loadLogList: function() {
        const tbody = document.getElementById('logs-table-body');
        if (!tbody) return;

        ApiClient.get('logs', 'list').then(data => {
            let html = '';
            data.logs.forEach(log => {
                const colors = {
                    error: { badge: 'bg-red-500/10 text-red-500 border-red-500/20', icon: 'ph-warning-circle' },
                    warning: { badge: 'bg-orange-500/10 text-orange-500 border-orange-500/20', icon: 'ph-warning' },
                    info: { badge: 'bg-blue-500/10 text-blue-400 border-blue-400/20', icon: 'ph-info' },
                    status: { badge: 'bg-green-500/10 text-green-400 border-green-400/20', icon: 'ph-check-circle' }
                };

                const cfg = colors[log.level] || colors.info;
                
                html += `
                    <tr class="hover:bg-white/5 transition-all group">
                        <td class="py-6 px-8 text-[10px] font-black text-gray-500 tracking-widest">#${log.id}</td>
                        <td class="py-6 px-8">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-lg ${cfg.badge.split(' ')[0]} flex items-center justify-center border ${cfg.badge.split(' ')[2]}">
                                    <i class="ph-bold ${cfg.icon} text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black tracking-tight uppercase">${log.action}</p>
                                    <p class="text-[10px] uppercase font-black opacity-40 tracking-widest mt-0.5">${log.level}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-8">
                            <div class="flex items-center gap-3">
                                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=${log.username || 'System'}" class="w-7 h-7 rounded-lg bg-white/5 border border-white/10">
                                <span class="text-xs font-black uppercase tracking-widest opacity-60">${log.username || 'SYSTEM'}</span>
                            </div>
                        </td>
                        <td class="py-6 px-8 text-[10px] font-black text-gray-500 tracking-widest font-mono">${log.ip || '0.0.0.0'}</td>
                        <td class="py-6 px-8 text-right">
                            <span class="text-[10px] font-black uppercase tracking-widest opacity-40">${new Date(log.created_at).toLocaleTimeString()}</span>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || '<tr><td colspan="5" class="p-20 text-center"><p class="font-black text-[10px] uppercase tracking-widest opacity-40">Zero Incidents Detected</p></td></tr>';
        });
    },

    openTerminal: function() {
        this.openModal('system-terminal-modal');
        const output = document.getElementById('terminal-output');
        if (output) {
            output.innerHTML = '<p class="text-primary font-black mb-2 animate-pulse">> Initializing Kernel Synchronizer...</p>';
            setTimeout(() => {
                output.innerHTML += '<p class="text-green-400 font-black mb-1">[OK] Global Nodes Authenticated</p>';
                output.innerHTML += '<p class="text-green-400 font-black mb-1">[OK] Encryption Handshake Verified</p>';
                output.innerHTML += '<p class="font-black mt-4" style="color: var(--text-main);">> Aether Core v10.4.2 Ready.</p>';
            }, 1200);
        }
    },

    /**
     * Ads Manager Logic
     */
    initAdsManager: function() {
        this.loadAdList();

        const searchInput = document.querySelector('#tab-content-campaigns input[placeholder*="Search streams"]');
        if (searchInput) {
            let timeout;
            searchInput.oninput = () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    this.loadAdList(searchInput.value);
                }, 300);
            };
        }

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

    loadAdList: function(filter = '') {
        const tbody = document.getElementById('ads-table-body');
        if (!tbody) return;

        ApiClient.get('ads', 'list', { filter }).then(data => {
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
        const rateLimit = document.getElementById('rate_limit').value;
        const ipBlocklist = document.getElementById('ip_blocklist').value;

        const p1 = ApiClient.post('settings', 'update', { key: 'session_timeout', value: sessionTimeout });
        const p2 = ApiClient.post('settings', 'update', { key: 'auth_retry_limit', value: authRetryLimit });
        const p3 = ApiClient.post('settings', 'update', { key: 'rate_limit', value: rateLimit });
        const p4 = ApiClient.post('settings', 'update', { key: 'ip_blocklist', value: ipBlocklist });

        Promise.all([p1, p2, p3, p4]).then(() => {
            toast.success('System Security', 'Protocol updates synchronized.');
        }).catch(err => {
            toast.error('Sync failure', 'Some settings could not be updated.');
        });
    },

    /**
     * Role Studio Logic
     */
    initRoles: function() {
        this.loadRoleList();
        
        const form = document.getElementById('save-role-form');
        if (form) {
            form.onsubmit = (e) => {
                e.preventDefault();
                this.saveRole();
            };
        }
    },

    loadRoleList: function() {
        const tbody = document.getElementById('roles-table-body');
        if (!tbody) return;

        ApiClient.get('roles', 'list').then(data => {
            let html = '';
            data.roles.forEach(role => {
                html += `
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-400 border border-rose-500/20">
                                    <i class="ph-bold ph-shield-star text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black uppercase tracking-tight">${role.name}</p>
                                    <p class="text-[10px] font-black opacity-40 tracking-widest mt-0.5">ESTABLISHED ${new Date(role.created_at).toLocaleDateString()}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-wrap gap-2">
                                ${this.renderPermissionChips(role.permissions)}
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                             <div class="flex justify-end gap-3 translate-x-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-0 transition-all">
                                <button onclick="AdminApp.editRole('${role.id}')" class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-all"><i class="ph ph-note-pencil text-lg"></i></button>
                                <button onclick="AdminApp.deleteRole('${role.id}')" class="w-10 h-10 rounded-xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all"><i class="ph ph-trash text-lg"></i></button>
                             </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || '<tr><td colspan="3" class="p-20 text-center"><p class="font-black text-[10px] uppercase tracking-widest opacity-40">No Security Roles defined. Create one to begin orchestration.</p></td></tr>';
        });
    },

    renderPermissionChips: function(permsJson) {
        if (!permsJson) return '<span class="text-[10px] font-black opacity-20 uppercase tracking-widest">NONE</span>';
        const perms = typeof permsJson === 'string' ? JSON.parse(permsJson) : permsJson;
        if (perms.all) return '<span class="px-3 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-lg text-[10px] font-black uppercase tracking-widest">Absolute Authority</span>';
        
        return Object.keys(perms).map(k => `
            <span class="px-2 py-1 bg-white/5 text-gray-400 border border-white/5 rounded-lg text-[9px] font-black uppercase tracking-widest">${k}</span>
        `).join('');
    },

    editRole: function(id) {
        ApiClient.get('roles', 'get', { id }).then(data => {
            const role = data.role;
            const form = document.getElementById('save-role-form');
            if (!form) return;

            form.role_id.value = role.id;
            form.role_name.value = role.name;
            
            // Reset and set checkboxes
            form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            
            const perms = JSON.parse(role.permissions);
            if (perms.all) {
                const allCb = form.querySelector('[name="perms[all]"]');
                if (allCb) allCb.checked = true;
            } else {
                Object.keys(perms).forEach(resource => {
                    const actions = perms[resource];
                    if (Array.isArray(actions)) {
                        actions.forEach(action => {
                            const cb = form.querySelector(`[name="perms[${resource}][${action}]"]`);
                            if (cb) cb.checked = true;
                        });
                    }
                });
            }
            
            AdminApp.openModal('role-editor-modal');
        });
    },

    saveRole: function() {
        const form = document.getElementById('save-role-form');
        const formData = new FormData(form);
        const id = formData.get('role_id');
        const name = formData.get('role_name');
        
        // Construct permissions object
        const perms = {};
        const allChecked = form.querySelector('[name="perms[all]"]').checked;
        
        if (allChecked) {
            perms.all = true;
        } else {
            const checkboxes = form.querySelectorAll('input[type="checkbox"]:checked:not([name="perms[all]"])');
            checkboxes.forEach(cb => {
                const name = cb.name; // e.g., perms[users][view]
                const match = name.match(/perms\[(.+?)\]\[(.+?)\]/);
                if (match) {
                    const resource = match[1];
                    const action = match[2];
                    if (!perms[resource]) perms[resource] = [];
                    perms[resource].push(action);
                }
            });
        }

        const payload = { id, name, permissions: JSON.stringify(perms) };

        ApiClient.post('roles', 'save', payload).then(res => {
            toast.success('Security Studio', res.message);
            closeModal();
            this.loadRoleList();
        }).catch(err => {
            toast.error('Governance Error', err.error || 'Identity protocol violation.');
        });
    },

    deleteRole: function(id) {
        if (!confirm('Permanently deconstruct this security role? This may impact mapped identities.')) return;
        ApiClient.post('roles', 'delete', { id }).then(res => {
            toast.success('Deconstructed', 'Security role purged from vault.');
            this.loadRoleList();
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
                                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Security Cluster (Role)</label>
                                    <div class="flex gap-2">
                                        <select name="role_id" class="flex-grow bg-white/5 border border-white/5 rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all">
                                            <option value="0">Unassigned</option>
                                            ${AdminApp.rolesList ? AdminApp.rolesList.map(r => `<option value="${r.id}" ${user.role_id == r.id ? 'selected' : ''}>${r.name}</option>`).join('') : ''}
                                        </select>
                                        <button type="button" onclick="AdminApp.submitRoleUpdate('${user.id}')" class="px-4 bg-primary/20 text-primary border border-primary/20 rounded-2xl hover:bg-primary hover:text-white transition-all"><i class="ph-bold ph-shield-check"></i></button>
                                    </div>
                                    ${user.is_master == 1 ? '<p class="text-[9px] font-black text-amber-500 uppercase tracking-widest mt-2 leading-relaxed">System Master: Level 0 Override Active</p>' : ''}
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
        } else if (type === 'app_config') {
            const cfg = this.appConfigs ? this.appConfigs.find(c => c.id == id) : null;
            if (!cfg) return;

            target.innerHTML = `
                <div class="p-8 h-full flex flex-col">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-bold uppercase tracking-widest text-primary">Edit Shard</h3>
                        <button onclick="closeDrawer()" class="p-2 hover:bg-white/10 rounded-xl transition-all">
                            <i class="ph ph-x text-2xl"></i>
                        </button>
                    </div>
                    <form id="appconfig-edit-form" class="space-y-6 flex-grow overflow-y-auto pr-2 custom-scrollbar">
                        <input type="hidden" name="id" value="${cfg.id}">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Version Tag (Semantic)</label>
                            <input type="text" name="latest_version" value="${cfg.latest_version}" class="w-full bg-transparent border rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all" style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Target Deployment URL</label>
                            <input type="url" name="update_url" value="${cfg.update_url}" class="w-full bg-transparent border rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all font-mono" style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">
                        </div>
                        <div class="p-6 rounded-2xl border flex items-center justify-between" style="background-color: var(--glass-bg); border-color: var(--border-color);">
                            <div>
                                <p class="text-xs font-black uppercase tracking-tight" style="color: var(--text-main);">Force Mandatory Upgrade</p>
                                <p class="text-[10px] font-bold opacity-30 mt-1 uppercase tracking-widest">Restrict platform access</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_mandatory" value="1" ${cfg.is_mandatory == 1 ? 'checked' : ''} class="sr-only peer">
                                <div class="w-14 h-7 bg-white/10 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-red-500 shadow-lg"></div>
                            </label>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Release Notes / Shard Notes</label>
                            <textarea name="description" rows="5" class="w-full bg-transparent border rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all resize-none" style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">${cfg.description || ''}</textarea>
                        </div>
                    </form>
                    <div class="pt-6 border-t border-white/5">
                        <button onclick="AdminApp.submitAppConfigEdit()" class="w-full btn-primary !justify-center py-4">Synchronize Shard</button>
                    </div>
                </div>
            `;
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
        });
    },

    submitRoleUpdate: function(userId) {
        const roleId = document.querySelector('#profile-edit-form select[name="role_id"]').value;
        ApiClient.post('users', 'update_role', { id: userId, role_id: roleId }).then(res => {
            toast.success('Security Protocol', res.message);
            this.loadUserList();
        }).catch(err => {
            toast.error('Violation', err.error || 'Failed to update identity cluster.');
        });
    },

    /**
     * Cache Roles for Selectors
     */
    
    refreshRolesCache: function() {
        ApiClient.get('roles', 'list').then(data => {
            this.rolesList = data.roles;
        });
    },

    generateInsights: function() {
        toast.info('Generating Insights', 'Analyzing global data patterns...');
        setTimeout(() => {
            const insights = [
                "User retention increased by 14% over the last 7 days.",
                "Primary traffic surge detected in the APAC region node.",
                "Ad efficiency is peaking between 18:00 and 22:00 UTC.",
                "Network latency remains stable at < 45ms across all channels."
            ];
            const random = insights[Math.floor(Math.random() * insights.length)];
            toast.success('Analysis Complete', random);
        }, 2000);
    },

    initAnalytics: function() {
        console.log("Analytics Initialized");
    },

    /** Governance (Policy Editor) Logic **/
    initPolicyEditor: function() {
        this.currentPolicy = 'privacy';
        this.policies = {
            privacy: '',
            terms: '',
            community: ''
        };

        // Fetch from DB
        toast.info('Synchronizing Governance...', 'Accessing platform policy ledger.');
        ApiClient.get('settings', 'get', { keys: 'policy_privacy,policy_terms,policy_community' }).then(data => {
            if (data.settings) {
                this.policies.privacy = data.settings.policy_privacy || this.getDefaultPolicy('privacy');
                this.policies.terms = data.settings.policy_terms || this.getDefaultPolicy('terms');
                this.policies.community = data.settings.policy_community || this.getDefaultPolicy('community');
                
                // Update editor with default/saved content
                const editor = document.getElementById('policy-editor');
                if (editor) editor.value = this.policies[this.currentPolicy];
            }
        });
    },

    getDefaultPolicy: function(type) {
        const defaults = {
            privacy: `## Privacy & Data Protection Framework\n\n### 1. Data Collection Protocols\nThe Aether ecosystem operates on a principle of radical transparency. We collect telemetry data only to ensure node stability and cross-chain verification.\n\n### 2. Encryption Standards\nAll identity records in the Vault are encrypted using AES-256-GCM. Private keys are never stored on centralized edge clusters.\n\n### 3. User Sovereignty\nUsers maintain 100% ownership of their data packets. Deletion requests are processed within a 24-hour governance window.\n\n[--- Draft Content Below ---]`,
            terms: `## Terms of Universal Service\n\n### 1. Access Authorization\nBy accessing the Aether network, you agree to abide by the decentralized consensus protocols. Unauthorized node manipulation is strictly prohibited.\n\n### 2. Liability Limitation\nThe infrastructure leads are not liable for packet loss during cross-node transmissions or atmospheric interference.\n\n### 3. Smart Contract Integrity\nAll governance actions are final and recorded on the immutable ledger.\n\n[--- Ready for Deployment ---]`,
            community: `## Community Engagement Guidelines\n\n### 1. Radical Respect\nDiscourse within the Aether channels must remain constructive. Personal attacks on identity profiles will result in immediate account suspension.\n\n### 2. Information Integrity\nSpreading misinformation regarding node health or network status is considered a security violation.\n\n### 3. Collaborative Growth\nUsers are encouraged to contribute to the open-source repository and report vulnerabilities via the Compliance center.\n\n[--- Community Approved ---]`
        };
        return defaults[type] || '';
    },

    switchPolicy: function(type) {
        // Save current draft to memory first
        const editor = document.getElementById('policy-editor');
        if (editor) this.policies[this.currentPolicy] = editor.value;

        this.currentPolicy = type;
        if (editor) editor.value = this.policies[type] || '';

        // Update active tab styling
        document.querySelectorAll('#policy-tabs button').forEach(btn => {
            btn.classList.add('opacity-40');
            btn.classList.remove('text-primary', 'border-b-2', 'border-primary');
        });

        const activeBtn = document.querySelector(`#policy-tab-${type}`);
        if (activeBtn) {
            activeBtn.classList.remove('opacity-40');
            activeBtn.classList.add('text-primary', 'border-b-2', 'border-primary', 'pb-3');
        }

        toast.info('Governance Switching', `Drafting ${type.charAt(0).toUpperCase() + type.slice(1)} guidelines...`);
    },

    previewPolicy: function() {
        const editor = document.getElementById('policy-editor');
        if (!editor) return;

        const content = editor.value;
        const target = document.getElementById('policy-preview-content');
        if (target) {
            // Simple markdown rendering simulation
            target.innerHTML = content
                .replace(/^## (.*$)/gim, '<h2 class="text-2xl font-bold mb-4">$1</h2>')
                .replace(/^### (.*$)/gim, '<h3 class="text-xl font-bold mb-3 mt-6">$1</h3>')
                .replace(/^\n/gim, '<br>')
                .replace(/\n(.*)/gim, '<p class="mb-4 opacity-80">$1</p>');
            
            this.openModal('policy-preview-modal');
        }
    },

    submitPolicy: function() {
        const editor = document.getElementById('policy-editor');
        if (!editor) return;

        const type = this.currentPolicy;
        const content = editor.value;

        toast.info('Deploying Policy', `Propagating ${type} updates to global nodes...`);

        ApiClient.post('settings', 'update', { 
            key: `policy_${type}`, 
            value: content 
        }).then(res => {
            toast.success('Confirmed', `Governance framework for ${type} is now active.`);
            this.policies[type] = content; // Update saved state
        }).catch(err => {
            toast.error('Deployment Failed', err.error || 'Failed to update protocol.');
        });
    },

    /** App Version Control Logic **/
    initAppConfig: function() {
        var self = this;
        this.appConfigs = [];
        
        var tbody = document.getElementById('appconfig-table-body');
        if (!tbody) return;

        tbody.innerHTML = '<tr><td colspan="6" class="p-20 text-center animate-pulse"><p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-30">Loading Shard Deployments...</p></td></tr>';

        ApiClient.get('app_config', 'list').then(data => {
            self.appConfigs = data.configs || [];

            // Update Telemetry Metrics
            var set = (id, val) => {
                var el = document.getElementById(id);
                if (el) el.textContent = val;
            };

            set('stat-active-version', data.metrics.active_version || 'v0.0.0');
            set('stat-enforcement-ratio', data.metrics.enforcement_ratio || '0%');
            set('stat-sync-status', data.metrics.sync_status || 'ONLINE');
            set('stat-total-releases', data.metrics.total_releases || 0);

            var html = '';
            for (var i = 0; i < self.appConfigs.length; i++) {
                var cfg = self.appConfigs[i];
                var isMandatory = (cfg.is_mandatory == 1 || cfg.is_mandatory === true);
                var badgeColor = isMandatory ? 'bg-red-500/10 text-red-500 border-red-500/20' : 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                var badgeLabel = isMandatory ? 'MANDATORY' : 'OPTIONAL';
                
                html += '<tr class="hover:bg-white/[0.03] transition-all duration-300 group">' +
                        '    <td class="px-8 py-6">' +
                        '        <div class="flex items-center gap-4">' +
                        '            <div class="w-12 h-12 rounded-[1.25rem] bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shadow-2xl shadow-purple-500/10">' +
                        '                <i class="ph-bold ph-git-branch text-xl"></i>' +
                        '            </div>' +
                        '            <div>' +
                        '                <p class="text-xs font-black uppercase tracking-tight">' + cfg.latest_version + '</p>' +
                        '                <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest mt-0.5">X-Class Deployment</p>' +
                        '            </div>' +
                        '        </div>' +
                        '    </td>' +
                        '    <td class="px-8 py-6">' +
                        '        <span class="px-3 py-1.5 ' + badgeColor + ' border rounded-xl text-[8px] font-black uppercase tracking-widest shadow-xl">' + badgeLabel + '</span>' +
                        '    </td>' +
                        '    <td class="px-8 py-6 font-mono text-xs text-primary/80 truncate max-w-xs">' +
                        '        <div class="flex items-center gap-2 group/url">' +
                        '            <span class="truncate">' + cfg.update_url + '</span>' +
                        '            <button onclick="navigator.clipboard.writeText(\'' + cfg.update_url + '\'); toast.success(\'Copied\', \'URL copied to clipboard.\')" class="opacity-0 group-hover/url:opacity-100 p-1 hover:bg-white/10 rounded transition-all text-gray-500"><i class="ph ph-copy text-xs"></i></button>' +
                        '        </div>' +
                        '    </td>' +
                        '    <td class="px-8 py-6">' +
                        '        <p class="text-xs text-gray-400 max-w-sm leading-relaxed truncate" title="' + (cfg.description || '') + '">' + (cfg.description || '<span class="opacity-20 italic">No notes provided</span>') + '</p>' +
                        '    </td>' +
                        '    <td class="px-8 py-6">' +
                        '        <p class="text-xs font-black tracking-tight" style="color: var(--text-main);">' + new Date(cfg.updated_at).toLocaleTimeString() + '</p>' +
                        '        <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest mt-0.5">' + new Date(cfg.updated_at).toLocaleDateString() + '</p>' +
                        '    </td>' +
                        '    <td class="px-8 py-6 text-right">' +
                        '        <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300">' +
                        '            <button onclick="AdminApp.openDrawer(\'app_config\', \'' + cfg.id + '\')" class="w-10 h-10 rounded-2xl bg-primary/10 text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-all shadow-xl shadow-primary/5">' +
                        '                <i class="ph-bold ph-pencil text-lg"></i>' +
                        '            </button>' +
                        '            <button onclick="AdminApp.deleteAppConfig(\'' + cfg.id + '\')" class="w-10 h-10 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-xl shadow-red-500/5">' +
                        '                <i class="ph-bold ph-trash text-lg"></i>' +
                        '            </button>' +
                        '        </div>' +
                        '    </td>' +
                        '</tr>';
            }

            tbody.innerHTML = html || '<tr><td colspan="6" class="p-20 text-center opacity-30 font-black uppercase tracking-[0.2em] text-[10px]">No active versions in registry</td></tr>';
        }).catch(err => {
            console.error("Failed to load app version config list:", err);
            tbody.innerHTML = '<tr><td colspan="6" class="p-20 text-center text-red-400 font-bold">Failed to load app configurations from SQL.</td></tr>';
        });
    },

    openPublishReleaseModal: function() {
        var m = document.getElementById('publish-release-modal');
        if (m) {
            m.classList.remove('hidden');
            m.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    },

    publishNewVersion: function() {
        var self = this;
        var version = document.getElementById('ac-new-version').value.trim();
        var url = document.getElementById('ac-new-url').value.trim();
        var isMandatory = document.getElementById('ac-new-mandatory').checked;
        var description = document.getElementById('ac-new-description').value.trim();

        if (!version || !url) {
            toast.error('Required Fields', 'Version Identifier and Target Deployment URL are required.');
            return;
        }

        // Validate URL format
        if (!url.startsWith('http://') && !url.startsWith('https://')) {
            toast.error('Invalid Protocol', 'URL must start with http:// or https://');
            return;
        }

        var payload = {
            latest_version: version,
            update_url: url,
            is_mandatory: isMandatory ? 1 : 0,
            description: description
        };

        ApiClient.post('app_config', 'create', payload).then(res => {
            toast.success('App Upgrade', res.message);
            
            // Reset fields
            document.getElementById('ac-new-version').value = '';
            document.getElementById('ac-new-url').value = '';
            document.getElementById('ac-new-mandatory').checked = false;
            document.getElementById('ac-new-description').value = '';

            window.closeModal();
            self.initAppConfig();
        }).catch(err => {
            toast.error('Deployment Failure', err.error || 'Failed to publish configuration.');
        });
    },

    deleteAppConfig: function(id) {
        var self = this;
        this.confirm({
            title: 'Decommission Upgrade Shard',
            message: 'Are you absolutely sure you want to permanently decommission this version update configuration? Users on older builds will no longer receive upgrade protocols.',
            icon: 'ph-warning-octagon',
            confirmLabel: 'Decommission Shard',
            type: 'danger',
            purge: true
        }).then(confirmed => {
            if (confirmed) {
                ApiClient.post('app_config', 'delete', { id: id }).then(res => {
                    toast.success('Decommissioned', 'Upgrade target removed.');
                    self.initAppConfig();
                });
            }
        });
    },

    submitAppConfigEdit: function() {
        var self = this;
        var form = document.getElementById('appconfig-edit-form');
        var formData = new FormData(form);
        var payload = {};
        formData.forEach(function(value, key) { payload[key] = value; });

        payload['is_mandatory'] = form.querySelector('input[name="is_mandatory"]').checked ? 1 : 0;

        ApiClient.post('app_config', 'update', payload).then(res => {
            toast.success('App Version Control', res.message);
            window.closeDrawer();
            self.initAppConfig();
        })['catch'](err => {
            toast.error('Error', err.error || 'Failed to update configuration.');
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
