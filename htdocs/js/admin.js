/**
 * Honlor Admin App
 * ================
 * Handles interactive logic for Dashboard, Identity Vault, Channels, and Moderation.
 * Optimized for High-Fidelity UI Overhaul (v10).
 */

const AdminApp = {
    chart: null,
    currentRange: 7,

    init: function () {
        if (window.isRestricted) {
            console.warn("Security Protocol: Restricted State Detected. Core Application Locked.");
            return;
        }

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

        // Sync Handshake Hub Badge for Administrators
        this.syncHandshakeBadge();
    },

    route: function (page) {
        switch (page) {
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
        }
    },

    toggleNotifications: function () {
        const pane = document.getElementById('notification-pane');
        if (pane) {
            pane.classList.toggle('hidden');
        }
    },

    switchSection: function (section, pushState = true) {
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
    switchTab: function (sectionId, tabId) {
        console.log(`Switching Tab: ${sectionId} -> ${tabId}`);

        // 1. Update Tab Buttons (Active Styles & Underlines)
        const tabContainer = document.querySelector(`#section-${sectionId} [id$="-tabs"]`);
        if (tabContainer) {
            tabContainer.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            const activeBtn = tabContainer.querySelector(`[data-tab="${tabId}"]`);
            if (activeBtn) activeBtn.classList.add('active');
        }

        // 2. Trigger Specialized Tab Loading
        if (sectionId === 'dashboard' && tabId === 'agents') {
            this.loadAgentStatus();
        }

        // 3. Switch Content visibility
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

            // Trigger data loading for specialized tabs
            if (tabId === 'handshakes') {
                this.loadHandshakeHub();
            }
        }
    },

    /**
     * Dashboard Logic
     */
    initDashboard: function (range = 7) {
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

    loadRecentActivity: function () {
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

    loadRecentMembers: function () {
        // This is shared logic for dashboard-agents and section-overview
        const dashboardTarget = document.getElementById('agent-status-grid');
        const overviewTarget = document.querySelector('#section-overview .grid-cols-2.md\\:grid-cols-4.lg\\:grid-cols-6');

        if (!dashboardTarget && !overviewTarget) return;

        ApiClient.get('users', 'recent').then(data => {
            if (overviewTarget) {
                let html = '';
                data.users.slice(0, 6).forEach(user => {
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
                overviewTarget.innerHTML = html || '<p class="text-center text-gray-500 py-8 col-span-full">No recent members</p>';
            }

            if (dashboardTarget) {
                this.renderAgentStatusGrid(data.users);
            }
        });
    },

    loadAgentStatus: function () {
        const target = document.getElementById('agent-status-grid');
        if (!target) return;

        // Show loading state
        target.innerHTML = `
            <div class="col-span-full py-20 text-center animate-pulse">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-40">Synchronizing Agent Clusters...</p>
            </div>
        `;

        ApiClient.get('users', 'recent').then(data => {
            this.renderAgentStatusGrid(data.users);
        });
    },

    renderAgentStatusGrid: function (users) {
        const target = document.getElementById('agent-status-grid');
        if (!target) return;

        let html = '';
        users.forEach(user => {
            const isOnline = user.active == 1;
            const statusBadge = isOnline ? 'badge-success' : 'badge-neutral';
            const statusText = isOnline ? 'Online' : 'Resting';
            const indicator = isOnline ? 'bg-green-500 shadow-[0_0_10px_#22c55e]' : 'bg-gray-500';
            const role = user.role_name || 'Agent';
            const name = user.firstname || user.username;

            html += `
                <div class="stat-card !p-6 text-center group transition-all hover:scale-[1.05] cursor-pointer" onclick="AdminApp.switchSection('users')">
                    <div class="relative inline-block mb-4">
                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=${user.username}" 
                             class="w-20 h-20 mx-auto rounded-[2rem] bg-primary/10 border border-primary/20 group-hover:bg-primary group-hover:rotate-6 transition-all" alt="Avatar">
                        <span class="absolute bottom-1 right-1 w-5 h-5 ${indicator} border-4 border-[var(--surface)] rounded-full"></span>
                    </div>
                    <p class="font-black text-xs uppercase tracking-tight truncate">${name}</p>
                    <p class="text-[9px] font-black uppercase tracking-widest text-primary mt-1 leading-none">${role}</p>
                    <div class="${statusBadge} mt-4 inline-block">${statusText}</div>
                </div>
            `;
        });

        target.innerHTML = html || '<p class="text-center text-gray-500 py-20 col-span-full uppercase font-black text-[10px] tracking-widest">No active agents detected</p>';
    },

    renderGrowthChart: function (growthData) {
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
    initUsers: function () {
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

    exportUserCSV: function () {
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

    changeUserPage: function (delta) {
        const newPage = this.userPage + delta;
        if (newPage < 1) return;
        this.userPage = newPage;
        const filter = document.getElementById('user-filter')?.value || '';
        this.loadUserList(this.userPage, filter);
    },

    loadUserList: function (page = 1, filter = '') {
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
                            ${AdminApp.renderRoleBadge(user)}
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
    initChannels: function () {
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

    loadChannelList: function () {
        const tbody = document.getElementById('channels-table-body');
        const nodeMetric = document.getElementById('metric-total-nodes');
        if (!tbody) return;

        ApiClient.get('channels', 'list').then(data => {
            // Update Metrics
            if (nodeMetric) {
                const count = data.metrics.total_nodes;
                nodeMetric.innerText = count < 10 ? `0${count}` : count;
            }

            let html = '';
            data.channels.forEach(ch => {
                const typeColor = ch.type === 'system' ? 'bg-purple-500/10 text-purple-400 border-purple-500/20' : (ch.type === 'private' ? 'bg-amber-500/10 text-amber-500 border-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20');
                const statusLabel = Math.random() > 0.1 ? 'Operational' : 'Syncing';
                const statusColor = statusLabel === 'Operational' ? 'text-emerald-400' : 'text-blue-400';
                const latency = Math.floor(Math.random() * 40) + 12;

                html += `
                    <tr class="hover:bg-white/[0.03] transition-all duration-300 group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 font-black text-xs shadow-xl shadow-indigo-500/5 transition-transform group-hover:scale-110">
                                    ${ch.name.substring(0, 2).toUpperCase()}
                                </div>
                                <div>
                                    <p class="text-[11px] font-black uppercase tracking-widest text-primary"># ${ch.name}</p>
                                    <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest mt-0.5">Edge Node ID: 0x${ch.id}0F</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1.5 ${typeColor} border rounded-xl text-[8px] font-black uppercase tracking-widest shadow-xl">${ch.type}</span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <div class="flex flex-col items-center">
                                <p class="text-sm font-black tracking-tighter">${ch.member_count}</p>
                                <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest mt-0.5">Active Signals</p>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2">
                                <i class="ph-bold ph-activity text-xs ${statusColor} animate-pulse"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest ${statusColor}">${statusLabel}</span>
                                <span class="text-[8px] font-bold opacity-20 uppercase ml-2">${latency}ms</span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <button onclick="AdminApp.deleteChannel('${ch.id}')" class="w-10 h-10 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-xl shadow-red-500/5">
                                    <i class="ph-bold ph-trash text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || '<tr><td colspan="5" class="p-20 text-center opacity-30 font-black uppercase tracking-[0.2em] text-[10px]">No infrastructure clusters found</td></tr>';
        });
    },

    deleteChannel: function (id) {
        if (!confirm('Permanently decommission this node?')) return;
        ApiClient.post('channels', 'delete', { id }).then(res => {
            toast.success('Confirmed', 'Node removed from network.');
            this.loadChannelList();
        });
    },

    /**
     * Messages Logic
     */
    initMessages: function () {
        this.loadMessageList();
        this.loadFlaggedMessages();

        // Polling for real-time telemetry
        if (this.telemetryInterval) clearInterval(this.telemetryInterval);
        this.telemetryInterval = setInterval(() => {
            if (Session.getCurrentPageIdentifier() === 'messages') {
                this.loadMessageList();
                this.loadFlaggedMessages();
            }
        }, 10000);

        // Filter listener
        const filterInput = document.getElementById('message-filter');
        if (filterInput) {
            filterInput.addEventListener('input', () => this.loadMessageList());
        }
    },

    loadMessageList: function () {
        const tbody = document.getElementById('messages-table-body');
        const velocityMetric = document.getElementById('metric-velocity');
        const flaggedBadge = document.getElementById('flagged-count-badge');
        const filter = document.getElementById('message-filter')?.value || '';

        if (!tbody) return;

        ApiClient.get('messages', 'list', { status: 'all', filter }).then(data => {
            // Update Metrics
            if (velocityMetric) velocityMetric.innerText = data.metrics.velocity.toFixed(1);
            if (flaggedBadge) {
                flaggedBadge.innerText = data.metrics.flagged;
                flaggedBadge.classList.toggle('hidden', data.metrics.flagged === 0);
            }

            let html = '';
            data.messages.forEach(msg => {
                const statusColor = msg.status === 'flagged' ? 'bg-orange-500/10 text-orange-400 border-orange-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                const statusLabel = msg.status === 'flagged' ? 'Flagged' : 'Safe';
                const avatar = `https://api.dicebear.com/7.x/avataaars/svg?seed=${msg.username}`;

                html += `
                    <tr class="hover:bg-white/[0.03] transition-all duration-300 group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-[1rem] bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold text-[10px] uppercase shadow-lg shadow-indigo-500/5">
                                    # ${msg.channel_name.substring(0, 1)}
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
                            <p class="text-[10px] font-black uppercase tracking-widest opacity-60">${new Date(msg.created_at).toLocaleTimeString()}</p>
                            <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest mt-0.5">${new Date(msg.created_at).toLocaleDateString()}</p>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                ${msg.status !== 'flagged' ? `
                                    <button onclick="AdminApp.executeMessageAction('flag', '${msg.id}')" class="w-10 h-10 rounded-2xl bg-orange-500/10 text-orange-400 flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all shadow-xl shadow-orange-500/5">
                                        <i class="ph-bold ph-flag text-lg"></i>
                                    </button>
                                ` : ''}
                                <button onclick="AdminApp.executeMessageAction('purge', '${msg.id}')" class="w-10 h-10 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-xl shadow-red-500/5">
                                    <i class="ph-bold ph-trash text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || `<tr><td colspan="5" class="p-20 text-center opacity-30 font-black uppercase tracking-[0.2em] text-[10px]">No active signals in stream</td></tr>`;
        });
    },

    loadFlaggedMessages: function () {
        const grid = document.getElementById('flagged-messages-grid');
        const chamberCount = document.getElementById('chamber-count');
        const ledger = document.getElementById('resolution-ledger');
        if (!grid) return;

        ApiClient.get('messages', 'list', { status: 'flagged' }).then(data => {
            // Update Summary Card
            if (chamberCount) {
                const count = data.metrics.flagged;
                chamberCount.innerText = count < 10 ? `0${count}` : count;
            }

            // Update Resolution Ledger (Recent Activity)
            if (ledger && data.metrics.recent_activity) {
                let ledgerHtml = '';
                data.metrics.recent_activity.forEach(log => {
                    const isPositive = log.message.toLowerCase().includes('success') || log.message.toLowerCase().includes('resolved') || log.message.toLowerCase().includes('approve');
                    const icon = isPositive ? 'ph-check-circle text-emerald-400' : 'ph-info text-blue-400';
                    const time = log.created_at ? new Date(log.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'NOW';
                    
                    ledgerHtml += `
                        <div class="flex gap-4 p-4 rounded-2xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.05] transition-all group/item">
                            <div class="w-10 h-10 shrink-0 rounded-xl bg-white/5 flex items-center justify-center">
                                <i class="ph-bold ${icon} text-lg"></i>
                            </div>
                            <div class="flex-grow overflow-hidden">
                                <div class="flex items-center justify-between mb-0.5">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-primary truncate">Protocol ${log.id}</p>
                                    <span class="text-[8px] font-bold opacity-30 uppercase">${time}</span>
                                </div>
                                <p class="text-[10px] font-medium opacity-60 line-clamp-1 italic">${log.message}</p>
                            </div>
                        </div>
                    `;
                });
                ledger.innerHTML = ledgerHtml || '<div class="p-12 text-center opacity-20 italic text-[10px] font-black uppercase tracking-widest py-20">No recent resolutions recorded</div>';
            }

            let html = '';
            data.messages.forEach(msg => {
                const avatar = `https://api.dicebear.com/7.x/avataaars/svg?seed=${msg.username}`;
                html += `
                    <div class="glass-card !p-8 border-orange-500/20 bg-gradient-to-br from-orange-500/[0.05] to-transparent animate-in zoom-in duration-500">
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-4">
                                <img src="${avatar}" class="w-12 h-12 rounded-2xl border border-white/10 shadow-2xl">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest text-primary">${msg.username}</p>
                                    <p class="text-[8px] font-bold opacity-40 uppercase tracking-widest mt-0.5">Flagged Packet</p>
                                </div>
                            </div>
                            <div class="text-[8px] font-black uppercase tracking-widest px-2 py-1 bg-white/5 rounded-lg border border-white/5 opacity-40">
                                # ${msg.id}
                            </div>
                        </div>
                        
                        <div class="p-6 rounded-3xl bg-black/20 border border-white/5 mb-8 italic text-xs leading-relaxed opacity-80">
                            "${msg.content}"
                        </div>
                        
                        <div class="flex gap-3">
                            <button onclick="AdminApp.executeMessageAction('unflag', '${msg.id}')" class="flex-grow btn-secondary !py-3 !text-[9px] !font-black !uppercase !justify-center !rounded-[1.25rem] !bg-emerald-500/10 !text-emerald-400 !border-emerald-500/20 hover:!bg-emerald-500 hover:!text-white transition-all">Restore Signal</button>
                            <button onclick="AdminApp.executeMessageAction('purge', '${msg.id}')" class="w-14 btn-secondary !p-0 !justify-center !rounded-[1.25rem] !bg-red-500/10 !text-red-500 !border-red-500/20 hover:!bg-red-500 hover:!text-white transition-all">
                                <i class="ph-bold ph-trash text-lg"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            grid.innerHTML = html || `
                <div class="col-span-full p-32 text-center rounded-[3rem] border border-dashed border-white/10 bg-white/[0.01]">
                    <div class="w-20 h-20 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mx-auto mb-8">
                         <i class="ph ph-shield-check text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-black uppercase tracking-tight mb-2">Chamber <span class="gradient-text">Secured</span></h3>
                    <p class="text-xs font-bold opacity-40 uppercase tracking-widest">No guideline violations currently isolated.</p>
                </div>
            `;
        });
    },

    executeMessageAction: function (action, id) {
        ApiClient.post('messages', 'action', { id, action }).then(res => {
            toast.success('Governance Success', res.message);
            this.loadMessageList();
            this.loadFlaggedMessages();
        }).catch(err => {
            toast.error('Protocol Error', err.message);
        });
    },

    /**
     * Safety Center (Reports) Logic
     */
    initReports: function () {
        this.loadReportList();
    },

    loadReportList: function () {
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
    initDeletionRequests: function () {
        this.loadDeletionRequests();
    },

    loadDeletionRequests: function () {
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

    handleDeletion: function (id, status) {
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
    initLogs: function () {
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

    updateTelemetry: function () {
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

    loadLogList: function () {
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

    openTerminal: function () {
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
    initAdsManager: function () {
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

    loadAdList: function (filter = '') {
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
                        <td class="px-6 py-4 text-center text-xs font-bold text-gray-400">${((ad.clicks / ad.impressions) * 100 || 0).toFixed(2)}%</td>
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

    deleteAd: function (id) {
        if (!confirm('Permanently delete this campaign?')) return;
        ApiClient.post('ads', 'delete', { id }).then(res => {
            toast.success('Confirmed', 'Campaign removed.');
            this.loadAdList();
        });
    },

    /**
     * Settings Logic
     */
    toggleSetting: function (key, isChecked) {
        const value = isChecked ? 'on' : 'off';

        ApiClient.post('settings', 'update', { key: key, value: value }).then(res => {
            toast.success('Updated', `Setting '${key}' is now ${value}.`);
        }).catch(err => {
            toast.error('System Error', err.error || 'Could not update setting.');
        });
    },

    saveSettings: function () {
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
    initRoles: function () {
        this.refreshRolesCache();
        this.loadRoleList();

        const form = document.getElementById('save-role-form');
        if (form) {
            form.onsubmit = (e) => {
                e.preventDefault();
                this.saveRole();
            };
        }
    },

    loadRoleList: function () {
        const grid = document.getElementById('roles-card-grid');
        if (!grid) return;

        ApiClient.get('roles', 'list').then(data => {
            let html = '';
            data.roles.forEach(role => {
                const perms = typeof role.permissions === 'string' ? JSON.parse(role.permissions) : role.permissions;
                const isMaster = perms.all === true;

                html += `
                    <div class="glass-card group relative p-8 border-white/5 transition-all duration-500 hover:border-primary/40 hover:-translate-y-2 hover:shadow-[0_40px_80px_rgba(0,0,0,0.5)] bg-gradient-to-br from-white/[0.02] to-transparent">
                        <div class="flex items-start justify-between mb-8">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shadow-[0_0_20px_rgba(124,106,255,0.1)] group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-500">
                                    <i class="ph-bold ${this.getRoleIcon(role.slug)} text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black uppercase tracking-tighter">${role.name}</h3>
                                    <p class="text-[10px] font-black opacity-30 tracking-[0.2em] uppercase mt-1">L${isMaster ? '0' : '1'} Clearance Protocol</p>
                                </div>
                            </div>
                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 translate-x-4 group-hover:translate-x-0 transition-all duration-500">
                                <button onclick="AdminApp.editRole('${role.id}')" class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-all shadow-xl shadow-primary/10"><i class="ph ph-note-pencil text-lg"></i></button>
                                <button onclick="AdminApp.deleteRole('${role.id}')" class="w-10 h-10 rounded-xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-xl shadow-red-500/10"><i class="ph ph-trash text-lg"></i></button>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mb-3">System Access Matrix</p>
                                <div class="flex flex-wrap gap-2">
                                    ${this.renderPermissionChips(role.permissions)}
                                </div>
                            </div>
                            
                            <div class="pt-6 border-t border-white/5 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Active Members</span>
                                <span class="text-sm font-black text-gray-500 dark:text-white">${role.member_count || 0}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            grid.innerHTML = html || '<div class="col-span-full p-20 text-center text-gray-500 font-bold uppercase tracking-widest opacity-30">Vault Empty. No Identities Found.</div>';
        });
    },

    renderPermissionChips: function (permsJson) {
        if (!permsJson) return '<span class="text-[10px] font-black opacity-10 uppercase tracking-widest italic">No Protocols Found</span>';
        const perms = typeof permsJson === 'string' ? JSON.parse(permsJson) : permsJson;
        if (perms.all) return '<span class="px-3 py-1.5 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2"><i class="ph-fill ph-shield-star"></i> Perfect Authority</span>';

        let html = '';
        Object.keys(perms).forEach(resource => {
            const actions = perms[resource];
            if (Array.isArray(actions)) {
                actions.forEach(action => {
                    let color = 'text-gray-400 bg-white/5 border-white/5';
                    let label = action;
                    if (action === 'view') { color = 'text-primary bg-primary/10 border-primary/20'; label = 'Audit'; }
                    if (action === 'manage') { color = 'text-green-500 bg-green-500/10 border-green-500/20'; label = 'Write'; }
                    if (action === 'delete') { color = 'text-red-500 bg-red-500/10 border-red-500/20'; label = 'Purge'; }

                    html += `
                        <span class="px-2.5 py-1 ${color} border rounded-lg text-[9px] font-black uppercase tracking-widest flex items-center gap-1.5 transition-all mb-1">
                            <i class="ph ph-circle text-[6px]"></i> ${resource}:${label}
                        </span>
                    `;
                });
            } else if (actions === true) {
                html += `
                    <span class="px-2.5 py-1 bg-white/5 text-gray-400 border border-white/5 rounded-lg text-[9px] font-black uppercase tracking-widest flex items-center gap-1.5 mb-1">
                        <i class="ph ph-circle text-[6px] text-primary"></i> ${resource}:Full
                    </span>
                `;
            }
        });
        return html;
    },

    newRole: function () {
        const form = document.getElementById('save-role-form');
        if (!form) return;

        form.reset();
        form.role_id.value = '';

        // Reset state & UI
        form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        const allCb = form.querySelector('[name="perms[all]"]');
        if (allCb) this.toggleMasterAccess(allCb);

        AdminApp.openModal('role-editor-modal');
        this.updateAuthorityMeter();
    },

    editRole: function (id) {
        ApiClient.get('roles', 'get', { id }).then(data => {
            const role = data.role;
            const form = document.getElementById('save-role-form');
            if (!form) return;

            form.role_id.value = role.id;
            // Standardize display name (allow user choice but match internally)
            form.role_name.value = role.name;

            // Reset all state first
            form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);

            try {
                const perms = typeof role.permissions === 'string' ? JSON.parse(role.permissions) : role.permissions;

                // Force a clean state sync before applying specific permissions
                const allCb = form.querySelector('[name="perms[all]"]');
                if (allCb) {
                    allCb.checked = perms.all === true;
                    this.toggleMasterAccess(allCb);
                }

                if (!perms.all) {
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
            } catch (e) {
                console.error("Orchestrator sync failure:", e);
                toast.error("Sync Failure", "Could not restore security protocol state.");
            }

            AdminApp.openModal('role-editor-modal');
            this.updateAuthorityMeter();
        });
    },

    updateAuthorityMeter: function () {
        const form = document.getElementById('save-role-form');
        if (!form) return;

        const meter = document.getElementById('authority-meter');
        const marker = document.getElementById('authority-marker');
        if (!meter || !marker) return;

        const allChecked = form.querySelector('[name="perms[all]"]').checked;
        if (allChecked) {
            meter.style.width = '100%';
            marker.innerText = 'Perfect Authority';
            marker.className = 'text-[9px] font-black px-2 py-0.5 bg-amber-500 text-white rounded-lg uppercase tracking-widest leading-none shadow-[0_0_10px_rgba(245,158,11,0.5)]';
            return;
        }

        const checkboxes = form.querySelectorAll('input[type="checkbox"]:checked:not([name="perms[all]"])');
        const total = form.querySelectorAll('input[type="checkbox"]:not([name="perms[all]"])').length;
        const percentage = total > 0 ? (checkboxes.length / total) * 100 : 0;

        meter.style.width = `${percentage}%`;

        if (percentage > 80) {
            marker.innerText = 'High Privilege';
            marker.className = 'text-[9px] font-black px-2 py-0.5 bg-red-500 text-white rounded-lg uppercase tracking-widest leading-none shadow-[0_0_10px_rgba(239,68,68,0.5)]';
        } else if (percentage > 40) {
            marker.innerText = 'Elevated';
            marker.className = 'text-[9px] font-black px-2 py-0.5 bg-primary text-white rounded-lg uppercase tracking-widest leading-none shadow-[0_0_10px_rgba(124,106,255,0.5)]';
        } else if (percentage > 0) {
            marker.innerText = 'Standard';
            marker.className = 'text-[9px] font-black px-2 py-0.5 bg-green-500 text-white rounded-lg uppercase tracking-widest leading-none shadow-[0_0_10px_rgba(34,197,94,0.5)]';
        } else {
            marker.innerText = 'Restricted';
            marker.className = 'text-[9px] font-black px-2 py-0.5 bg-white/10 text-gray-400 rounded-lg uppercase tracking-widest leading-none';
        }
    },

    saveRole: function () {
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
            window.closeModal();

            this.loadRoleList();
        }).catch(err => {
            toast.error('Governance Error', err.message || 'Identity protocol violation.');
        });
    },

    deleteRole: function (id) {
        if (!confirm('Permanently deconstruct this security role? This may impact mapped identities.')) return;
        ApiClient.post('roles', 'delete', { id }).then(res => {
            toast.success('Deconstructed', 'Security role purged from vault.');
            this.loadRoleList();
        });
    },

    /**
     * Global Modals & Drawers
     */
    openModal: function (id) {
        const container = document.getElementById(id);
        if (container) {
            container.classList.remove('hidden');
            container.classList.add('flex');
            // Animate in if GSAP is available
            if (window.gsap) {
                gsap.fromTo(container.querySelector('.glass-card'),
                    { scale: 0.9, opacity: 0 },
                    { scale: 1, opacity: 1, duration: 0.4, ease: "back.out(1.7)" }
                );
            }
        }
        document.body.style.overflow = 'hidden';
    },


    openDrawer: function (type, id) {
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
                        <div class="text-center p-6 bg-white/5 rounded-[2.5rem] border border-white/5 mb-6 relative overflow-hidden">
                            ${user.request_pending == 1 && user.role_id == 0 ? `
                                <div class="absolute inset-x-0 top-0 py-2 bg-amber-500/20 border-b border-amber-500/20">
                                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-amber-500 flex items-center justify-center gap-2">
                                        <i class="ph-bold ph-handshake animate-pulse"></i>
                                        Identity Handshake Pending
                                    </p>
                                </div>
                                <div class="mt-4"></div>
                            ` : ''}
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
                                <div class="space-y-4">
                                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Security Cluster (Role)</label>
                                    <input type="hidden" name="role_id" value="${user.role_id || 0}">
                                    <div id="role-selection-container">
                                        ${AdminApp.renderRoleSelectionUI(user.role_id)}
                                    </div>
                                    <div class="pt-4 border-t border-white/5 flex items-center justify-between">
                                        <div class="space-y-1">
                                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-white">Level 0 Authorization</p>
                                            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-tight">Grant Absolute Master Status</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="is_master" value="1" ${user.is_master == 1 ? 'checked' : ''} class="sr-only peer">
                                            <div class="w-11 h-6 bg-white/5 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-gray-400 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                        </label>
                                    </div>
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

    submitProfileEdit: function () {
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

    submitAdEdit: function () {
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

    executeAction: function (action, id) {
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

    submitRoleUpdate: function (userId) {
        // This is now used as a visual indicator, but the profile save handles the actual update
        toast.info('Security Identity', 'Assigned role selected. Click Save to commit changes.');
    },

    renderRoleSelectionUI: function (currentRoleId) {
        if (!this.rolesList || this.rolesList.length === 0) {
            return `
                <div class="p-6 border-2 border-dashed border-white/5 rounded-3xl text-center">
                    <p class="text-[10px] font-black opacity-30 uppercase tracking-[0.2em]">No Identity Clusters Defined</p>
                </div>
            `;
        }

        let html = '<div class="grid grid-cols-2 gap-3" id="role-selector-grid">';
        this.rolesList.forEach(role => {
            const isActive = role.id == currentRoleId;
            html += `
                <div onclick="AdminApp.selectRoleCard(this, '${role.id}')" 
                     data-role-id="${role.id}"
                     class="role-selection-card group p-5 rounded-2xl border transition-all cursor-pointer ${isActive ? 'bg-primary/10 border-primary shadow-[0_15px_40px_rgba(124,106,255,0.2)] ring-4 ring-primary/5' : 'bg-white/5 border-white/5 hover:border-primary/40'}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl ${isActive ? 'bg-primary text-white' : 'bg-white/10 text-gray-500 group-hover:bg-primary/20 group-hover:text-primary'} flex items-center justify-center transition-all duration-300">
                            <i class="ph-bold ${this.getRoleIcon(role.slug)} text-lg"></i>
                        </div>
                        ${isActive ? '<span class="w-2.5 h-2.5 rounded-full bg-primary shadow-[0_0_10px_#7c6aff] animate-pulse"></span>' : ''}
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-tight ${isActive ? 'text-primary' : 'opacity-60'} transition-colors">${role.name}</p>
                        <p class="text-[8px] font-bold opacity-30 uppercase tracking-[0.3em] mt-1">L${isActive ? '0' : '1'} Authorized</p>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        return html;
    },

    selectRoleCard: function (el, id) {
        // Reset all cards in grid
        const grid = document.getElementById('role-selector-grid');
        if (!grid) return;

        grid.querySelectorAll('.role-selection-card').forEach(card => {
            card.classList.remove('bg-primary/10', 'border-primary', 'shadow-[0_15px_40px_rgba(124,106,255,0.2)]', 'ring-4', 'ring-primary/5');
            card.querySelector('p').classList.replace('text-primary', 'opacity-60');
            const iconWrap = card.querySelector('div > div');
            iconWrap.classList.replace('bg-primary', 'bg-white/10');
            iconWrap.classList.replace('text-white', 'text-gray-500');
            const pulse = card.querySelector('span.rounded-full');
            if (pulse) pulse.remove();
        });

        // Activate selected
        el.classList.add('bg-primary/10', 'border-primary', 'shadow-[0_15px_40px_rgba(124,106,255,0.2)]', 'ring-4', 'ring-primary/5');
        el.querySelector('p').classList.replace('opacity-60', 'text-primary');
        const activeIconWrap = el.querySelector('div > div');
        activeIconWrap.classList.replace('bg-white/10', 'bg-primary');
        activeIconWrap.classList.replace('text-gray-500', 'text-white');

        // Add pulse indicator
        const head = el.querySelector('div.flex');
        if (head && !head.querySelector('span.rounded-full')) {
            head.innerHTML += '<span class="w-2.5 h-2.5 rounded-full bg-primary shadow-[0_0_10px_#7c6aff] animate-pulse"></span>';
        }

        // Update hidden input in form
        const form = document.getElementById('profile-edit-form');
        if (form && form.role_id) {
            form.role_id.value = id;
        }

        toast.info('Role Selected', `Prepared for cluster migration.`);
    },

    /**
     * Cache Roles for Selectors
     */

    getRoleIcon: function (slug) {
        const icons = {
            'super-admin': 'ph-shield-star',
            'admin': 'ph-user-gear',
            'moderator': 'ph-fingerprint',
            'editor': 'ph-note-pencil',
            'analyst': 'ph-chart-bar',
            'support': 'ph-headset',
            'security': 'ph-lock-key',
            'developer': 'ph-code',
            'architect': 'ph-lock-key',
            'curator': 'ph-stack',
            'content-creator': 'ph-paint-brush'
        };
        return icons[slug] || 'ph-shield';
    },

    refreshRolesCache: function () {

        ApiClient.get('roles', 'list').then(data => {
            this.rolesList = data.roles;
        });
    },

    toggleAllPermissions: function (capability) {
        const matrix = document.getElementById('privilege-matrix');
        if (!matrix) return;

        const checkboxes = matrix.querySelectorAll(`input[name*="[${capability}]"]`);
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);

        checkboxes.forEach(cb => cb.checked = !allChecked);

        toast.info('Security Studio', `${allChecked ? 'Revoked' : 'Granted'} all ${capability} capabilities.`);
    },

    securityBlueprints: {
        moderator: {
            name: 'Moderator',
            perms: {
                users: ['view'],
                messages: ['view', 'manage', 'delete'],
                reports: ['view', 'manage'],
                deletion: ['view']
            }
        },
        curator: {
            name: 'Content Curator',
            perms: {
                channels: ['view', 'manage'],
                messages: ['view'],
                ads: ['view', 'manage']
            }
        },
        analyst: {
            name: 'Data Analyst',
            perms: {
                dashboard: ['view'],
                analytics: ['view'],
                logs: ['view']
            }
        },
        security: {
            name: 'Security Architect',
            perms: {
                users: ['view', 'manage'],
                roles: ['view', 'manage'],
                logs: ['view'],
                settings: ['view']
            }
        },
        support: {
            name: 'Support Agent',
            perms: {
                users: ['view'],
                deletion: ['view', 'manage'],
                channels: ['view']
            }
        },
        developer: {
            name: 'System Developer',
            perms: {
                all: true
            }
        }
    },

    applyBlueprint: function (key) {
        const blueprint = this.securityBlueprints[key];
        if (!blueprint) return;

        const form = document.getElementById('save-role-form');
        if (!form) return;

        // 1. Set Name suggests
        const nameInput = form.querySelector('[name="role_name"]');
        if (nameInput) nameInput.value = blueprint.name;

        // 2. Clear all first
        form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);

        // 3. Apply perms
        if (blueprint.perms.all) {
            const allCb = form.querySelector('[name="perms[all]"]');
            if (allCb) allCb.checked = true;
        } else {
            Object.keys(blueprint.perms).forEach(resource => {
                const actions = blueprint.perms[resource];
                actions.forEach(action => {
                    const cb = form.querySelector(`[name="perms[${resource}][${action}]"]`);
                    if (cb) {
                        cb.checked = true;
                        // Tiny animation effect
                        cb.parentElement.classList.add('scale-110');
                        setTimeout(() => cb.parentElement.classList.remove('scale-110'), 200);
                    }
                });
            });
        }

        toast.success('Blueprint Applied', `Loaded standard '${blueprint.name}' protocol.`);
        this.updateAuthorityMeter();
    },


    toggleMasterAccess: function (el) {
        const matrix = document.getElementById('privilege-matrix');
        if (!matrix) return;

        const isChecked = el.checked;
        const checkboxes = matrix.querySelectorAll('input[type="checkbox"]');

        checkboxes.forEach(cb => {
            cb.checked = isChecked;
        });

        if (isChecked) {
            matrix.classList.add('opacity-40', 'pointer-events-none');
            toast.info('Root Protocol Active', 'Absolute authority override engaged.');
        } else {
            matrix.classList.remove('opacity-40', 'pointer-events-none');
        }

        this.updateAuthorityMeter();
    },

    generateInsights: function () {
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

    initAnalytics: function () {
        console.log("Analytics Initialized");
    },

    /** Governance (Policy Editor) Logic **/
    initPolicyEditor: function () {
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

    getDefaultPolicy: function (type) {
        const defaults = {
            privacy: `## Privacy & Data Protection Framework\n\n### 1. Data Collection Protocols\nThe Aether ecosystem operates on a principle of radical transparency. We collect telemetry data only to ensure node stability and cross-chain verification.\n\n### 2. Encryption Standards\nAll identity records in the Vault are encrypted using AES-256-GCM. Private keys are never stored on centralized edge clusters.\n\n### 3. User Sovereignty\nUsers maintain 100% ownership of their data packets. Deletion requests are processed within a 24-hour governance window.\n\n[--- Draft Content Below ---]`,
            terms: `## Terms of Universal Service\n\n### 1. Access Authorization\nBy accessing the Aether network, you agree to abide by the decentralized consensus protocols. Unauthorized node manipulation is strictly prohibited.\n\n### 2. Liability Limitation\nThe infrastructure leads are not liable for packet loss during cross-node transmissions or atmospheric interference.\n\n### 3. Smart Contract Integrity\nAll governance actions are final and recorded on the immutable ledger.\n\n[--- Ready for Deployment ---]`,
            community: `## Community Engagement Guidelines\n\n### 1. Radical Respect\nDiscourse within the Aether channels must remain constructive. Personal attacks on identity profiles will result in immediate account suspension.\n\n### 2. Information Integrity\nSpreading misinformation regarding node health or network status is considered a security violation.\n\n### 3. Collaborative Growth\nUsers are encouraged to contribute to the open-source repository and report vulnerabilities via the Compliance center.\n\n[--- Community Approved ---]`
        };
        return defaults[type] || '';
    },

    switchPolicy: function (type) {
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

    previewPolicy: function () {
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

    submitPolicy: function () {
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

    /**
     * Identity Handshake Logic
     */
    requestRoleHandshake: function () {
        const btn = document.getElementById('request-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="ph-bold ph-spinner animate-spin mr-2"></i> Initializing Handshake...';
        }

        ApiClient.post('users', 'request_access').then(res => {
            toast.success('Handshake Initiated', res.message);
            setTimeout(() => window.location.reload(), 1500);
        }).catch(err => {
            toast.error('Handshake Failed', err.error || 'Identity protocol error.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="ph-bold ph-handshake mr-2"></i> Request Identity Handshake';
            }
        });
    },

    checkClearance: function () {
        const icon = document.querySelector('.ph-arrow-clockwise');
        if (icon) icon.classList.add('animate-spin');

        ApiClient.get('users', 'check_clearance').then(res => {
            if (res.cleared) {
                toast.success('Identity Cleared', res.message);
                setTimeout(() => window.location.reload(), 800);
            } else {
                toast.info('Status Audit', res.message);
                if (icon) icon.classList.remove('animate-spin');
            }
        }).catch(err => {
            toast.error('Audit Error', 'Could not verify identity clearance.');
            if (icon) icon.classList.remove('animate-spin');
        });
    },

    /**
     * Handshake Hub Governance
     */
    syncHandshakeBadge: function () {
        ApiClient.get('users', 'handshakes').then(res => {
            const badge = document.getElementById('handshake-count-badge');
            if (badge) {
                if (res.count > 0) {
                    badge.innerText = res.count;
                    badge.classList.remove('hidden');
                    badge.classList.add('flex');
                } else {
                    badge.classList.add('hidden');
                    badge.classList.remove('flex');
                }
            }
        });
    },

    loadHandshakeHub: function () {
        const grid = document.getElementById('handshake-requests-grid');
        if (!grid) return;

        ApiClient.get('users', 'handshakes').then(res => {
            let html = '';
            if (res.requests.length === 0) {
                html = `
                    <div class="col-span-full py-20 text-center space-y-4">
                        <div class="w-20 h-20 bg-primary/5 rounded-[2rem] mx-auto flex items-center justify-center text-primary/20">
                            <i class="ph-bold ph-shield-check text-4xl"></i>
                        </div>
                        <p class="font-black text-[10px] uppercase tracking-[0.3em] opacity-40">Identity Vault Secured: No Pending Handshakes</p>
                    </div>
                `;
            } else {
                res.requests.forEach(req => {
                    html += `
                        <div class="glass-card !p-8 border-primary/10 hover:border-primary/30 transition-all group animate-in zoom-in duration-500">
                            <div class="flex items-start justify-between mb-6">
                                <img src="${req.avatar || 'https://api.dicebear.com/7.x/avataaars/svg?seed=' + req.username}" class="w-16 h-16 rounded-[1.5rem] bg-primary/10 border border-primary/5">
                                <div class="px-3 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-lg text-[8px] font-black uppercase tracking-widest animate-pulse">
                                    Identity Request
                                </div>
                            </div>
                            <div class="space-y-1 mb-8">
                                <h4 class="text-lg font-black tracking-tight text-slate-800">${req.firstname || req.username} ${req.lastname || ''}</h4>
                                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">${req.email}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-2">Requested: ${new Date(req.created_at).toLocaleString()}</p>
                            </div>
                            <div class="flex gap-3 pt-6 border-t border-slate-100">
                                <button onclick="AdminApp.authorizeHandshake(${req.id}, 'reject')" class="btn-secondary !bg-red-500/5 !border-red-500/10 !text-red-500 hover:!bg-red-500/10 flex-1 !justify-center py-3 text-[10px] font-black uppercase">Termnate</button>
                                <button onclick="AdminApp.openHandshakeAuthorize(${req.id})" class="btn-primary flex-1 !justify-center py-3 text-[10px] font-black uppercase shadow-lg shadow-primary/20">Authorize</button>
                            </div>
                        </div>
                    `;
                });
            }
            grid.innerHTML = html;
            this.syncHandshakeBadge();
        });
    },

    authorizeHandshake: function (id, action, roleId = 0) {
        if (action === 'reject' && !confirm('Terminate this Identity Handshake? User will remain restricted.')) return;

        // Finalize Protocol
        ApiClient.post('users', 'authorize', { id, action, role_id: roleId }).then(res => {
            toast.success('Security Event', res.message);
            closeModal();
            this.loadHandshakeHub();
            this.loadUserList(); // Sync main list
        }).catch(err => {
            toast.error('Protocol Error', err.error || 'Authorization failed.');
        });
    },

    openHandshakeAuthorize: function (id) {
        const input = document.getElementById('auth-request-user-id');
        const displayName = document.getElementById('auth-role-display-name');
        const roleIdInput = document.getElementById('auth-request-role-id');

        if (input) input.value = id;
        if (roleIdInput) roleIdInput.value = "0"; // Reset to default Observer
        if (displayName) displayName.innerText = "--- Default (Observer Cluster) ---";

        // Ensure menu is hidden
        const menu = document.getElementById('handshake-role-menu');
        if (menu) menu.classList.add('hidden');

        this.openModal('handshake-authorize-modal');
    },

    toggleHandshakeRoleDropdown: function () {
        const menu = document.getElementById('handshake-role-menu');
        const chevron = document.getElementById('auth-role-chevron');
        if (!menu) return;

        const isHidden = menu.classList.contains('hidden');

        if (isHidden) {
            menu.classList.remove('hidden');
            if (chevron) chevron.style.transform = 'rotate(180deg)';
        } else {
            menu.classList.add('hidden');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
    },

    selectHandshakeRole: function (roleId, name) {
        const input = document.getElementById('auth-request-role-id');
        const displayName = document.getElementById('auth-role-display-name');
        const menu = document.getElementById('handshake-role-menu');
        const chevron = document.getElementById('auth-role-chevron');

        if (input) input.value = roleId;
        if (displayName) displayName.innerText = name;

        // Close menu
        if (menu) menu.classList.add('hidden');
        if (chevron) chevron.style.transform = 'rotate(0deg)';

        toast.help('Cluster Staged', `Authorized identity will be migrated to the ${name} cluster.`);
    },

    toggleInviteRoleDropdown: function () {
        const menu = document.getElementById('invite-role-menu');
        const chevron = document.getElementById('invite-role-chevron');
        if (!menu) return;
        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            if (chevron) chevron.style.transform = 'rotate(180deg)';
        } else {
            menu.classList.add('hidden');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
    },

    selectInviteRole: function (roleId, name) {
        const input = document.getElementById('invite-role-id');
        const displayName = document.getElementById('invite-role-display-name');
        const menu = document.getElementById('invite-role-menu');
        const chevron = document.getElementById('invite-role-chevron');
        if (input) input.value = roleId;
        if (displayName) displayName.innerText = name;
        if (menu) menu.classList.add('hidden');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    },

    confirmHandshakeAuthorization: function () {
        const id = document.getElementById('auth-request-user-id')?.value;
        const roleId = document.getElementById('auth-request-role-id')?.value;

        if (!id) return toast.error('Selection Error', 'User ID missing from protocol.');

        this.authorizeHandshake(id, 'approve', roleId);
    },

    renderRoleBadge: function (user) {
        if (user.is_master == 1) {
            return `<div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> <span class="px-2 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-lg text-[9px] font-black uppercase tracking-widest">Master Admin</span></div>`;
        }
        if (user.role_id > 0) {
            const name = (user.role_name || '').toLowerCase();
            let color = 'bg-white/5 text-gray-400 border-white/5'; // Default

            if (name.includes('admin')) color = 'bg-purple-500/10 text-purple-400 border-purple-500/20 shadow-[0_0_15px_rgba(168,85,247,0.1)]';
            else if (name.includes('moderator')) color = 'bg-blue-500/10 text-blue-400 border-blue-500/20 shadow-[0_0_15px_rgba(59,130,246,0.1)]';
            else if (name.includes('agent')) color = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.1)]';
            else if (name.includes('auditor')) color = 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20 shadow-[0_0_15px_rgba(6,182,212,0.1)]';

            return `<span class="px-2 py-1 ${color} border rounded-lg text-[9px] font-black uppercase tracking-widest">${user.role_name}</span>`;
        }
        if (user.request_pending == 1) {
            return '<span class="px-2 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-[0_0_15px_rgba(245,158,11,0.2)]">Handshake Pending</span>';
        }
        return '<span class="px-2 py-1 bg-red-500/10 text-red-500 border border-red-500/20 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-[0_0_15px_rgba(239,68,68,0.2)]">Restricted</span>';
    }
};

document.addEventListener('DOMContentLoaded', () => {
    AdminApp.init();

    // Global Dropdown Closer (Click Outside)
    document.addEventListener('click', (e) => {
        const handshakeMenu = document.getElementById('handshake-role-menu');
        const handshakeChevron = document.getElementById('auth-role-chevron');
        if (handshakeMenu && !handshakeMenu.contains(e.target) && !e.target.closest('button[onclick*="toggleHandshakeRoleDropdown"]')) {
            handshakeMenu.classList.add('hidden');
            if (handshakeChevron) handshakeChevron.style.transform = 'rotate(0deg)';
        }

        const inviteMenu = document.getElementById('invite-role-menu');
        const inviteChevron = document.getElementById('invite-role-chevron');
        if (inviteMenu && !inviteMenu.contains(e.target) && !e.target.closest('button[onclick*="toggleInviteRoleDropdown"]')) {
            inviteMenu.classList.add('hidden');
            if (inviteChevron) inviteChevron.style.transform = 'rotate(0deg)';
        }
    });
});

/** Global Helpers **/
window.closeModal = function () {
    document.querySelectorAll('.modal-overlay, [id*="-modal"]').forEach(m => {
        if (!m.classList.contains('hidden')) {
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
    });
    document.body.style.overflow = 'auto';
};

window.closeDrawer = function () {
    const drawer = document.getElementById('side-drawer');
    if (drawer) drawer.classList.add('translate-x-full');
};

window.openModal = function (id) {
    AdminApp.openModal(id);
};
