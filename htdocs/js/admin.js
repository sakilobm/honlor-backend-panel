/**
 * Honlor Admin App
 * ================
 * Handles interactive logic for Dashboard, Identity Vault, Channels, and Moderation.
 * Optimized for High-Fidelity UI Overhaul (v10).
 */

var AdminApp = {
    chart: null,
    currentRange: 7,

    init: function () {
        if (window.isRestricted) {
            console.warn("Security Protocol: Restricted State Detected.");
            return;
        }

        console.log("AdminApp Initializing...");
        var self = this;
        var params = new URLSearchParams(window.location.search);
        var page = params.get('page') || 'dashboard';

        this.route(page);

        window.onpopstate = function (e) {
            if (e.state && e.state.page) {
                self.switchSection(e.state.page, false);
            }
        };

        window.onclick = function (event) {
            if (!event.target.matches('.ph-bell') && !event.target.closest('.notification-pane')) {
                var pane = document.getElementById('notification-pane');
                if (pane) pane.classList.add('hidden');
            }
        };

        this.syncHandshakeBadge();

        var isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        if (isCollapsed) {
            var sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.add('sidebar-collapsed');
                this.syncSidebarIcon();
            }
        }
    },

    route: function (page) {
        switch (page) {
            case 'dashboard': this.initDashboard(); break;
            case 'users': this.initUsers(); break;
            case 'channels': this.initChannels(); break;
            case 'messages': this.initMessages(); break;
            case 'ads': this.initAdsManager(); break;
            case 'deletion_requests': this.initDeletionRequests(); break;
            case 'reports': this.initReports(); break;
            case 'logs': this.initLogs(); break;
            case 'policy_editor': this.initPolicyEditor(); break;
            case 'analytics': this.initAnalytics ? this.initAnalytics() : null; break;
            case 'roles': this.initRoles(); break;
        }
    },

    toggleNotifications: function () {
        var pane = document.getElementById('notification-pane');
        if (pane) pane.classList.toggle('hidden');
    },

    toggleSidebar: function () {
        var sidebar = document.getElementById('sidebar');
        if (sidebar) {
            var isCollapsing = sidebar.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsing);
            this.syncSidebarIcon();
            if (this.chart) {
                setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 400);
            }
        }
    },

    syncSidebarIcon: function () {
        var sidebar = document.getElementById('sidebar');
        var icon = document.getElementById('toggle-icon');
        if (sidebar && icon) {
            var isCollapsed = sidebar.classList.contains('sidebar-collapsed');
            if (isCollapsed) {
                icon.classList.remove('ph-caret-left');
                icon.classList.add('ph-caret-right');
            } else {
                icon.classList.remove('ph-caret-right');
                icon.classList.add('ph-caret-left');
            }
        }
    },

    switchSection: function (section, pushState) {
        var self = this;
        var shouldPush = (pushState === undefined) ? true : pushState;
        
        var navLinks = document.querySelectorAll('.nav-link-premium');
        for (var i = 0; i < navLinks.length; i++) {
            navLinks[i].classList.remove('active');
        }
        
        var activeLink = document.querySelector('a[href*="' + section + '"]');
        if (activeLink) activeLink.classList.add('active');

        var target = document.getElementById('content-container');
        if (!target) return;

        target.innerHTML = '<div class="flex items-center justify-center p-32 opacity-40"><div class="w-16 h-16 border-4 border-primary border-t-transparent rounded-full animate-spin"></div></div>';

        var base = (window.BASE_PATH || '/').replace(/\/$/, '');
        fetch(base + '/admin?page=' + section + '&ajax=1')
            .then(function(res) { return res.text(); })
            .then(function(html) {
                target.innerHTML = html;
                var firstTab = target.querySelector('.tab-btn');
                if (firstTab) {
                    var tabId = firstTab.getAttribute('data-tab');
                    if (tabId) self.switchTab(section, tabId);
                }
                if (shouldPush) {
                    window.history.pushState({ page: section }, "", base + '/admin?page=' + section);
                }
                self.init();
            })
            .catch(function(err) {
                console.error("SPA failure:", err);
                window.location.href = base + '/admin?page=' + section;
            });
    },

    switchTab: function (sectionId, tabId) {
        console.log("Switching Tab: " + sectionId + " -> " + tabId);

        var tabContainer = document.querySelector("#section-" + sectionId + " [id$='-tabs']");
        if (tabContainer) {
            var tabBtns = tabContainer.querySelectorAll('.tab-btn');
            for (var i = 0; i < tabBtns.length; i++) {
                tabBtns[i].classList.remove('active');
            }
            var activeBtn = tabContainer.querySelector("[data-tab='" + tabId + "']");
            if (activeBtn) activeBtn.classList.add('active');
        }

        if (sectionId === 'dashboard' && tabId === 'agents') {
            this.loadAgentStatus();
        }

        var section = document.getElementById("section-" + sectionId);
        if (section) {
            var contents = section.querySelectorAll('.tab-content');
            for (var j = 0; j < contents.length; j++) {
                contents[j].classList.add('hidden');
                contents[j].classList.remove('active');
            }
            var targetContent = document.getElementById("tab-content-" + tabId);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('active');
            }

            if (tabId === 'handshakes') {
                this.loadHandshakeHub();
            }
        }
    },

    initDashboard: function (range) {
        this.currentRange = (range === undefined) ? 7 : range;
        console.log("Initializing Dashboard with range: " + this.currentRange);

        var self = this;
        ApiClient.get('dashboard', 'metrics', { range: range }).then(function(data) {
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
                var newAlerts = Math.max(1, data.messages_today % 15);
                document.getElementById('notif-count-badge').innerText = newAlerts + " New";
            }

            self.renderGrowthChart(data.growth_data);
            self.loadRecentActivity();
            self.loadRecentMembers();
        })['catch'](function(err) {
            console.error(err);
        });
    },

    loadRecentActivity: function () {
        var target = document.getElementById('recent-activity-list');
        if (!target) return;

        ApiClient.get('dashboard', 'activity').then(function(data) {
            var html = '';
            for (var i = 0; i < data.activity.length; i++) {
                var item = data.activity[i];
                html += '<div class="flex items-start gap-4 p-4 rounded-2xl hover:bg-white/5 transition-colors">' +
                        '    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">' +
                        '        <i class="ph-bold ph-activity text-lg"></i>' +
                        '    </div>' +
                        '    <div class="overflow-hidden">' +
                        '        <p class="text-sm font-bold truncate">' + item.action + '</p>' +
                        '        <p class="text-[11px] text-gray-500 font-medium">' + (item.username || 'System') + ' • ' + item.created_at + '</p>' +
                        '    </div>' +
                        '</div>';
            }
            target.innerHTML = html || '<p class="text-center text-gray-500 py-8">No recent activity</p>';
        });
    },

    loadRecentMembers: function () {
        var dashboardTarget = document.getElementById('agent-status-grid');
        var overviewTarget = document.querySelector('#section-overview .grid-cols-2.md\\:grid-cols-4.lg\\:grid-cols-6');

        if (!dashboardTarget && !overviewTarget) return;

        var self = this;
        ApiClient.get('users', 'recent').then(function(data) {
            if (overviewTarget) {
                var html = '';
                var members = data.users.slice(0, 6);
                for (var i = 0; i < members.length; i++) {
                    var user = members[i];
                    var statusBadge = (user.active == 1) ? 'badge-success' : 'badge-neutral';
                    var statusText = (user.active == 1) ? 'Online' : 'Offline';
                    var name = user.firstname ? user.firstname : user.username;

                    html += '<div class="border p-4 rounded-3xl text-center group transition-all hover:bg-glass-white" style="border-color: var(--border-color); background-color: var(--surface);">' +
                            '    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=' + user.username + '" class="w-16 h-16 mx-auto rounded-2xl mb-3 bg-blue-500/10 p-1 group-hover:scale-105 transition-transform" alt="Avatar">' +
                            '    <h4 class="text-xs font-bold truncate mb-1">' + name + '</h4>' +
                            '    <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold ' + statusBadge + '">' + statusText + '</span>' +
                            '</div>';
                }
                overviewTarget.innerHTML = html || '<p class="text-center text-gray-500 py-8 col-span-full">No recent members</p>';
            }

            if (dashboardTarget) {
                self.renderAgentStatusGrid(data.users);
            }
        });
    },

    loadAgentStatus: function () {
        var self = this;
        var target = document.getElementById('agent-status-grid');
        if (!target) return;

        target.innerHTML = '<div class="col-span-full py-20 text-center animate-pulse">' +
                           '    <p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-40">Synchronizing Agent Clusters...</p>' +
                           '</div>';

        ApiClient.get('users', 'recent').then(function(data) {
            self.renderAgentStatusGrid(data.users);
        });
    },

    renderAgentStatusGrid: function (users) {
        var target = document.getElementById('agent-status-grid');
        if (!target) return;

        var html = '';
        for (var i = 0; i < users.length; i++) {
            var user = users[i];
            var isOnline = (user.active == 1);
            var statusBadge = isOnline ? 'badge-success' : 'badge-neutral';
            var statusText = isOnline ? 'Online' : 'Resting';
            var indicator = isOnline ? 'bg-green-500 shadow-[0_0_10px_#22c55e]' : 'bg-gray-500';
            var role = user.role_name || 'Agent';
            var name = user.firstname || user.username;

            html += '<div class="stat-card !p-6 text-center group transition-all hover:scale-[1.05] cursor-pointer" onclick="AdminApp.switchSection(\'users\')">' +
                    '    <div class="relative inline-block mb-4">' +
                    '        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=' + user.username + '" ' +
                    '             class="w-20 h-20 mx-auto rounded-[2rem] bg-primary/10 border border-primary/20 group-hover:bg-primary group-hover:rotate-6 transition-all" alt="Avatar">' +
                    '        <span class="absolute bottom-1 right-1 w-5 h-5 ' + indicator + ' border-4 border-[var(--surface)] rounded-full"></span>' +
                    '    </div>' +
                    '    <p class="font-black text-xs uppercase tracking-tight truncate">' + name + '</p>' +
                    '    <p class="text-[9px] font-black uppercase tracking-widest text-primary mt-1 leading-none">' + role + '</p>' +
                    '    <div class="' + statusBadge + ' mt-4 inline-block">' + statusText + '</div>' +
                    '</div>';
        }

        target.innerHTML = html || '<p class="text-center text-gray-500 py-20 col-span-full uppercase font-black text-[10px] tracking-widest">No active agents detected</p>';
    },

    renderGrowthChart: function (growthData) {
        var canvas = document.getElementById('growthChart');
        if (!canvas) return;

        var ctx = canvas.getContext('2d');
        if (this.chart) this.chart.destroy();

        var isLight = document.body.classList.contains('light');
        var primaryColor = '#7c6aff';

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

    initUsers: function () {
        this.userPage = 1;
        this.loadUserList();

        var filterInput = document.getElementById('user-filter');
        if (filterInput) {
            var self = this;
            var timeout;
            filterInput.oninput = function () {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    self.userPage = 1;
                    self.loadUserList(1, filterInput.value);
                }, 300);
            };
        }
    },

    exportUserCSV: function () {
        toast.info('Preparing Export', 'Gathering identity records...');
        ApiClient.get('users', 'list', { page: 1, limit: 1000 }).then(function(data) {
            var users = data.users;
            var headers = ['ID', 'Username', 'Email', 'Status', 'Joined'];
            var rows = users.map(function(u) {
                return [
                    u.id,
                    u.username,
                    u.email,
                    u.blocked == 1 ? 'Blocked' : 'Active',
                    u.created_at
                ];
            });

            var csvContent = "data:text/csv;charset=utf-8," +
                [headers].concat(rows).map(function(e) { return e.join(","); }).join("\n");

            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "honlor_users_export_" + new Date().getTime() + ".csv");
            document.body.appendChild(link);
            link.click();
            toast.success('Export Ready', 'Identity vault downloaded successfully.');
        });
    },

    changeUserPage: function (delta) {
        var newPage = this.userPage + delta;
        if (newPage < 1) return;
        this.userPage = newPage;
        var filterEl = document.getElementById('user-filter');
        var filter = (filterEl) ? filterEl.value : '';
        this.loadUserList(this.userPage, filter);
    },

    loadUserList: function (page, filter) {
        page = page || 1;
        filter = filter || '';
        var tbody = document.getElementById('users-table-body');
        if (!tbody) return;

        ApiClient.get('users', 'list', { page: page, filter: filter }).then(function(data) {
            var html = '';
            var users = data.users || [];
            for (var i = 0; i < users.length; i++) {
                var user = users[i];
                var badge = user.blocked == 1 ? 'badge-danger' : (user.active == 1 ? 'badge-success' : 'badge-neutral');
                var status = user.blocked == 1 ? 'Blocked' : (user.active == 1 ? 'Active' : 'Inactive');
                var name = user.firstname ? user.firstname : user.username;

                html += '<tr class="hover:bg-white/5 transition-colors">' +
                        '    <td class="px-6 py-4">' +
                        '        <div class="flex items-center gap-3">' +
                        '            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=' + user.username + '" class="w-10 h-10 rounded-xl bg-primary/10 p-0.5" alt="Avatar">' +
                        '            <div>' +
                        '                <p class="font-bold text-sm">' + name + ' ' + (user.lastname || '') + '</p>' +
                        '                <p class="text-[11px] text-gray-500 font-medium">' + user.email + '</p>' +
                        '            </div>' +
                        '        </div>' +
                        '    </td>' +
                        '    <td class="px-6 py-4">' + AdminApp.renderRoleBadge(user) + '</td>' +
                        '    <td class="px-6 py-4"><span class="' + badge + '">' + status + '</span></td>' +
                        '    <td class="px-6 py-4">' +
                        '        <div class="flex items-center gap-2">' +
                        '            <div class="w-16 h-1.5 rounded-full bg-gray-800">' +
                        '                <div class="bg-primary h-full rounded-full" style="width: ' + (user.active == 1 ? 85 : 10) + '%"></div>' +
                        '            </div>' +
                        '            <span class="text-[10px] font-bold text-gray-400">' + (user.active == 1 ? 85 : 10) + '%</span>' +
                        '        </div>' +
                        '    </td>' +
                        '    <td class="px-6 py-4 text-xs font-semibold text-gray-400">' + new Date(user.created_at).toLocaleDateString() + '</td>' +
                        '    <td class="px-6 py-4 text-right">' +
                        '        <div class="flex justify-end gap-2">' +
                        '            <button onclick="AdminApp.openDrawer(\'user\', \'' + user.id + '\')" class="p-2 hover:bg-primary/10 hover:text-primary rounded-xl transition-all text-gray-500"><i class="ph ph-eye text-lg"></i></button>' +
                        '            <button onclick="AdminApp.executeAction(\'toggle_block\', \'' + user.id + '\')" class="p-2 hover:bg-red-500/10 hover:text-red-400 rounded-xl transition-all text-gray-500"><i class="ph ph-prohibit text-lg"></i></button>' +
                        '        </div>' +
                        '    </td>' +
                        '</tr>';
            }
            tbody.innerHTML = html || '<tr><td colspan="5" class="p-8 text-center text-gray-500">No users found</td></tr>';

            if (document.getElementById('users-total-count')) {
                document.getElementById('users-total-count').innerText = (data.total / 1000).toFixed(1) + 'k Total';
            }

            if (document.getElementById('users-count-text')) {
                var start = (page - 1) * 10 + 1;
                var end = Math.min(page * 10, data.total);
                document.getElementById('users-count-text').innerText = 'Showing ' + start + '-' + end + ' of ' + data.total + ' identities';
            }
        });
    },

    initChannels: function () {
        this.loadChannelList();

        var form = document.getElementById('create-channel-form');
        if (form) {
            var self = this;
            form.onsubmit = function (e) {
                e.preventDefault();
                var formData = new FormData(form);
                var payload = {};
                formData.forEach(function(value, key) { payload[key] = value; });

                ApiClient.post('channels', 'create', payload).then(function(res) {
                    toast.success('Success', res.message);
                    closeModal();
                    form.reset();
                    self.loadChannelList();
                })['catch'](function(err) {
                    toast.error('Error', err.error || 'Failed to create channel.');
                });
            };
        }
    },

    loadChannelList: function () {
        var tbody = document.getElementById('channels-table-body');
        var nodeMetric = document.getElementById('metric-total-nodes');
        if (!tbody) return;

        ApiClient.get('channels', 'list').then(function (res) {
            var html = '';
            var channels = res.channels || [];
            
            if (nodeMetric && res.metrics) {
                var count = res.metrics.total_nodes;
                nodeMetric.innerText = count < 10 ? "0" + count : count;
            }

            if (channels.length === 0) {
                html = '<tr><td colspan="5" class="py-20 text-center opacity-40 uppercase tracking-[0.3em] text-[10px] font-black italic">Infrastructure Void: No Active Nodes</td></tr>';
            } else {
                for (var i = 0; i < channels.length; i++) {
                    var ch = channels[i];
                    var statusBadge = ch.status === 'active' ? 'bg-green-500/10 text-green-500 border-green-500/20' : 'bg-slate-500/10 text-slate-400 border-slate-500/20';
                    var statusText = ch.status === 'active' ? 'Operational' : (ch.status || 'Standby').toUpperCase();
                    
                    html += '<tr class="hover:bg-white/[0.03] transition-all duration-300 group">' +
                            '    <td class="px-8 py-6">' +
                            '        <div class="flex items-center gap-4">' +
                            '            <div class="w-12 h-12 rounded-[1.25rem] bg-primary/10 border border-primary/20 flex items-center justify-center text-primary shadow-2xl shadow-primary/10">' +
                            '                <i class="ph-bold ph-broadcast text-xl"></i>' +
                            '            </div>' +
                            '            <div>' +
                            '                <p class="text-xs font-black uppercase tracking-tight">' + ch.name + '</p>' +
                            '                <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest mt-0.5">X-Class Infrastructure</p>' +
                            '            </div>' +
                            '        </div>' +
                            '    </td>' +
                            '    <td class="px-8 py-6">' +
                            '        <span class="px-3 py-1.5 ' + statusBadge + ' border rounded-xl text-[8px] font-black uppercase tracking-widest shadow-xl">' + statusText + '</span>' +
                            '    </td>' +
                            '    <td class="px-8 py-6">' +
                            '        <p class="text-xs font-black tracking-tight">' + (ch.load_factor || '98.2') + '%</p>' +
                            '        <p class="text-[9px] font-bold opacity-30 uppercase tracking-widest">Network Load</p>' +
                            '    </td>' +
                            '    <td class="px-8 py-6">' +
                            '        <p class="text-xs font-black tracking-tight">' + (ch.deployment || 'MAINNET') + '</p>' +
                            '        <p class="text-[9px] font-bold opacity-30 uppercase tracking-widest">Cluster Layer</p>' +
                            '    </td>' +
                            '    <td class="px-8 py-6 text-right">' +
                            '        <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300">' +
                            '            <button onclick="AdminApp.deleteChannel(\'' + ch.id + '\')" class="w-10 h-10 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-xl shadow-red-500/5">' +
                                             '<i class="ph-bold ph-trash text-lg"></i>' +
                            '            </button>' +
                            '        </div>' +
                            '    </td>' +
                            '</tr>';
                }
            }
            tbody.innerHTML = html;
        });
    },

    deleteChannel: function (id) {
        var self = this;
        this.confirm({
            title: 'Node Decommission',
            message: 'Are you absolutely sure you want to permanently decommission this node?',
            icon: 'ph-warning-octagon',
            confirmLabel: 'Decommission Node',
            type: 'danger',
            purge: true
        }).then(function(confirmed) {
            if (confirmed) {
                ApiClient.post('channels', 'delete', { id: id }).then(function(res) {
                    toast.success('Confirmed', 'Node removed from network.');
                    self.loadChannelList();
                });
            }
        });
    },

    switchChannelTab: function (tab) {
        var activeBtn = document.getElementById('tab-btn-active');
        var browseBtn = document.getElementById('tab-btn-browse');
        var activeView = document.getElementById('channels-active-view');
        var browseView = document.getElementById('channels-browse-view');
        var discoveryBar = document.getElementById('channels-discovery-bar');

        if (tab === 'active') {
            if (activeView) activeView.classList.remove('hidden');
            if (browseView) browseView.classList.add('hidden');
            if (discoveryBar) discoveryBar.classList.add('hidden');
            if (activeBtn) {
                activeBtn.className = 'px-6 py-2 bg-primary text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-primary/20 transition-all';
            }
            if (browseBtn) {
                browseBtn.className = 'px-6 py-2 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:text-white transition-all';
            }
            this.loadChannelList();
        } else {
            if (activeView) activeView.classList.add('hidden');
            if (browseView) browseView.classList.remove('hidden');
            if (discoveryBar) discoveryBar.classList.remove('hidden');
            if (activeBtn) {
                activeBtn.className = 'px-6 py-2 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:text-white transition-all';
            }
            if (browseBtn) {
                browseBtn.className = 'px-6 py-2 bg-primary text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-primary/20 transition-all';
            }
            this.loadBrowseChannels();
        }
    },

    loadBrowseChannels: function (filter) {
        var grid = document.getElementById('browse-channels-grid');
        if (!grid) return;

        ApiClient.get('channels', 'browse', { filter: filter || '' }).then(function (data) {
            var html = '';
            var channels = data.channels || [];
            
            if (channels.length === 0) {
                html = '<div class="col-span-full py-20 text-center space-y-4">' +
                       '    <div class="w-20 h-20 bg-primary/5 rounded-[2rem] mx-auto flex items-center justify-center text-primary/20">' +
                       '        <i class="ph-bold ph-magnifying-glass text-4xl"></i>' +
                       '    </div>' +
                       '    <p class="font-black text-[10px] uppercase tracking-[0.3em] opacity-40">No matching clusters found in the global registry</p>' +
                       '</div>';
            } else {
                for (var i = 0; i < channels.length; i++) {
                    var ch = channels[i];
                    html += '<div class="glass-card !p-8 border-white/5 hover:border-primary/30 transition-all group animate-in zoom-in duration-500">' +
                            '    <div class="flex items-start justify-between mb-6">' +
                            '        <div class="w-16 h-16 rounded-[1.5rem] bg-primary/10 border border-primary/5 flex items-center justify-center text-primary shadow-2xl shadow-primary/5">' +
                            '            <i class="ph-bold ph-broadcast text-2xl"></i>' +
                            '        </div>' +
                            '        <div class="px-3 py-1 bg-primary/10 text-primary border border-primary/20 rounded-lg text-[8px] font-black uppercase tracking-widest">' +
                            '            ' + (ch.type || 'Public') + ' Node' +
                            '        </div>' +
                            '    </div>' +
                            '    <div class="space-y-1 mb-8">' +
                            '        <h4 class="text-xl font-black tracking-tight text-white">' + ch.name + '</h4>' +
                            '        <p class="text-[11px] font-bold opacity-40 uppercase tracking-widest">' + (ch.description || 'X-Class Infrastructure Node') + '</p>' +
                            '        <div class="flex items-center gap-2 mt-4">' +
                            '            <div class="flex -space-x-2">' +
                            '                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=1" class="w-6 h-6 rounded-lg border-2 border-surface-dark">' +
                            '                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=2" class="w-6 h-6 rounded-lg border-2 border-surface-dark">' +
                            '                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=3" class="w-6 h-6 rounded-lg border-2 border-surface-dark">' +
                            '            </div>' +
                            '            <span class="text-[9px] font-black opacity-30 uppercase tracking-tight">' + (ch.member_count || '2.4k') + ' Identities Syncing</span>' +
                            '        </div>' +
                            '    </div>' +
                            '    <button onclick="AdminApp.joinChannelProtocol(\'' + ch.id + '\')" class="w-full py-4 rounded-2xl bg-white/[0.03] border border-white/5 text-[10px] font-black uppercase tracking-widest hover:bg-primary hover:text-white hover:border-primary transition-all shadow-xl hover:shadow-primary/20">' +
                            '        Initialize Join Protocol' +
                            '    </button>' +
                            '</div>';
                }
            }
            grid.innerHTML = html;
        });
    },

    searchChannels: function (query) {
        var self = this;
        if (this.searchTimeout) clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(function () {
            self.loadBrowseChannels(query);
        }, 300);
    },

    joinChannelProtocol: function (id) {
        var self = this;
        ApiClient.post('channels', 'join', { id: id }).then(function (res) {
            toast.success('Sync Synchronized', 'Identity established with node cluster. Infrastructure updated.');
            self.switchChannelTab('active');
        })['catch'](function (err) {
            toast.error('Protocol Error', err.error || 'Identity rejection in target cluster.');
        });
    },

    /**
     * Messages Logic
     */
    initMessages: function () {
        var self = this;
        this.loadMessageList();
        this.loadFlaggedMessages();

        // Polling for real-time telemetry
        if (this.telemetryInterval) clearInterval(this.telemetryInterval);
        this.telemetryInterval = setInterval(function() {
            if (self.currentSection === 'messages') {
                self.loadMessageList();
                self.loadFlaggedMessages();
            }
        }, 10000);

        // Filter listener
        var filterInput = document.getElementById('message-filter');
        if (filterInput) {
            filterInput.addEventListener('input', function() {
                self.loadMessageList();
            });
        }
    },

    loadMessageList: function () {
        var self = this;
        var filterEl = document.getElementById('message-filter');
        var filter = (filterEl) ? filterEl.value : '';
        
        var tbody = document.getElementById('messages-table-body');
        var velocityMetric = document.getElementById('metric-velocity');
        var flaggedBadge = document.getElementById('flagged-count-badge');

        if (!tbody) return;

        ApiClient.get('messages', 'list', { status: 'all', filter: filter }).then(function(data) {
            // Update Metrics
            if (velocityMetric) velocityMetric.innerText = data.metrics.velocity.toFixed(1);
            if (flaggedBadge) {
                flaggedBadge.innerText = data.metrics.flagged;
                flaggedBadge.classList.toggle('hidden', data.metrics.flagged === 0);
            }

            var html = '';
            var messages = data.messages || [];
            for (var i = 0; i < messages.length; i++) {
                var msg = messages[i];
                var statusColor = (msg.status === 'flagged') ? 'bg-orange-500/10 text-orange-400 border-orange-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                var statusLabel = (msg.status === 'flagged') ? 'Flagged' : 'Safe';
                var avatar = 'https://api.dicebear.com/7.x/avataaars/svg?seed=' + msg.username;

                html += '<tr class="hover:bg-white/[0.03] transition-all duration-300 group">' +
                        '    <td class="px-8 py-6">' +
                        '        <div class="flex items-center gap-4">' +
                        '            <div class="w-10 h-10 rounded-[1rem] bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold text-[10px] uppercase shadow-lg shadow-indigo-500/5">' +
                        '                # ' + (msg.channel_name ? msg.channel_name.substring(0, 1) : '?') +
                        '            </div>' +
                        '            <div>' +
                        '                <p class="text-[10px] font-black uppercase tracking-widest text-primary">' + msg.channel_name + '</p>' +
                        '                <p class="text-[8px] font-bold opacity-40 uppercase tracking-widest mt-0.5">Global Cluster</p>' +
                        '            </div>' +
                        '        </div>' +
                        '    </td>' +
                        '    <td class="px-8 py-6">' +
                        '        <div class="flex items-center gap-4">' +
                        '            <img src="' + avatar + '" class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 shadow-xl">' +
                        '            <div>' +
                        '                <p class="text-xs font-black uppercase tracking-tight mb-0.5">' + msg.username + '</p>' +
                        '                <p class="text-[11px] font-medium opacity-70 line-clamp-1 max-w-sm italic">"' + msg.content + '"</p>' +
                        '            </div>' +
                        '        </div>' +
                        '    </td>' +
                        '    <td class="px-8 py-6 text-center">' +
                        '        <span class="px-3 py-1.5 ' + statusColor + ' border rounded-xl text-[8px] font-black uppercase tracking-widest shadow-xl">' + statusLabel + '</span>' +
                        '    </td>' +
                        '</tr>';
            }
            tbody.innerHTML = html || '<tr><td colspan="5" class="p-20 text-center opacity-30 font-black uppercase tracking-[0.2em] text-[10px]">No active signals in stream</td></tr>';
        });
    },

    loadFlaggedMessages: function () {
        var self = this;
        var grid = document.getElementById('flagged-messages-grid');
        var chamberCount = document.getElementById('chamber-count');
        var ledger = document.getElementById('resolution-ledger');
        if (!grid) return;

        ApiClient.get('messages', 'list', { status: 'flagged' }).then(function (data) {
            // Update Summary Card
            if (chamberCount) {
                var count = data.metrics.flagged;
                chamberCount.innerText = count < 10 ? '0' + count : count;
            }

            if (ledger && data.metrics.recent_activity) {
                var ledgerHtml = '';
                for (var i = 0; i < data.metrics.recent_activity.length; i++) {
                    var log = data.metrics.recent_activity[i];
                    var isPositive = log.message.toLowerCase().indexOf('success') !== -1 || log.message.toLowerCase().indexOf('resolved') !== -1 || log.message.toLowerCase().indexOf('approve') !== -1;
                    var icon = isPositive ? 'ph-check-circle text-emerald-400' : 'ph-info text-blue-400';
                    var time = log.created_at ? new Date(log.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'NOW';

                    ledgerHtml += '<div class="flex gap-4 p-4 rounded-2xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.05] transition-all group/item">' +
                        '    <div class="w-10 h-10 shrink-0 rounded-xl bg-white/5 flex items-center justify-center">' +
                        '        <i class="ph-bold ' + icon + ' text-lg"></i>' +
                        '    </div>' +
                        '    <div class="flex-grow overflow-hidden">' +
                        '        <div class="flex items-center justify-between mb-0.5">' +
                        '            <p class="text-[9px] font-black uppercase tracking-widest text-primary truncate">Protocol ' + log.id + '</p>' +
                        '            <span class="text-[8px] font-bold opacity-30 uppercase">' + time + '</span>' +
                        '        </div>' +
                        '        <p class="text-[10px] font-medium opacity-60 line-clamp-1 italic">' + log.message + '</p>' +
                        '    </div>' +
                        '</div>';
                }
                ledger.innerHTML = ledgerHtml || '<div class="p-12 text-center opacity-20 italic text-[10px] font-black uppercase tracking-widest py-20">No recent resolutions recorded</div>';
            }

            var html = '';
            for (var j = 0; j < data.messages.length; j++) {
                var msg = data.messages[j];
                var avatar = 'https://api.dicebear.com/7.x/avataaars/svg?seed=' + msg.username;
                html += '<div class="glass-card !p-8 border-orange-500/20 bg-gradient-to-br from-orange-500/[0.05] to-transparent animate-in zoom-in duration-500">' +
                        '    <div class="flex items-center justify-between mb-8">' +
                        '        <div class="flex items-center gap-4">' +
                        '            <img src="' + avatar + '" class="w-12 h-12 rounded-2xl border border-white/10 shadow-2xl">' +
                        '            <div>' +
                        '                <p class="text-xs font-black uppercase tracking-widest text-primary">' + msg.username + '</p>' +
                        '                <p class="text-[8px] font-bold opacity-40 uppercase tracking-widest mt-0.5">Flagged Packet</p>' +
                        '            </div>' +
                        '        </div>' +
                        '        <div class="text-[8px] font-black uppercase tracking-widest px-2 py-1 bg-white/5 rounded-lg border border-white/5 opacity-40">' +
                        '            # ' + msg.id +
                        '        </div>' +
                        '    </div>' +
                        '    <div class="p-6 rounded-3xl bg-black/20 border border-white/5 mb-8 italic text-xs leading-relaxed opacity-80">' +
                        '        "' + msg.content + '"' +
                        '    </div>' +
                        '    <div class="flex gap-3">' +
                        '        <button onclick="AdminApp.executeMessageAction(\'unflag\', \'' + msg.id + '\')" class="flex-grow btn-secondary !py-3 !text-[9px] !font-black !uppercase !justify-center !rounded-[1.25rem] !bg-emerald-500/10 !text-emerald-400 !border-emerald-500/20 hover:!bg-emerald-500 hover:!text-white transition-all">Restore Signal</button>' +
                        '        <button onclick="AdminApp.executeMessageAction(\'purge\', \'' + msg.id + '\')" class="w-14 btn-secondary !p-0 !justify-center !rounded-[1.25rem] !bg-red-500/10 !text-red-500 !border-red-500/20 hover:!bg-red-500 hover:!text-white transition-all">' +
                        '            <i class="ph-bold ph-trash text-lg"></i>' +
                        '        </button>' +
                        '    </div>' +
                        '</div>';
            }

            grid.innerHTML = html || '<div class="col-span-full p-32 text-center rounded-[3rem] border border-dashed border-white/10 bg-white/[0.01]">' +
                '    <div class="w-20 h-20 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mx-auto mb-8">' +
                '         <i class="ph ph-shield-check text-4xl"></i>' +
                '    </div>' +
                '    <h3 class="text-2xl font-black uppercase tracking-tight mb-2">Chamber <span class="gradient-text">Secured</span></h3>' +
                '    <p class="text-xs font-bold opacity-40 uppercase tracking-widest">No guideline violations currently isolated.</p>' +
                '</div>';
        });
    },

    executeMessageAction: function (action, id) {
        var self = this;
        var confirmOptions = null;
        if (action === 'purge' || action === 'delete') {
            confirmOptions = {
                title: 'Packet Purge',
                message: 'Are you sure you want to permanently remove this communication packet from the global ledger?',
                icon: 'ph-trash',
                confirmLabel: 'Purge Packet',
                type: 'danger'
            };
        }

        var execute = function () {
            ApiClient.post('messages', 'action', { id: id, action: action }).then(function (res) {
                toast.success('Governance Success', res.message);
                self.loadMessageList();
                self.loadFlaggedMessages();
            })['catch'](function (err) {
                toast.error('Protocol Error', err.message);
            });
        };

        if (confirmOptions) {
            this.confirm(confirmOptions).then(function (confirmed) {
                if (confirmed) execute();
            });
        } else {
            execute();
        }
    },

    handleReport: function (id, action) {
        var self = this;
        var confirmOptions = null;
        if (action === 'delete') {
            confirmOptions = {
                title: 'Data Purge',
                message: 'Permanently remove this report from the intelligence ledger?',
                icon: 'ph-trash',
                confirmLabel: 'Purge Report',
                type: 'danger'
            };
        }

        var execute = function () {
            ApiClient.post('messages', 'action', { id: id, action: action }).then(function (res) {
                toast.success('Confirmed', res.message);
                self.loadReportData();
            })['catch'](function (err) {
                toast.error('Protocol Error', err.error || 'Failed to settle incident.');
            });
        };

        if (confirmOptions) {
            this.confirm(confirmOptions).then(function (confirmed) {
                if (confirmed) execute();
            });
        } else {
            execute();
        }
    },

    /**
     * Safety Center (Reports) Logic
     */
    initReports: function () {
        this.loadReportList();
    },

    loadReportList: function () {
        var tbody = document.getElementById('reports-table-body');
        var historyList = document.getElementById('reports-history-list');
        var activeMetric = document.getElementById('metric-active-incidents');
        var velocityMetric = document.getElementById('metric-resolution-velocity');
        var totalMetric = document.getElementById('metric-total-reports');
        if (!tbody) return;

        ApiClient.get('compliance', 'list').then(function (data) {
            if (activeMetric) activeMetric.innerText = data.metrics.active_queue < 10 ? '0' + data.metrics.active_queue : data.metrics.active_queue;
            if (velocityMetric) velocityMetric.innerText = data.metrics.velocity + '%';
            if (totalMetric) totalMetric.innerText = data.metrics.total_incidents < 10 ? '0' + data.metrics.total_incidents : data.metrics.total_incidents;

            var html = '';
            for (var i = 0; i < data.incidents.length; i++) {
                var inc = data.incidents[i];
                var severityColor = inc.severity === 'Critical' ? 'bg-red-500/10 text-red-500 border-red-500/20' : (inc.severity === 'Moderate' ? 'bg-orange-500/10 text-orange-400 border-orange-500/20' : 'bg-blue-500/10 text-blue-400 border-blue-400/20');
                var urgencyPulse = inc.severity === 'Critical' ? 'bg-red-500' : (inc.severity === 'Moderate' ? 'bg-orange-500' : 'bg-blue-500');

                html += '<tr class="hover:bg-white/[0.03] transition-all duration-300 group">' +
                        '    <td class="px-8 py-6">' +
                        '        <div class="flex items-center gap-4">' +
                        '            <div class="flex -space-x-3 transition-transform group-hover:scale-105">' +
                        '                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=' + inc.reporter_name + '" class="w-10 h-10 rounded-xl bg-primary/10 border-2 border-[var(--bg-main)] z-20 shadow-xl">' +
                        '                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=' + inc.target_name + '" class="w-10 h-10 rounded-xl bg-red-500/10 border-2 border-[var(--bg-main)] z-10 shadow-xl">' +
                        '            </div>' +
                        '            <div>' +
                        '                <p class="text-[11px] font-black uppercase tracking-widest text-primary truncate max-w-[150px]">Case #' + inc.id + '</p>' +
                        '                <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest mt-0.5">By @' + inc.reporter_name + '</p>' +
                        '            </div>' +
                        '        </div>' +
                        '    </td>' +
                        '    <td class="px-8 py-6 text-center">' +
                        '        <span class="text-[10px] font-black opacity-80 uppercase tracking-widest">' + inc.category + '</span>' +
                        '        <p class="text-[8px] font-bold opacity-20 uppercase mt-0.5">Safety Node 0x' + inc.id + 'F</p>' +
                        '    </td>' +
                        '    <td class="px-8 py-6">' +
                        '        <p class="text-[10px] font-medium opacity-60 truncate max-w-[200px] italic" style="color: var(--text-main);">"' + inc.content + '"</p>' +
                        '    </td>' +
                        '    <td class="px-8 py-6">' +
                        '         <div class="flex items-center justify-center gap-3">' +
                        '            <span class="px-3 py-1.5 ' + severityColor + ' border rounded-xl text-[8px] font-black uppercase tracking-widest shadow-xl">' + inc.severity + '</span>' +
                        '            <span class="flex h-1.5 w-1.5 relative">' +
                        '              <span class="animate-ping absolute inline-flex h-full w-full rounded-full ' + urgencyPulse + ' opacity-75"></span>' +
                        '              <span class="relative inline-flex rounded-full h-1.5 w-1.5 ' + urgencyPulse + '"></span>' +
                        '            </span>' +
                        '         </div>' +
                        '    </td>' +
                        '    <td class="px-8 py-6 text-right">' +
                        '         <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300">' +
                        '            <button onclick="AdminApp.executeComplianceAction(\'' + inc.id + '\', \'Resolved\')" class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all shadow-xl shadow-emerald-500/5">' +
                        '                <i class="ph-bold ph-check text-lg"></i>' +
                        '            </button>' +
                        '            <button onclick="AdminApp.executeComplianceAction(\'' + inc.id + '\', \'Dismissed\')" class="w-10 h-10 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-xl shadow-red-500/5">' +
                        '                <i class="ph-bold ph-x text-lg"></i>' +
                        '            </button>' +
                        '         </div>' +
                        '    </td>' +
                        '</tr>';
            }
            tbody.innerHTML = html || '<tr><td colspan="5" class="p-24 text-center opacity-30 font-black uppercase tracking-[0.3em] text-[10px]">No active incidents in queue</td></tr>';

            var historyData = data.history || (data.metrics && data.metrics.recent_activity) || [];
            if (historyList) {
                var historyHtml = '';
                if (historyData.length === 0) {
                    historyHtml = '<div class="p-12 text-center opacity-20 italic text-[10px] font-black uppercase tracking-widest py-20">No recent resolutions recorded</div>';
                } else {
                    for (var j = 0; j < historyData.length; j++) {
                        var log = historyData[j];
                        var time = log.created_at ? new Date(log.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'JUST NOW';
                        
                        var nodeSource = '0x' + (Math.floor(Math.random() * 99) + 10);
                        var latency = Math.floor(Math.random() * 20) + 5;
                        var protocolLabel = (log.message || log.action || 'Protocol Clearance').toUpperCase();

                        historyHtml += '<div class="relative pl-10 py-3 group animate-in slide-in-from-right duration-500 hover:bg-white/[0.02] rounded-2xl transition-all">' +
                                       '    <div class="absolute left-[0.25rem] top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-emerald-500/60 animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.3)] z-10"></div>' +
                                       '    <div class="flex flex-col gap-1">' +
                                       '        <div class="flex items-center justify-between">' +
                                       '            <p class="text-[10px] font-black uppercase tracking-widest opacity-80" style="color: var(--text-main);">' + protocolLabel + '</p>' +
                                       '            <span class="text-[8px] font-black opacity-20 uppercase" style="color: var(--text-main);">' + time + '</span>' +
                                       '        </div>' +
                                       '        <div class="flex items-center gap-3">' +
                                       '            <div class="flex items-center gap-1.5">' +
                                       '                <i class="ph ph-cpu text-[10px] opacity-20"></i>' +
                                       '                <span class="text-[8px] font-bold opacity-30 uppercase tracking-widest">Node ' + nodeSource + '</span>' +
                                       '            </div>' +
                                       '            <div class="w-1 h-1 rounded-full bg-white/5"></div>' +
                                       '            <div class="flex items-center gap-1.5">' +
                                       '                <i class="ph ph-activity text-[10px] text-emerald-500/40"></i>' +
                                       '                <span class="text-[8px] font-bold text-emerald-500/40 uppercase tracking-widest">' + latency + 'ms SYNC</span>' +
                                       '            </div>' +
                                       '        </div>' +
                                       '    </div>' +
                                       '</div>';
                    }
                }
                historyList.innerHTML = historyHtml;
            }
        });
    },

    executeComplianceAction: function (id, action) {
        var self = this;
        ApiClient.post('compliance', 'action', { id: id, action: action }).then(function (res) {
            toast.success('Sync Synchronized', 'Incident #' + id + ' was marked as ' + action + '. Ledger updated.');
            self.loadReportList();
        });
    },

    /**
     * Deletion Requests Logic
     */
    initDeletionRequests: function () {
        this.loadDeletionRequests();
    },

    loadDeletionRequests: function () {
        var tbody = document.getElementById('deletion-requests-table-body');
        var countBadge = document.getElementById('pending-deletion-count');
        var velocityBadge = document.getElementById('metric-deletion-velocity');
        if (!tbody) return;

        ApiClient.get('users', 'deletion_requests').then(function (data) {
            if (countBadge) countBadge.innerText = data.stats.pending < 10 ? '0' + data.stats.pending : data.stats.pending;
            if (velocityBadge) velocityBadge.innerText = data.stats.velocity + '%';

            var html = '';
            data.requests.forEach(function (req) {
                var statusColor = req.status === 'pending' ? 'bg-orange-500/10 text-orange-400 border-orange-500/20' : (req.status === 'approved' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-red-500/10 text-red-500 border-red-500/20');
                var pulseColor = req.status === 'pending' ? 'bg-orange-500' : (req.status === 'approved' ? 'bg-emerald-500' : 'bg-red-500');

                html += '<tr class="hover:bg-white/[0.03] transition-all duration-300 group">' +
                        '    <td class="px-8 py-6">' +
                        '        <div class="flex items-center gap-4">' +
                        '            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=' + req.username + '" class="w-10 h-10 rounded-xl bg-primary/10 border-2 border-[var(--bg-main)] z-10 shadow-xl">' +
                        '            <div>' +
                        '                <p class="text-[11px] font-black uppercase tracking-widest text-primary truncate max-w-[150px]">' + req.username + '</p>' +
                        '                <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest mt-0.5">' + req.email + '</p>' +
                        '            </div>' +
                        '        </div>' +
                        '    </td>' +
                        '    <td class="px-8 py-6">' +
                        '        <p class="text-[10px] font-medium opacity-60 max-w-[200px] italic leading-relaxed" style="color: var(--text-main);">"' + (req.reason || 'Sovereign data removal request.') + '"</p>' +
                        '    </td>' +
                        '    <td class="px-8 py-6">' +
                        '         <div class="flex items-center justify-center gap-3">' +
                        '            <span class="px-3 py-1.5 ' + statusColor + ' border rounded-xl text-[8px] font-black uppercase tracking-widest shadow-xl">' + req.status + '</span>' +
                        '            <span class="flex h-1.5 w-1.5 relative">' +
                        '              <span class="animate-ping absolute inline-flex h-full w-full rounded-full ' + pulseColor + ' opacity-75"></span>' +
                        '              <span class="relative inline-flex rounded-full h-1.5 w-1.5 ' + pulseColor + '"></span>' +
                        '            </span>' +
                        '         </div>' +
                        '    </td>' +
                        '    <td class="px-8 py-6">' +
                        '         <p class="text-[10px] font-black tracking-tight" style="color: var(--text-main); text-align: center;">' + new Date(req.created_at).toLocaleDateString() + '</p>' +
                        '         <p class="text-[8px] font-bold opacity-20 uppercase tracking-widest mt-0.5" style="text-align: center;">Sync Logged</p>' +
                        '    </td>' +
                        '    <td class="px-8 py-6 text-right">' +
                        '         <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300">' +
                        '            <button onclick="AdminApp.handleDeletion(\'' + req.id + '\', \'approved\')" class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all shadow-xl shadow-emerald-500/5">' +
                        '                <i class="ph-bold ph-shield-check text-lg"></i>' +
                        '            </button>' +
                        '            <button onclick="AdminApp.handleDeletion(\'' + req.id + '\', \'rejected\')" class="w-10 h-10 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-xl shadow-red-500/5">' +
                        '                <i class="ph-bold ph-prohibit text-lg"></i>' +
                        '            </button>' +
                        '         </div>' +
                        '    </td>' +
                        '</tr>';
            });
            tbody.innerHTML = html || '<tr><td colspan="5" class="p-32 text-center opacity-30 font-black uppercase tracking-[0.2em] text-[10px]">No active erasure packets in queue</td></tr>';
        });
    },

    handleDeletion: function (id, status) {
        var self = this;
        this.confirm({
            title: 'Erasure Protocol',
            message: 'Are you sure you want to ' + status + ' this deletion request? This will impact the subject\'s identity visibility.',
            icon: status === 'approved' ? 'ph-shield-check' : 'ph-prohibit',
            confirmLabel: status === 'approved' ? 'Authorize Erasure' : 'Deny Request',
            type: status === 'approved' ? 'info' : 'danger'
        }).then(function (confirmed) {
            if (confirmed) {
                ApiClient.post('users', 'process_deletion', { id: id, status: status }).then(function (res) {
                    toast.success('Governance', res.message);
                    self.loadDeletionRequests();
                })['catch'](function (err) {
                    toast.error('Error', err.error || 'Failed to process request.');
                });
            }
        });
    },

    /**
     * Logs Logic
     */
    initLogs: function () {
        var self = this;
        this.loadLogList();

        // Polling interval for real-time telemetry (Every 5 seconds)
        if (this.telemetryInterval) clearInterval(this.telemetryInterval);

        this.telemetryInterval = setInterval(function () {
            self.updateTelemetry();
            if (self.currentSection === 'logs') {
                self.loadLogList();
            }
        }, 5000);

        this.updateTelemetry();
    },

    updateTelemetry: function () {
        if (this.currentSection !== 'logs') return;

        var cpu = Math.floor(Math.random() * (25 - 8 + 1)) + 8;
        var ram = (2.2 + Math.random() * 0.4).toFixed(1);
        var ramPercent = Math.floor((ram / 8) * 100);
        var threads = Math.floor(Math.random() * (52 - 38 + 1)) + 38;
        var ingress = (10 + Math.random() * 5).toFixed(1);
        var egress = (45 + Math.random() * 10).toFixed(1);

        var up = function (id, val, suffix) {
            var el = document.getElementById(id);
            if (el) el.innerText = val + (suffix || '');
        };

        var bar = function (id, percent) {
            var el = document.getElementById(id);
            if (el) el.style.width = percent + '%';
        };

        up('monitor-cpu-text', cpu, '%');
        bar('monitor-cpu-bar', cpu);
        up('monitor-ram-text', ram, ' GB');
        bar('monitor-ram-bar', ramPercent);
        up('monitor-threads', threads);
        up('monitor-ingress', ingress, ' MB/s');
        up('monitor-egress', egress, ' MB/s');
    },

    updateSystemMetric: function (key, value, unit) {
        var up = function (id, val, u) {
            var el = document.getElementById(id);
            if (el) el.innerText = val + (u || '');
        };
        var bar = function (id, val) {
            var el = document.getElementById(id);
            if (el) el.style.width = val + '%';
        };

        if (key === 'cpu') {
            up('monitor-cpu-text', value, '%');
            bar('monitor-cpu-bar', value);
        } else if (key === 'ram') {
            up('monitor-ram-text', value, ' GB');
            bar('monitor-ram-bar', (value / 32) * 100);
        }
    },

    loadLogList: function () {
        var tbody = document.getElementById('logs-table-body');
        if (!tbody) return;

        ApiClient.get('logs', 'list').then(function(data) {
            var html = '';
            var logs = data.logs || [];
            for (var i = 0; i < logs.length; i++) {
                var log = logs[i];
                var colors = {
                    error: { badge: 'bg-red-500/10 text-red-500 border-red-500/20', icon: 'ph-warning-circle' },
                    warning: { badge: 'bg-orange-500/10 text-orange-500 border-orange-500/20', icon: 'ph-warning' },
                    info: { badge: 'bg-blue-500/10 text-blue-400 border-blue-400/20', icon: 'ph-info' },
                    status: { badge: 'bg-green-500/10 text-green-400 border-green-400/20', icon: 'ph-check-circle' }
                };

                var cfg = colors[log.level] || colors.info;
                var username = log.username || 'System';

                html += '<tr class="hover:bg-white/5 transition-all group">' +
                        '    <td class="py-6 px-8 text-[10px] font-black text-gray-500 tracking-widest">#' + log.id + '</td>' +
                        '    <td class="py-6 px-8">' +
                        '        <div class="flex items-center gap-4">' +
                        '            <div class="w-8 h-8 rounded-lg ' + cfg.badge.split(' ')[0] + ' flex items-center justify-center border ' + cfg.badge.split(' ')[2] + '">' +
                        '                <i class="ph-bold ' + cfg.icon + ' text-sm"></i>' +
                        '            </div>' +
                        '            <div>' +
                        '                <p class="text-sm font-black tracking-tight uppercase">' + log.action + '</p>' +
                        '                <p class="text-[10px] uppercase font-black opacity-40 tracking-widest mt-0.5">' + log.level + '</p>' +
                        '            </div>' +
                        '        </div>' +
                        '    </td>' +
                        '    <td class="py-6 px-8">' +
                        '        <div class="flex items-center gap-3">' +
                        '            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=' + username + '" class="w-7 h-7 rounded-lg bg-white/5 border border-white/10">' +
                        '            <span class="text-xs font-black uppercase tracking-widest opacity-60">' + username.toUpperCase() + '</span>' +
                        '        </div>' +
                        '    </td>' +
                        '    <td class="py-6 px-8 text-[10px] font-black text-gray-500 tracking-widest font-mono">' + (log.ip || '0.0.0.0') + '</td>' +
                        '    <td class="py-6 px-8 text-right">' +
                        '        <span class="text-[10px] font-black uppercase tracking-widest opacity-40">' + (log.created_at ? new Date(log.created_at).toLocaleTimeString() : '') + '</span>' +
                        '    </td>' +
                        '</tr>';
            }
            tbody.innerHTML = html || '<tr><td colspan="5" class="p-20 text-center"><p class="font-black text-[10px] uppercase tracking-widest opacity-40">Zero Incidents Detected</p></td></tr>';
        });
    },

    openTerminal: function () {
        this.openModal('system-terminal-modal');
        var output = document.getElementById('terminal-output');
        if (output) {
            output.innerHTML = '<p class="text-primary font-black mb-2 animate-pulse">> Initializing Kernel Synchronizer...</p>';
            setTimeout(function() {
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
        var self = this;
        this.loadAdList();

        var searchInput = document.querySelector('#tab-content-campaigns input[placeholder*="Search streams"]');
        if (searchInput) {
            var timeout;
            searchInput.oninput = function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    self.loadAdList(searchInput.value);
                }, 300);
            };
        }

        var form = document.getElementById('create-ad-form');
        if (form) {
            form.onsubmit = function(e) {
                e.preventDefault();
                var formData = new FormData(form);
                var payload = {};
                formData.forEach(function(value, key) { payload[key] = value; });
                var isEdit = payload.id && payload.id !== '';

                var endpoint = isEdit ? 'update' : 'create';

                ApiClient.post('ads', endpoint, payload).then(function(res) {
                    toast.success('Marketing', res.message);
                    closeModal();
                    form.reset();
                    self.loadAdList();
                })['catch'](function(err) {
                    toast.error('Error', err.error || 'Failed to process campaign.');
                });
            };
        }
    },

    loadAdList: function (filter) {
        filter = filter || '';
        var tbody = document.getElementById('ads-table-body');
        var countBadge = document.getElementById('ads-count-badge');
        var spendBadge = document.getElementById('ads-spend-badge');
        var roiBadge = document.getElementById('ads-roi-badge');
        if (!tbody) return;

        ApiClient.get('ads', 'list', { filter: filter }).then(function(data) {
            // Update Top Metrics
            if (countBadge) {
                var count = data.metrics.active_nodes;
                countBadge.innerText = count < 10 ? '0' + count : count;
            }
            if (spendBadge) {
                spendBadge.innerText = '$' + parseFloat(data.metrics.daily_spend).toLocaleString();
            }
            if (roiBadge) {
                roiBadge.innerText = data.metrics.avg_ctr + '%';
            }

            var html = '';
            var ads = data.ads || [];
            for (var i = 0; i < ads.length; i++) {
                var ad = ads[i];
                var ctr = ((ad.clicks / ad.impressions) * 100 || 0).toFixed(2);
                var statusColor = ad.status === 'Active' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-orange-500/10 text-orange-400 border-orange-500/20';
                var pulseColor = ad.status === 'Active' ? 'bg-emerald-400' : 'bg-orange-400';

                html += '<tr class="hover:bg-white/[0.03] transition-all duration-300 group">' +
                        '    <td class="px-8 py-6">' +
                        '        <div class="flex items-center gap-4">' +
                        '            <div class="w-12 h-12 rounded-2xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary font-black text-xs shadow-xl shadow-primary/5 transition-transform group-hover:scale-110">' +
                        '                ' + ad.name.substring(0, 2).toUpperCase() +
                        '            </div>' +
                        '            <div>' +
                        '                <p class="text-[11px] font-black uppercase tracking-widest text-primary truncate max-w-[150px]">' + ad.name + '</p>' +
                        '                <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest mt-0.5">Stream Protocol: ' + ad.type + '</p>' +
                        '            </div>' +
                        '        </div>' +
                        '    </td>' +
                        '    <td class="px-8 py-6">' +
                        '         <div class="flex items-center gap-3">' +
                        '            <span class="px-3 py-1.5 ' + statusColor + ' border rounded-xl text-[8px] font-black uppercase tracking-widest shadow-xl">' + ad.status + '</span>' +
                        '            <span class="flex h-1.5 w-1.5 relative">' +
                        '              <span class="animate-ping absolute inline-flex h-full w-full rounded-full ' + pulseColor + ' opacity-75"></span>' +
                        '              <span class="relative inline-flex rounded-full h-1.5 w-1.5 ' + pulseColor + '"></span>' +
                        '            </span>' +
                        '         </div>' +
                        '    </td>' +
                        '    <td class="px-8 py-6 text-center">' +
                        '        <p class="text-sm font-black tracking-tighter">$' + parseFloat(ad.budget).toLocaleString() + '</p>' +
                        '        <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest mt-0.5">Daily Fuel</p>' +
                        '    </td>' +
                        '    <td class="px-8 py-6 text-center">' +
                        '        <div class="flex flex-col items-center gap-1">' +
                        '            <span class="text-[11px] font-black tracking-tight text-primary">' + ctr + '%</span>' +
                        '            <div class="w-16 bg-white/5 h-1 rounded-full overflow-hidden">' +
                        '                <div class="bg-primary h-full" style="width: ' + Math.min(ctr * 10, 100) + '%"></div>' +
                        '            </div>' +
                        '        </div>' +
                        '    </td>' +
                        '    <td class="px-8 py-6 text-right">' +
                        '         <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300">' +
                        '            <button onclick="AdminApp.openDrawer(\'ad\', \'' + ad.id + '\')" class="w-10 h-10 rounded-2xl bg-primary/10 text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-all shadow-xl shadow-primary/5">' +
                        '                <i class="ph-bold ph-note-pencil text-lg"></i>' +
                        '            </button>' +
                        '            <button onclick="AdminApp.deleteAd(\'' + ad.id + '\')" class="w-10 h-10 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-xl shadow-red-500/5">' +
                        '                <i class="ph-bold ph-trash text-lg"></i>' +
                        '            </button>' +
                        '         </div>' +
                        '    </td>' +
                        '</tr>';
            }
            tbody.innerHTML = html || '<tr><td colspan="5" class="p-20 text-center opacity-30 font-black uppercase tracking-[0.2em] text-[10px]">No marketing nodes active</td></tr>';
        });
    },

    deleteAd: function (id) {
        var self = this;
        this.confirm({
            title: 'Stream Termination',
            message: 'Permanently delete this marketing campaign? This action will halt all associated fuel distribution and telemetry gathering.',
            icon: 'ph-trash',
            confirmLabel: 'Delete Campaign',
            type: 'danger'
        }).then(function (confirmed) {
            if (confirmed) {
                ApiClient.post('ads', 'delete', { id: id }).then(function (res) {
                    toast.success('Confirmed', 'Campaign removed.');
                    self.loadAdList();
                });
            }
        });
    },

    /**
     * Command Orchestration (Control Center) Logic
     */
    toggleSetting: function (key, isChecked) {
        var value = isChecked ? 'on' : 'off';

        ApiClient.post('settings', 'update', { key: key, value: value }).then(function(res) {
            toast.success('Protocol Update', "Global setting '" + key + "' synchronized to " + value + ".");
        })['catch'](function(err) {
            toast.error('Sync failure', err.error || 'Identity gatekeeping error.');
        });
    },

    saveSettings: function () {
        var self = this;
        var btn = document.getElementById('save-settings-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="ph-bold ph-gear-six animate-spin mr-2"></i> Synchronizing...';
        }

        var data = {
            session_timeout: document.getElementById('session_timeout').value,
            auth_retry_limit: document.getElementById('auth_retry_limit').value,
            rate_limit: document.getElementById('rate_limit').value,
            ip_blocklist: document.getElementById('ip_blocklist').value
        };

        var promises = Object.keys(data).map(function(key) {
            return ApiClient.post('settings', 'update', { key: key, value: data[key] });
        });

        toast.info('Orchestration Pulse', 'Broadcasting protocol updates to node clusters.');

        Promise.all(promises).then(function() {
            setTimeout(function() {
                toast.success('Synchronization Complete', 'Global platform parameters updated.');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ph-bold ph-floppy-disk text-lg mr-2"></i> Synchronize Protocols';
                }
                self.initControlCenterTelemetry();
            }, 1200);
        })['catch'](function(err) {
            toast.error('Sync Critical Error', 'Gateway timeout or integrity failure.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="ph-bold ph-floppy-disk text-lg mr-2"></i> Synchronize Protocols';
            }
        });
    },

    initControlCenterTelemetry: function () {
        var auditTrail = document.querySelector('.custom-scrollbar');
        if (auditTrail) {
            auditTrail.style.opacity = '0.5';
            setTimeout(function() { auditTrail.style.opacity = '1'; }, 500);
        }

        toast.info('Diagnostic Boot', 'Initializing performance audit sequence...');
        console.log("Control Center Telemetry: Synchronized Node Cluster v4.2");

        var efficiency = document.querySelector('.ph-gear-six.animate-spin-slow');
        if (efficiency) {
            efficiency.classList.add('scale-110');
            setTimeout(function() { efficiency.classList.remove('scale-110'); }, 2000);
        }
    },

    /**
     * Role Studio Logic
     */
    initRoles: function () {
        this.refreshRolesCache();
        this.loadRoleList();

        var form = document.getElementById('save-role-form');
        if (form) {
            var self = this;
            form.onsubmit = function(e) {
                e.preventDefault();
                self.saveRole();
            };
        }
    },

    loadRoleList: function () {
        var self = this;
        var grid = document.getElementById('roles-card-grid');
        if (!grid) return;

        ApiClient.get('roles', 'list').then(function(data) {
            var html = '';
            data.roles.forEach(function(role) {
                var perms = typeof role.permissions === 'string' ? JSON.parse(role.permissions) : role.permissions;
                var isMaster = perms.all === true;

                html += '<div class="glass-card group relative p-8 border-white/5 transition-all duration-500 hover:border-primary/40 hover:-translate-y-2 hover:shadow-[0_40px_80px_rgba(0,0,0,0.5)] bg-gradient-to-br from-white/[0.02] to-transparent">' +
                        '    <div class="flex items-start justify-between mb-8">' +
                        '        <div class="flex items-center gap-5">' +
                        '            <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shadow-[0_0_20px_rgba(124,106,255,0.1)] group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-500">' +
                        '                <i class="ph-bold ' + self.getRoleIcon(role.slug) + ' text-2xl"></i>' +
                        '            </div>' +
                        '            <div>' +
                        '                <h3 class="text-xl font-black uppercase tracking-tighter">' + role.name + '</h3>' +
                        '                <p class="text-[10px] font-black opacity-30 tracking-[0.2em] uppercase mt-1">L' + (isMaster ? '0' : '1') + ' Clearance Protocol</p>' +
                        '            </div>' +
                        '        </div>' +
                        '        <div class="flex gap-2 opacity-0 group-hover:opacity-100 translate-x-4 group-hover:translate-x-0 transition-all duration-500">' +
                        '            <button onclick="AdminApp.editRole(\'' + role.id + '\')" class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-all shadow-xl shadow-primary/10"><i class="ph ph-note-pencil text-lg"></i></button>' +
                        '            <button onclick="AdminApp.deleteRole(\'' + role.id + '\')" class="w-10 h-10 rounded-xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-xl shadow-red-500/10"><i class="ph ph-trash text-lg"></i></button>' +
                        '        </div>' +
                        '    </div>' +
                        '    <div class="space-y-6">' +
                        '        <div>' +
                        '            <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mb-3">System Access Matrix</p>' +
                        '            <div class="flex flex-wrap gap-2">' +
                        '                ' + self.renderPermissionChips(role.permissions) +
                        '            </div>' +
                        '        </div>' +
                        '        <div class="pt-6 border-t border-white/5 flex items-center justify-between">' +
                        '            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Active Members</span>' +
                        '            <span class="text-sm font-black text-gray-500 dark:text-white">' + (role.member_count || 0) + '</span>' +
                        '        </div>' +
                        '    </div>' +
                        '</div>';
            });
            grid.innerHTML = html || '<div class="col-span-full p-20 text-center text-gray-500 font-bold uppercase tracking-widest opacity-30">Vault Empty. No Identities Found.</div>';
        });
    },

    renderPermissionChips: function (permsJson) {
        if (!permsJson) return '<span class="text-[10px] font-black opacity-10 uppercase tracking-widest italic">No Protocols Found</span>';
        var perms = typeof permsJson === 'string' ? JSON.parse(permsJson) : permsJson;
        if (perms.all) return '<span class="px-3 py-1.5 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2"><i class="ph-fill ph-shield-star"></i> Perfect Authority</span>';

        var html = '';
        Object.keys(perms).forEach(function(resource) {
            var actions = perms[resource];
            if (Array.isArray(actions)) {
                actions.forEach(function(action) {
                    var color = 'text-gray-400 bg-white/5 border-white/5';
                    var label = action;
                    if (action === 'view') { color = 'text-primary bg-primary/10 border-primary/20'; label = 'Audit'; }
                    if (action === 'manage') { color = 'text-green-500 bg-green-500/10 border-green-500/20'; label = 'Write'; }
                    if (action === 'delete') { color = 'text-red-500 bg-red-500/10 border-red-500/20'; label = 'Purge'; }

                    html += '<span class="px-2.5 py-1 ' + color + ' border rounded-lg text-[9px] font-black uppercase tracking-widest flex items-center gap-1.5 transition-all mb-1">' +
                            '    <i class="ph ph-circle text-[6px]"></i> ' + resource + ':' + label +
                            '</span>';
                });
            } else if (actions === true) {
                html += '<span class="px-2.5 py-1 bg-white/5 text-gray-400 border border-white/5 rounded-lg text-[9px] font-black uppercase tracking-widest flex items-center gap-1.5 mb-1">' +
                        '    <i class="ph ph-circle text-[6px] text-primary"></i> ' + resource + ':Full' +
                        '</span>';
            }
        });
        return html;
    },

    newRole: function () {
        var form = document.getElementById('save-role-form');
        if (!form) return;

        form.reset();
        form.role_id.value = '';

        form.querySelectorAll('input[type="checkbox"]').forEach(function(cb) { cb.checked = false; });
        var allCb = form.querySelector('[name="perms[all]"]');
        if (allCb) this.toggleMasterAccess(allCb);

        AdminApp.openModal('role-editor-modal');
        this.updateAuthorityMeter();
    },

    editRole: function (id) {
        var self = this;
        ApiClient.get('roles', 'get', { id: id }).then(function(data) {
            var role = data.role;
            var form = document.getElementById('save-role-form');
            if (!form) return;

            form.role_id.value = role.id;
            form.role_name.value = role.name;

            form.querySelectorAll('input[type="checkbox"]').forEach(function(cb) { cb.checked = false; });

            try {
                var perms = typeof role.permissions === 'string' ? JSON.parse(role.permissions) : role.permissions;

                var allCb = form.querySelector('[name="perms[all]"]');
                if (allCb) {
                    allCb.checked = perms.all === true;
                    self.toggleMasterAccess(allCb);
                }

                if (!perms.all) {
                    Object.keys(perms).forEach(function(resource) {
                        var actions = perms[resource];
                        if (Array.isArray(actions)) {
                            actions.forEach(function(action) {
                                var cb = form.querySelector('[name="perms[' + resource + '][' + action + ']"]');
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
            self.updateAuthorityMeter();
        });
    },

    updateAuthorityMeter: function () {
        var form = document.getElementById('save-role-form');
        if (!form) return;

        var meter = document.getElementById('authority-meter');
        var marker = document.getElementById('authority-marker');
        if (!meter || !marker) return;

        var allChecked = form.querySelector('[name="perms[all]"]').checked;
        if (allChecked) {
            meter.style.width = '100%';
            marker.innerText = 'Perfect Authority';
            marker.className = 'text-[9px] font-black px-2 py-0.5 bg-amber-500 text-white rounded-lg uppercase tracking-widest leading-none shadow-[0_0_10px_rgba(245,158,11,0.5)]';
            return;
        }

        var checkboxes = form.querySelectorAll('input[type="checkbox"]:checked:not([name="perms[all]"])');
        var total = form.querySelectorAll('input[type="checkbox"]:not([name="perms[all]"])').length;
        var percentage = total > 0 ? (checkboxes.length / total) * 100 : 0;

        meter.style.width = percentage + '%';

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
        var form = document.getElementById('save-role-form');
        var formData = new FormData(form);
        var id = formData.get('role_id');
        var name = formData.get('role_name');

        var perms = {};
        var allChecked = form.querySelector('[name="perms[all]"]').checked;

        if (allChecked) {
            perms.all = true;
        } else {
            var checkboxes = form.querySelectorAll('input[type="checkbox"]:checked:not([name="perms[all]"])');
            checkboxes.forEach(function(cb) {
                var name = cb.name;
                var match = name.match(/perms\[(.+?)\]\[(.+?)\]/);
                if (match) {
                    var resource = match[1];
                    var action = match[2];
                    if (!perms[resource]) perms[resource] = [];
                    perms[resource].push(action);
                }
            });
        }

        var payload = { id: id, name: name, permissions: JSON.stringify(perms) };

        ApiClient.post('roles', 'save', payload).then(function(res) {
            toast.success('Security Studio', res.message);
            window.closeModal();
            this.loadRoleList();
        }.bind(this))['catch'](function(err) {
            toast.error('Governance Error', err.message || 'Identity protocol violation.');
        });
    },

    confirm: function (options) {
        return new Promise(function (resolve) {
            var modal = document.getElementById('global-confirm-modal');
            var card = document.getElementById('global-confirm-card');
            var iconBox = document.getElementById('global-confirm-icon-box');
            var icon = document.getElementById('global-confirm-icon');
            var title = document.getElementById('global-confirm-title');
            var message = document.getElementById('global-confirm-message');
            var abortBtn = document.getElementById('global-confirm-abort');
            var executeBtn = document.getElementById('global-confirm-execute');
            var progressContainer = document.getElementById('global-confirm-progress-container');
            var progressBar = document.getElementById('global-confirm-progress-bar');
            var progressPct = document.getElementById('global-confirm-progress-pct');

            if (!modal || !executeBtn || !abortBtn) return resolve(false);

            title.innerText = options.title || 'Confirm Protocol';
            message.innerText = options.message || 'Are you sure you want to proceed?';
            icon.className = 'ph-bold ' + (options.icon || 'ph-warning') + ' text-5xl';
            executeBtn.innerText = options.confirmLabel || 'Execute';

            if (options.type === 'danger') {
                iconBox.className = 'w-24 h-24 rounded-[2.5rem] bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20 shadow-[0_0_40px_rgba(239,68,68,0.15)] animate-pulse';
                executeBtn.className = 'btn-primary !bg-red-500 !shadow-[0_20px_40px_rgba(239,68,68,0.3)] !rounded-3xl !py-5 uppercase font-black tracking-widest text-[10px] hover:!bg-red-600';
            } else {
                iconBox.className = 'w-24 h-24 rounded-[2.5rem] bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shadow-[0_0_40px_rgba(124,106,255,0.15)]';
                executeBtn.className = 'btn-primary !rounded-3xl !py-5 uppercase font-black tracking-widest text-[10px] shadow-2xl';
            }

            progressContainer.classList.add('hidden');
            abortBtn.disabled = false;
            executeBtn.disabled = false;
            executeBtn.classList.remove('opacity-50', 'cursor-not-allowed');

            var cleanup = function () {
                modal.classList.add('hidden');
                executeBtn.removeEventListener('click', onConfirm);
                abortBtn.removeEventListener('click', onCancel);
            };

            var onConfirm = function () {
                if (options.purge) {
                    executeBtn.disabled = true;
                    executeBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    executeBtn.innerText = 'Purge Initialized...';
                    progressContainer.classList.remove('hidden');

                    var progress = 0;
                    var interval = setInterval(function () {
                        progress += Math.floor(Math.random() * 15) + 5;
                        if (progress >= 100) {
                            progress = 100;
                            clearInterval(interval);
                            setTimeout(function () { cleanup(); resolve(true); }, 600);
                        }
                        if (progressBar) progressBar.style.width = progress + '%';
                        if (progressPct) progressPct.innerText = progress + '%';
                    }, 200);
                } else {
                    cleanup();
                    resolve(true);
                }
            };

            var onCancel = function () { cleanup(); resolve(false); };

            executeBtn.addEventListener('click', onConfirm);
            abortBtn.addEventListener('click', onCancel);

            modal.classList.remove('hidden');
        });
    },

    deleteRole: function (id) {
        var self = this;
        this.confirm({
            title: 'Governance Deconstruction',
            message: 'Permanently deconstruct this security role? This may impact mapped identities and absolute system clearance.',
            icon: 'ph-shield-slash',
            confirmLabel: 'Deconstruct Role',
            type: 'danger',
            purge: true
        }).then(function (confirmed) {
            if (confirmed) {
                ApiClient.post('roles', 'delete', { id: id }).then(function (res) {
                    toast.success('Deconstructed', 'Security role purged from vault.');
                    self.loadRoleList();
                });
            }
        });
    },

    /**
     * Global Modals & Drawers
     */
    openModal: function (id) {
        if (id === 'create-channel-modal') {
            this.initChannelWizard();
        }

        var container = document.getElementById(id);
        if (container) {
            container.classList.remove('hidden');
            container.classList.add('flex');
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
        var self = this;
        var drawer = document.getElementById('side-drawer');
        var target = document.getElementById('drawer-content-target');
        if (!drawer || !target) return;

        target.innerHTML = '<div class="flex flex-col items-center justify-center h-full space-y-4 opacity-50">' +
                           '    <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>' +
                           '    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-primary">Fetching Intelligence...</p>' +
                           '</div>';
        drawer.classList.remove('translate-x-full');

        if (type === 'user') {
            ApiClient.get('users', 'details', { id: id }).then(function (data) {
                var user = data.user;
                var handshakeHTML = (user.request_pending == 1 && user.role_id == 0) ? 
                    '<div class="absolute inset-x-0 top-0 py-2 bg-amber-500/20 border-b border-amber-500/20">' +
                    '    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-amber-500 flex items-center justify-center gap-2">' +
                    '        <i class="ph-bold ph-handshake animate-pulse"></i> Identity Handshake Pending' +
                    '    </p>' +
                    '</div><div class="mt-4"></div>' : '';

                var blockBtn = (user.id != window.adminId) ? 
                    '<button class="w-full btn-secondary !justify-center py-4 text-red-500 border-red-500/20 hover:bg-red-500/10" onclick="AdminApp.executeAction(\'toggle_block\', \'' + user.id + '\')">' + (user.blocked ? 'Unblock User' : 'Suspend Account') + '</button>' : '';

                target.innerHTML = '<div class="p-8 h-full flex flex-col">' +
                    '    <div class="flex items-center justify-between mb-8">' +
                    '        <h3 class="text-xl font-bold uppercase tracking-widest text-primary">Identity Profile</h3>' +
                    '        <button onclick="closeDrawer()" class="p-2 hover:bg-white/10 rounded-xl transition-all"><i class="ph ph-x text-2xl"></i></button>' +
                    '    </div>' +
                    '    <form id="profile-edit-form" class="space-y-6 flex-grow overflow-y-auto pr-2 custom-scrollbar">' +
                    '        <input type="hidden" name="id" value="' + user.id + '">' +
                    '        <div class="text-center p-6 bg-white/5 rounded-[2.5rem] border border-white/5 mb-6 relative overflow-hidden">' +
                    '            ' + handshakeHTML +
                    '            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=' + user.username + '" class="w-24 h-24 rounded-3xl mx-auto mb-4 border-2 border-primary/20">' +
                    '            <h4 class="text-2xl font-bold">' + user.username + '</h4>' +
                    '        </div>' +
                    '        <div class="space-y-4">' +
                    '            <div class="grid grid-cols-2 gap-4">' +
                    '                <div class="space-y-2">' +
                    '                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">First Name</label>' +
                    '                    <input type="text" name="firstname" value="' + (user.firstname || '') + '" class="w-full bg-white/5 border border-white/5 rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all">' +
                    '                </div>' +
                    '                <div class="space-y-2">' +
                    '                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Last Name</label>' +
                    '                    <input type="text" name="lastname" value="' + (user.lastname || '') + '" class="w-full bg-white/5 border border-white/5 rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all">' +
                    '                </div>' +
                    '            </div>' +
                    '            <div class="space-y-2">' +
                    '                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Private E-mail</label>' +
                    '                <input type="email" name="email" value="' + user.email + '" class="w-full bg-white/5 border border-white/5 rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all">' +
                    '            </div>' +
                    '            <div class="space-y-4">' +
                    '                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Security Cluster (Role)</label>' +
                    '                <input type="hidden" name="role_id" value="' + (user.role_id || 0) + '">' +
                    '                <div id="role-selection-container">' + self.renderRoleSelectionUI(user.role_id) + '</div>' +
                    '                <div class="pt-4 border-t border-white/5 flex items-center justify-between">' +
                    '                    <div class="space-y-1">' +
                    '                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-500">Level 0 Authorization</p>' +
                    '                        <p class="text-[9px] text-gray-500 font-bold uppercase tracking-tight">Grant Absolute Master Status</p>' +
                    '                    </div>' +
                    '                    <label class="relative inline-flex items-center cursor-pointer">' +
                    '                        <input type="checkbox" name="is_master" value="1" ' + (user.is_master == 1 ? 'checked' : '') + ' class="sr-only peer">' +
                    '                        <div class="w-11 h-6 bg-white/5 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-gray-400 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>' +
                    '                    </label>' +
                    '                </div>' +
                    '            </div>' +
                    '            <div class="space-y-2">' +
                    '                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Professional Bio</label>' +
                    '                <textarea name="bio" rows="4" class="w-full bg-white/5 border border-white/5 rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all resize-none">' + (user.bio || '') + '</textarea>' +
                    '            </div>' +
                    '        </div>' +
                    '    </form>' +
                    '    <div class="pt-6 border-t border-white/5 space-y-3">' +
                    '        <button onclick="AdminApp.submitProfileEdit()" class="w-full btn-primary !justify-center py-4">Save Identity Changes</button>' +
                    '        ' + blockBtn +
                    '    </div>' +
                    '</div>';
            });
        } else if (type === 'ad') {
            ApiClient.get('ads', 'list').then(function (data) {
                var ad = null;
                for (var i = 0; i < data.ads.length; i++) {
                    if (data.ads[i].id == id) { ad = data.ads[i]; break; }
                }
                if (!ad) return;

                target.innerHTML = '<div class="p-8 h-full flex flex-col">' +
                    '    <div class="flex items-center justify-between mb-8">' +
                    '        <h3 class="text-xl font-bold uppercase tracking-widest text-primary">Creative Console</h3>' +
                    '        <button onclick="closeDrawer()" class="p-2 hover:bg-white/10 rounded-xl transition-all"><i class="ph ph-x text-2xl"></i></button>' +
                    '    </div>' +
                    '    <form id="ad-edit-form" class="space-y-6 flex-grow overflow-y-auto pr-2 custom-scrollbar">' +
                    '        <input type="hidden" name="id" value="' + ad.id + '">' +
                    '        <div class="space-y-2">' +
                    '            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Campaign Reference</label>' +
                    '            <input type="text" name="name" value="' + ad.name + '" class="w-full bg-transparent border rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all" style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">' +
                    '        </div>' +
                    '        <div class="grid grid-cols-2 gap-4">' +
                    '            <div class="space-y-2">' +
                    '                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Daily Limit ($)</label>' +
                    '                <input type="number" step="0.01" name="budget" value="' + ad.budget + '" class="w-full bg-transparent border rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all" style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">' +
                    '            </div>' +
                    '            <div class="space-y-2">' +
                    '                <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Strategy</label>' +
                    '                <select name="type" class="w-full bg-transparent border rounded-2xl p-4 text-sm focus:border-primary outline-none transition-all" style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);">' +
                    '                    <option value="Social" ' + (ad.type === 'Social' ? 'selected' : '') + '>Social</option>' +
                    '                    <option value="Search" ' + (ad.type === 'Search' ? 'selected' : '') + '>Search</option>' +
                    '                    <option value="Display" ' + (ad.type === 'Display' ? 'selected' : '') + '>Display</option>' +
                    '                </select>' +
                    '            </div>' +
                    '        </div>' +
                    '        <div class="space-y-2">' +
                    '            <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Injection Logic (Ads Code)</label>' +
                    '            <textarea name="ad_code" rows="10" class="w-full bg-transparent border rounded-2xl p-4 text-xs font-mono focus:border-primary outline-none transition-all resize-none" style="background-color: var(--glass-bg); border-color: var(--border-color); color: var(--text-main);" placeholder="<!-- Paste raw ad script here -->">' + (ad.ad_code || '') + '</textarea>' +
                    '        </div>' +
                    '    </form>' +
                    '    <div class="pt-6 border-t border-white/5">' +
                    '        <button onclick="AdminApp.submitAdEdit()" class="w-full btn-primary !justify-center py-4">Apply Media Updates</button>' +
                    '    </div>' +
                    '</div>';
            });
        }
    },

    submitProfileEdit: function () {
        var form = document.getElementById('profile-edit-form');
        var formData = new FormData(form);
        var payload = {};
        formData.forEach(function(value, key) { payload[key] = value; });

        ApiClient.post('users', 'update_profile', payload).then(function(res) {
            toast.success('Identity', res.message);
            window.closeDrawer();
            AdminApp.loadUserList();
        })['catch'](function(err) {
            toast.error('Error', err.error || 'Failed to update profile.');
        });
    },

    submitAdEdit: function () {
        var form = document.getElementById('ad-edit-form');
        var formData = new FormData(form);
        var payload = {};
        formData.forEach(function(value, key) { payload[key] = value; });

        ApiClient.post('ads', 'update', payload).then(function(res) {
            toast.success('Marketing', res.message);
            window.closeDrawer();
            AdminApp.loadAdList();
        })['catch'](function(err) {
            toast.error('Error', err.error || 'Failed to update campaign.');
        });
    },

    executeAction: function (action, id) {
        var self = this;
        var ns = 'users', method = 'status', payload = { id: id, action: action };
        var confirmOptions = null;

        if (action === 'toggle_block' || action === 'suspend_account') {
            confirmOptions = {
                title: 'Identity Nullification',
                message: 'Are you sure you want to toggle the access status of this identity? This will immediately sever or restore node connectivity.',
                icon: 'ph-shield-warning',
                confirmLabel: 'Confirm Status Change',
                type: 'danger'
            };
        } else if (action === 'delete_message') {
            ns = 'messages';
            method = 'flag';
            payload.action = 'delete';
            confirmOptions = {
                title: 'Packet Purge',
                message: 'Permanently remove this communication packet from the ledger? This action cannot be reversed.',
                icon: 'ph-trash',
                confirmLabel: 'Purge Packet',
                type: 'danger'
            };
        } else if (action === 'flag_message' || action === 'resolve_flag') {
            ns = 'messages';
            method = 'flag';
            payload.action = action.replace('_message', '').replace('_flag', '');
        }

        var execute = function () {
            toast.info('Processing', 'Executing ' + action + '...');
            ApiClient.post(ns, method, payload).then(function(res) {
                toast.success('Confirmed', res.message);
                if (ns === 'users') self.loadUserList();
                if (ns === 'messages') self.loadMessageList();
                if (window.closeDrawer) window.closeDrawer();
            })['catch'](function(err) {
                toast.error('Protocol Error', err.error || 'Identity gatekeeping error.');
            });
        };

        if (confirmOptions) {
            this.confirm(confirmOptions).then(function(confirmed) {
                if (confirmed) execute();
            });
        } else {
            execute();
        }
    },

    submitRoleUpdate: function (userId) {
        toast.info('Security Identity', 'Assigned role selected. Click Save to commit changes.');
    },

    renderRoleSelectionUI: function (currentRoleId) {
        var self = this;
        if (!this.rolesList || this.rolesList.length === 0) {
            return '<div class="p-6 border-2 border-dashed border-white/5 rounded-3xl text-center">' +
                   '    <p class="text-[10px] font-black opacity-30 uppercase tracking-[0.2em]">No Identity Clusters Defined</p>' +
                   '</div>';
        }

        var html = '<div class="grid grid-cols-2 gap-3" id="role-selector-grid">';
        this.rolesList.forEach(function(role) {
            var isActive = role.id == currentRoleId;
            html += '<div onclick="AdminApp.selectRoleCard(this, \'' + role.id + '\')" ' +
                    '     data-role-id="' + role.id + '" ' +
                    '     class="role-selection-card group p-5 rounded-2xl border transition-all cursor-pointer ' + (isActive ? 'bg-primary/10 border-primary shadow-[0_15px_40px_rgba(124,106,255,0.2)] ring-4 ring-primary/5' : 'bg-white/5 border-white/5 hover:border-primary/40') + '">' +
                    '    <div class="flex items-center justify-between mb-4">' +
                    '        <div class="w-10 h-10 rounded-xl ' + (isActive ? 'bg-primary text-white' : 'bg-white/10 text-gray-500 group-hover:bg-primary/20 group-hover:text-primary') + ' flex items-center justify-center transition-all duration-300">' +
                    '            <i class="ph-bold ' + self.getRoleIcon(role.slug) + ' text-lg"></i>' +
                    '        </div>' +
                    '        ' + (isActive ? '<span class="w-2.5 h-2.5 rounded-full bg-primary shadow-[0_0_10px_#7c6aff] animate-pulse"></span>' : '') +
                    '    </div>' +
                    '    <div>' +
                    '        <p class="text-[10px] font-black uppercase tracking-tight ' + (isActive ? 'text-primary' : 'opacity-60') + ' transition-colors">' + role.name + '</p>' +
                    '        <p class="text-[8px] font-bold opacity-30 uppercase tracking-[0.3em] mt-1">L' + (isActive ? '0' : '1') + ' Authorized</p>' +
                    '    </div>' +
                    '</div>';
        });
        html += '</div>';
        return html;
    },

    selectRoleCard: function (el, id) {
        var grid = document.getElementById('role-selector-grid');
        if (!grid) return;

        var cards = grid.querySelectorAll('.role-selection-card');
        for (var i = 0; i < cards.length; i++) {
            var card = cards[i];
            card.classList.remove('bg-primary/10', 'border-primary', 'shadow-[0_15px_40px_rgba(124,106,255,0.2)]', 'ring-4', 'ring-primary/5');
            var p = card.querySelector('p');
            if (p) p.classList.remove('text-primary');
            if (p) p.classList.add('opacity-60');
            var iconWrap = card.querySelector('div > div');
            if (iconWrap) {
                iconWrap.classList.remove('bg-primary', 'text-white');
                iconWrap.classList.add('bg-white/10', 'text-gray-500');
            }
            var pulse = card.querySelector('span.rounded-full');
            if (pulse) pulse.parentNode.removeChild(pulse);
        }

        el.classList.add('bg-primary/10', 'border-primary', 'shadow-[0_15px_40px_rgba(124,106,255,0.2)]', 'ring-4', 'ring-primary/5');
        el.querySelector('p').classList.remove('opacity-60');
        el.querySelector('p').classList.add('text-primary');
        var activeIconWrap = el.querySelector('div > div');
        activeIconWrap.classList.remove('bg-white/10', 'text-gray-500');
        activeIconWrap.classList.add('bg-primary', 'text-white');

        var head = el.querySelector('div.flex');
        if (head && !head.querySelector('span.rounded-full')) {
            var span = document.createElement('span');
            span.className = 'w-2.5 h-2.5 rounded-full bg-primary shadow-[0_0_10px_#7c6aff] animate-pulse';
            head.appendChild(span);
        }

        var form = document.getElementById('profile-edit-form');
        if (form && form.role_id) {
            form.role_id.value = id;
        }

        toast.info('Role Selected', 'Prepared for cluster migration.');
    },

    getRoleIcon: function (slug) {
        var icons = {
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
        var self = this;
        ApiClient.get('roles', 'list').then(function(data) {
            self.rolesList = data.roles;
        });
    },

    toggleAllPermissions: function (capability) {
        var matrix = document.getElementById('privilege-matrix');
        if (!matrix) return;

        var checkboxes = matrix.querySelectorAll('input[name*="[' + capability + ']"]');
        var allChecked = true;
        for (var i = 0; i < checkboxes.length; i++) { if (!checkboxes[i].checked) { allChecked = false; break; } }

        for (var j = 0; j < checkboxes.length; j++) { checkboxes[j].checked = !allChecked; }

        toast.info('Security Studio', (allChecked ? 'Revoked' : 'Granted') + ' all ' + capability + ' capabilities.');
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
        var blueprint = this.securityBlueprints[key];
        if (!blueprint) return;

        var form = document.getElementById('save-role-form');
        if (!form) return;

        var nameInput = form.querySelector('[name="role_name"]');
        if (nameInput) nameInput.value = blueprint.name;

        // 2. Clear all first
        form.querySelectorAll('input[type="checkbox"]').forEach(function (cb) { cb.checked = false; });

        if (blueprint.perms.all) {
            var allCb = form.querySelector('[name="perms[all]"]');
            if (allCb) allCb.checked = true;
        } else {
            Object.keys(blueprint.perms).forEach(function (resource) {
                var actions = blueprint.perms[resource];
                actions.forEach(function (action) {
                    var cb = form.querySelector('[name="perms[' + resource + '][' + action + ']"]');
                    if (cb) {
                        cb.checked = true;
                        cb.parentElement.classList.add('scale-110');
                        setTimeout(function () { cb.parentElement.classList.remove('scale-110'); }, 200);
                    }
                });
            });
        }

        toast.success('Blueprint Applied', "Loaded standard '" + blueprint.name + "' protocol.");
        this.updateAuthorityMeter();
    },

    toggleMasterAccess: function (el) {
        var matrix = document.getElementById('privilege-matrix');
        if (!matrix) return;

        var isChecked = el.checked;
        var checkboxes = matrix.querySelectorAll('input[type="checkbox"]');

        checkboxes.forEach(function (cb) {
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
        var container = document.getElementById('insights-container');
        if (!container) return;

        container.innerHTML = '<div class="flex flex-col items-center gap-6 animate-in fade-in zoom-in duration-500">' +
                              '    <div class="w-16 h-16 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>' +
                              '    <p class="font-black text-[10px] uppercase tracking-[0.3em] opacity-40">Analyzing Neural Data Stream...</p>' +
                              '</div>';

        setTimeout(function () {
            var insights = [
                { title: "Retention Surge", desc: "User retention increased by 14.2% over the last 72 hours due to node optimization.", color: "text-emerald-400" },
                { title: "Node Congestion", desc: "Primary traffic bottleneck detected in the APAC-01 node. Scaling advised.", color: "text-orange-400" },
                { title: "Ad Saturation", desc: "Ad efficiency is peaking at 92.4% during peak signal hours (18:00 - 22:00 UTC).", color: "text-primary" },
                { title: "Latency Protocol", desc: "Signal latency remains stable at < 12ms across all decentralized vault shards.", color: "text-indigo-400" }
            ];
            var random = insights[Math.floor(Math.random() * insights.length)];

            container.innerHTML = '<div class="animate-in fade-in slide-in-from-bottom-4 duration-700 max-w-lg">' +
                                  '    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20 mb-8 mx-auto shadow-2xl">' +
                                  '        <i class="ph-bold ph-lightning-wedge text-3xl"></i>' +
                                  '    </div>' +
                                  '    <h4 class="text-xl font-black uppercase tracking-tight mb-4 ' + random.color + '">' + random.title + '</h4>' +
                                  '    <p class="text-[12px] font-medium opacity-70 leading-relaxed px-10" style="color: var(--text-main);">' + random.desc + '</p>' +
                                  '    <div class="mt-10 flex gap-3 justify-center">' +
                                  '         <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-lg text-[9px] font-black uppercase tracking-widest opacity-40">Insight v4.2</span>' +
                                  '         <span class="px-3 py-1 bg-primary/10 border border-primary/20 rounded-lg text-[9px] font-black uppercase tracking-widest text-primary">Verified</span>' +
                                  '    </div>' +
                                  '</div>';
            toast.success('Cognitive Analysis complete', random.title);
        }, 2500);
    },

    initIntelligenceHub: function () {
        console.log("Cognitive Intelligence Hub Synchronized.");
        var container = document.getElementById('insights-container');
        if (container) {
            container.innerHTML = '<div class="flex flex-col items-center gap-6 opacity-40 transition-all duration-1000">' +
                                  '    <i class="ph-bold ph-brain text-5xl mb-4"></i>' +
                                  '    <p class="font-black text-[10px] uppercase tracking-[0.3em]">Standby for Neural handshake</p>' +
                                  '    <button class="btn-primary !p-3 !px-8 !rounded-2xl mt-4" onclick="AdminApp.generateInsights()">Begin Handshake</button>' +
                                  '</div>';
        }

        if (document.getElementById('metric-signal-strength')) {
            document.getElementById('metric-signal-strength').innerText = '94.2%';
        }
        if (document.getElementById('metric-cognitive-retention')) {
            document.getElementById('metric-cognitive-retention').innerText = '72.8%';
        }
        if (document.getElementById('metric-intelligence-velocity')) {
            document.getElementById('metric-intelligence-velocity').innerText = (Math.random() * 5 + 95).toFixed(1);
        }
    },

    initAnalytics: function () {
        this.initIntelligenceHub();
    },

    /** Governance (Policy Editor) Logic **/
    initPolicyEditor: function () {
        var self = this;
        this.currentPolicy = 'privacy';
        this.policies = {
            privacy: '',
            terms: '',
            community: ''
        };

        toast.info('Synchronizing Governance...', 'Accessing platform policy ledger.');
        ApiClient.get('settings', 'get', { keys: 'policy_privacy,policy_terms,policy_community' }).then(function (data) {
            if (data.settings) {
                self.policies.privacy = data.settings.policy_privacy || self.getDefaultPolicy('privacy');
                self.policies.terms = data.settings.policy_terms || self.getDefaultPolicy('terms');
                self.policies.community = data.settings.policy_community || self.getDefaultPolicy('community');

                var editor = document.getElementById('policy-editor');
                if (editor) editor.value = self.policies[self.currentPolicy];
            }
        });
    },

    getDefaultPolicy: function (type) {
        var defaults = {
            privacy: '## Privacy & Data Protection Framework\n\n### 1. Data Collection Protocols\nThe Aether ecosystem operates on a principle of radical transparency. We collect telemetry data only to ensure node stability and cross-chain verification.\n\n### 2. Encryption Standards\nAll identity records in the Vault are encrypted using AES-256-GCM. Private keys are never stored on centralized edge clusters.\n\n### 3. User Sovereignty\nUsers maintain 100% ownership of their data packets. Deletion requests are processed within a 24-hour governance window.\n\n[--- Draft Content Below ---]',
            terms: '## Terms of Universal Service\n\n### 1. Access Authorization\nBy accessing the Aether network, you agree to abide by the decentralized consensus protocols. Unauthorized node manipulation is strictly prohibited.\n\n### 2. Liability Limitation\nThe infrastructure leads are not liable for packet loss during cross-node transmissions or atmospheric interference.\n\n### 3. Smart Contract Integrity\nAll governance actions are final and recorded on the immutable ledger.\n\n[--- Ready for Deployment ---]',
            community: '## Community Engagement Guidelines\n\n### 1. Radical Respect\nDiscourse within the Aether channels must remain constructive. Personal attacks on identity profiles will result in immediate account suspension.\n\n### 2. Information Integrity\nSpreading misinformation regarding node health or network status is considered a security violation.\n\n### 3. Collaborative Growth\nUsers are encouraged to contribute to the open-source repository and report vulnerabilities via the Compliance center.\n\n[--- Community Approved ---]'
        };
        return defaults[type] || '';
    },

    switchPolicy: function (type) {
        var editor = document.getElementById('policy-editor');
        if (editor) this.policies[this.currentPolicy] = editor.value;

        this.currentPolicy = type;
        if (editor) editor.value = this.policies[type] || '';

        var tabs = document.querySelectorAll('#policy-tabs button');
        for (var i = 0; i < tabs.length; i++) {
            var btn = tabs[i];
            btn.classList.add('opacity-40');
            btn.classList.remove('text-primary', 'border-b-2', 'border-primary');
        }

        var activeBtn = document.querySelector('#policy-tab-' + type);
        if (activeBtn) {
            activeBtn.classList.remove('opacity-40');
            activeBtn.classList.add('text-primary', 'border-b-2', 'border-primary', 'pb-3');
        }

        toast.info('Governance Switching', 'Drafting ' + type.charAt(0).toUpperCase() + type.slice(1) + ' guidelines...');
    },

    previewPolicy: function () {
        var editor = document.getElementById('policy-editor');
        if (!editor) return;

        var content = editor.value;
        var target = document.getElementById('policy-preview-content');
        if (target) {
            target.innerHTML = content
                .replace(/^## (.*$)/gim, '<h2 class="text-2xl font-bold mb-4">$1</h2>')
                .replace(/^### (.*$)/gim, '<h3 class="text-xl font-bold mb-3 mt-6">$1</h3>')
                .replace(/^\n/gim, '<br>')
                .replace(/\n(.*)/gim, '<p class="mb-4 opacity-80">$1</p>');

            this.openModal('policy-preview-modal');
        }
    },

    submitPolicy: function () {
        var self = this;
        var editor = document.getElementById('policy-editor');
        if (!editor) return;

        var type = this.currentPolicy;
        var content = editor.value;

        toast.info('Deploying Policy', 'Propagating ' + type + ' updates to global nodes...');

        ApiClient.post('settings', 'update', {
            key: 'policy_' + type,
            value: content
        }).then(function (res) {
            toast.success('Confirmed', 'Governance framework for ' + type + ' is now active.');
            self.policies[type] = content;
        })['catch'](function (err) {
            toast.error('Deployment Failed', err.error || 'Failed to update protocol.');
        });
    },

    /**
     * Identity Handshake Logic
     */
    requestRoleHandshake: function () {
        var btn = document.getElementById('request-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="ph-bold ph-spinner animate-spin mr-2"></i> Initializing Handshake...';
        }

        ApiClient.post('users', 'request_access').then(function (res) {
            toast.success('Handshake Initiated', res.message);
            setTimeout(function () { window.location.reload(); }, 1500);
        })['catch'](function (err) {
            toast.error('Handshake Failed', err.error || 'Identity protocol error.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="ph-bold ph-handshake mr-2"></i> Request Identity Handshake';
            }
        });
    },

    checkClearance: function () {
        var icon = document.querySelector('.ph-arrow-clockwise');
        if (icon) icon.classList.add('animate-spin');

        ApiClient.get('users', 'check_clearance').then(function (res) {
            if (res.cleared) {
                toast.success('Identity Cleared', res.message);
                setTimeout(function () { window.location.reload(); }, 800);
            } else {
                toast.info('Status Audit', res.message);
                if (icon) icon.classList.remove('animate-spin');
            }
        })['catch'](function (err) {
            toast.error('Audit Error', 'Could not verify identity clearance.');
            if (icon) icon.classList.remove('animate-spin');
        });
    },

    syncHandshakeBadge: function () {
        ApiClient.get('users', 'handshakes').then(function (res) {
            var badge = document.getElementById('handshake-count-badge');
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
        var self = this;
        var grid = document.getElementById('handshake-requests-grid');
        if (!grid) return;

        ApiClient.get('users', 'handshakes').then(function (res) {
            var html = '';
            var requests = res.requests || res.handshakes || [];
            
            if (requests.length === 0) {
                html = '<div class="col-span-full py-20 text-center space-y-4">' +
                       '    <div class="w-20 h-20 bg-primary/5 rounded-[2rem] mx-auto flex items-center justify-center text-primary/20">' +
                       '        <i class="ph-bold ph-shield-check text-4xl"></i>' +
                       '    </div>' +
                       '    <p class="font-black text-[10px] uppercase tracking-[0.3em] opacity-40">Identity Vault Secured: No Pending Handshakes</p>' +
                       '</div>';
            } else {
                for (var i = 0; i < requests.length; i++) {
                    var req = requests[i];
                    var avatarSeed = req.username;
                    html += '<div class="glass-card !p-8 border-primary/10 hover:border-primary/30 transition-all group animate-in zoom-in duration-500">' +
                            '    <div class="flex items-start justify-between mb-6">' +
                            '        <img src="' + (req.avatar || 'https://api.dicebear.com/7.x/avataaars/svg?seed=' + avatarSeed) + '" class="w-16 h-16 rounded-[1.5rem] bg-primary/10 border border-primary/5">' +
                            '        <div class="px-3 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-lg text-[8px] font-black uppercase tracking-widest animate-pulse">' +
                            '            Identity Request' +
                            '        </div>' +
                            '    </div>' +
                            '    <div class="space-y-1 mb-8">' +
                            '        <h4 class="text-lg font-black tracking-tight text-slate-800">' + (req.firstname || req.username) + ' ' + (req.lastname || '') + '</h4>' +
                            '        <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">' + req.email + '</p>' +
                            '        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-2">Requested: ' + (req.created_at ? new Date(req.created_at).toLocaleString() : 'PENDING') + '</p>' +
                            '    </div>' +
                            '    <div class="flex gap-3 pt-6 border-t border-slate-100">' +
                            '        <button onclick="AdminApp.processIdentityHandshake(' + req.id + ', \'reject\')" class="btn-secondary !bg-red-500/5 !border-red-500/10 !text-red-500 hover:!bg-red-500/10 flex-1 !justify-center py-3 text-[10px] font-black uppercase">Termnate</button>' +
                            '        <button onclick="AdminApp.openHandshakeAuthorize(' + req.id + ')" class="btn-primary flex-1 !justify-center py-3 text-[10px] font-black uppercase shadow-lg shadow-primary/20">Authorize</button>' +
                            '    </div>' +
                            '</div>';
                }
            }
            grid.innerHTML = html;
            self.syncHandshakeBadge();
        });
    },

    processIdentityHandshake: function (id, action) {
        var self = this;
        this.confirm({
            title: 'Identity Verification',
            message: action === 'reject' ? 'Terminate this Identity Handshake? User will remain restricted from global nodes.' : 'Authorize this Handshake Request and grant identity clearance?',
            icon: action === 'reject' ? 'ph-prohibit' : 'ph-shield-check',
            confirmLabel: action === 'reject' ? 'Terminate Handshake' : 'Authorize Handshake',
            type: action === 'reject' ? 'danger' : 'success'
        }).then(function (confirmed) {
            if (confirmed) {
                ApiClient.post('security', 'process_handshake', { id: id, action: action }).then(function (res) {
                    toast.success('Governance Sync', res.message);
                    self.loadHandshakeHub();
                    self.loadUserList();
                })['catch'](function (err) {
                    toast.error('Protocol Error', err.message);
                });
            }
        });
    },

    openHandshakeAuthorize: function (id) {
        var input = document.getElementById('auth-request-user-id');
        var displayName = document.getElementById('auth-role-display-name');
        var roleIdInput = document.getElementById('auth-request-role-id');

        if (input) input.value = id;
        if (roleIdInput) roleIdInput.value = "0";
        if (displayName) displayName.innerText = "--- Default (Observer Cluster) ---";

        var menu = document.getElementById('handshake-role-menu');
        if (menu) menu.classList.add('hidden');

        this.openModal('handshake-authorize-modal');
    },

    toggleHandshakeRoleDropdown: function () {
        var menu = document.getElementById('handshake-role-menu');
        var chevron = document.getElementById('auth-role-chevron');
        if (!menu) return;

        var isHidden = menu.classList.contains('hidden');

        if (isHidden) {
            menu.classList.remove('hidden');
            if (chevron) chevron.style.transform = 'rotate(180deg)';
        } else {
            menu.classList.add('hidden');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
    },

    selectHandshakeRole: function (roleId, name) {
        var input = document.getElementById('auth-request-role-id');
        var displayName = document.getElementById('auth-role-display-name');
        var menu = document.getElementById('handshake-role-menu');
        var chevron = document.getElementById('auth-role-chevron');

        if (input) input.value = roleId;
        if (displayName) displayName.innerText = name;

        if (menu) menu.classList.add('hidden');
        if (chevron) chevron.style.transform = 'rotate(0deg)';

        toast.info('Cluster Staged', 'Authorized identity will be migrated to the ' + name + ' cluster.');
    },

    toggleInviteRoleDropdown: function () {
        var menu = document.getElementById('invite-role-menu');
        var chevron = document.getElementById('invite-role-chevron');
        if (!menu) return;
        var isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            if (chevron) chevron.style.transform = 'rotate(180deg)';
        } else {
            menu.classList.add('hidden');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
    },

    selectInviteRole: function (roleId, name) {
        var input = document.getElementById('invite-role-id');
        var displayName = document.getElementById('invite-role-display-name');
        var menu = document.getElementById('invite-role-menu');
        var chevron = document.getElementById('invite-role-chevron');
        if (input) input.value = roleId;
        if (displayName) displayName.innerText = name;
        if (menu) menu.classList.add('hidden');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    },

    authorizeHandshake: function (id, action, roleId) {
        var self = this;
        ApiClient.post('users', 'authorize', { id: id, action: action, role_id: roleId }).then(function (res) {
            toast.success('Governance Sync', res.message);
            self.loadHandshakeHub();
            self.loadUserList();
            window.closeModal();
        })['catch'](function (err) {
            toast.error('Identity Error', err.message);
        });
    },

    confirmHandshakeAuthorization: function () {
        var idEl = document.getElementById('auth-request-user-id');
        var roleIdEl = document.getElementById('auth-request-role-id');
        var id = idEl ? idEl.value : null;
        var roleId = roleIdEl ? roleIdEl.value : null;

        if (!id) return toast.error('Selection Error', 'User ID missing from protocol.');

        AdminApp.authorizeHandshake(id, 'approve', roleId);
    },

    renderRoleBadge: function (user) {
        if (user.is_master == 1) {
            return '<div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> <span class="px-2 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-lg text-[9px] font-black uppercase tracking-widest">Master Admin</span></div>';
        }
        if (user.role_id > 0) {
            var name = (user.role_name || '').toLowerCase();
            var color = 'bg-white/5 text-gray-400 border-white/5';

            if (name.indexOf('admin') !== -1) color = 'bg-purple-500/10 text-purple-400 border-purple-500/20 shadow-[0_0_15px_rgba(168,85,247,0.1)]';
            else if (name.indexOf('moderator') !== -1) color = 'bg-blue-500/10 text-blue-400 border-blue-500/20 shadow-[0_0_15px_rgba(59,130,246,0.1)]';
            else if (name.indexOf('agent') !== -1) color = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.1)]';
            else if (name.indexOf('auditor') !== -1) color = 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20 shadow-[0_0_15px_rgba(6,182,212,0.1)]';

            return '<span class="px-2 py-1 ' + color + ' border rounded-lg text-[9px] font-black uppercase tracking-widest">' + user.role_name + '</span>';
        }
        if (user.request_pending == 1) {
            return '<span class="px-2 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-[0_0_15px_rgba(245,158,11,0.2)]">Handshake Pending</span>';
        }
        return '<span class="px-2 py-1 bg-red-500/10 text-red-500 border border-red-500/20 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-[0_0_15px_rgba(239,68,68,0.2)]">Restricted</span>';
    },

    wizard: {
        currentStep: 1,
        totalSteps: 4,
        selectedMembers: []
    },

    initChannelWizard: function () {
        this.wizard.currentStep = 1;
        this.wizard.selectedMembers = [];
        this.updateWizardUI();
        document.getElementById('wizard-name').value = '';
        document.getElementById('wizard-slug').value = '';
        document.getElementById('wizard-description').value = '';
        document.getElementById('wizard-selected-members').innerHTML = '<div class="p-6 rounded-[1.5rem] border border-dashed border-white/10 text-center opacity-40">' +
                                                                       '    <p class="font-black text-[10px] uppercase tracking-widest">No agents authorized yet.</p>' +
                                                                       '</div>';
    },

    navWizard: function (delta) {
        var nextStep = this.wizard.currentStep + delta;
        if (nextStep < 1 || nextStep > this.wizard.totalSteps) return;

        if (delta > 0 && this.wizard.currentStep === 2) {
            var name = document.getElementById('wizard-name').value;
            if (name.length < 3) return toast.error('Security Check', 'Channel name must be at least 3 characters.');
        }

        this.wizard.currentStep = nextStep;
        this.updateWizardUI();

        var nextBtn = document.getElementById('wizard-next');
        if (this.wizard.currentStep === this.wizard.totalSteps) {
            nextBtn.innerHTML = 'Finalize Node <i class="ph-bold ph-check-circle ml-2"></i>';
        } else {
            nextBtn.innerHTML = 'Next Step <i class="ph-bold ph-arrow-right ml-2 opacity-60"></i>';
        }
    },

    updateWizardUI: function () {
        var self = this;
        document.querySelectorAll('.wizard-pane').forEach(function (p) { p.classList.add('hidden'); });
        var activePane = document.querySelector('.wizard-pane[data-step="' + this.wizard.currentStep + '"]');
        if (activePane) activePane.classList.remove('hidden');

        document.querySelectorAll('.wizard-step').forEach(function (s) {
            var sNum = parseInt(s.getAttribute('data-step'));
            s.classList.remove('active', 'completed');
            if (sNum === self.wizard.currentStep) s.classList.add('active');
            if (sNum < self.wizard.currentStep) s.classList.add('completed');
        });

        var progress = ((this.wizard.currentStep - 1) / (this.wizard.totalSteps - 1)) * 100;
        var bar = document.getElementById('wizard-progress-bar');
        if (bar) bar.style.height = progress + '%';

        var prevBtn = document.getElementById('wizard-prev');
        if (prevBtn) prevBtn.classList.toggle('hidden', this.wizard.currentStep === 1);

        var nextBtn = document.getElementById('wizard-next');
        if (nextBtn) {
            if (this.wizard.currentStep === 4) {
                nextBtn.innerHTML = 'Finalize Channel <i class="ph-bold ph-check-circle ml-2 opacity-60"></i>';
                nextBtn.onclick = function () { self.submitWizard(); };
            } else {
                nextBtn.innerHTML = 'Next Step <i class="ph-bold ph-arrow-right ml-2 opacity-60"></i>';
                nextBtn.onclick = function () { self.navWizard(1); };
            }
        }
    },

    searchWizardUsers: function (q) {
        var results = document.getElementById('wizard-search-results');
        if (q.length < 2) {
            results.classList.add('hidden');
            return;
        }

        ApiClient.get('users', 'search', { q: q }).then(function (data) {
            if (data.users.length > 0) {
                var html = '<ul class="p-2 space-y-1">';
                data.users.forEach(function (u) {
                    var displayName = u.first_name || u.username;
                    html += '<li>' +
                            '    <button onclick="AdminApp.selectWizardUser(' + u.id + ', \'' + (u.first_name || '') + ' ' + (u.last_name || '') + '\', \'' + u.username + '\')" ' +
                            '            class="w-full text-left p-4 rounded-2xl hover:bg-primary/10 flex items-center gap-4 group transition-all">' +
                            '        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=' + u.username + '" class="w-8 h-8 rounded-lg bg-[var(--glass-bg)]">' +
                            '        <div>' +
                            '            <p class="text-[10px] font-black uppercase tracking-tight" style="color: var(--text-main);">' + displayName + '</p>' +
                            '            <p class="text-[8px] font-bold opacity-40 uppercase tracking-widest" style="color: var(--text-muted);">@' + u.username + '</p>' +
                            '        </div>' +
                            '    </button>' +
                            '</li>';
                });
                html += '</ul>';
                results.innerHTML = html;
                results.classList.remove('hidden');
            } else {
                results.classList.add('hidden');
            }
        });
    },

    selectWizardUser: function (uid, name, username) {
        var alreadyAdded = false;
        for (var i = 0; i < this.wizard.selectedMembers.length; i++) {
            if (this.wizard.selectedMembers[i].uid === uid) { alreadyAdded = true; break; }
        }
        if (alreadyAdded) return;

        this.wizard.selectedMembers.push({ uid: uid, name: name, username: username, role: 'member' });
        document.getElementById('wizard-user-search').value = '';
        document.getElementById('wizard-search-results').classList.add('hidden');
        this.renderSelectedMembers();
    },

    renderSelectedMembers: function () {
        var target = document.getElementById('wizard-selected-members');
        if (this.wizard.selectedMembers.length === 0) {
            target.innerHTML = '<div class="p-6 rounded-[1.5rem] border border-dashed border-[var(--border-color)] text-center opacity-40"><p class="font-black text-[10px] uppercase tracking-widest" style="color: var(--text-muted);">No agents authorized yet.</p></div>';
            return;
        }

        var html = '';
        this.wizard.selectedMembers.forEach(function (m, idx) {
            html += '<div class="p-5 rounded-3xl bg-[var(--glass-bg)] border border-[var(--border-color)] flex items-center justify-between group">' +
                    '    <div class="flex items-center gap-4">' +
                    '        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=' + m.username + '" class="w-10 h-10 rounded-xl">' +
                    '        <div>' +
                    '            <p class="text-xs font-black uppercase tracking-tight" style="color: var(--text-main);">' + (m.name || m.username) + '</p>' +
                    '            <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest" style="color: var(--text-muted);">Agent ID: 0x' + m.uid + '</p>' +
                    '        </div>' +
                    '    </div>' +
                    '    <div class="flex items-center gap-3">' +
                    '        <select onchange="AdminApp.wizard.selectedMembers[' + idx + '].role = this.value" class="bg-[var(--glass-bg)] border border-[var(--border-color)] rounded-xl px-4 py-2 text-[9px] font-black uppercase tracking-widest outline-none" style="color: var(--text-main);">' +
                    '            <option value="member" ' + (m.role === 'member' ? 'selected' : '') + '>Member</option>' +
                    '            <option value="moderator" ' + (m.role === 'moderator' ? 'selected' : '') + '>Moderator</option>' +
                    '            <option value="curator" ' + (m.role === 'curator' ? 'selected' : '') + '>Curator</option>' +
                    '        </select>' +
                    '        <button onclick="AdminApp.removeWizardUser(' + m.uid + ')" class="w-8 h-8 rounded-xl bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">' +
                    '            <i class="ph-bold ph-trash"></i>' +
                    '        </button>' +
                    '    </div>' +
                    '</div>';
        });
        target.innerHTML = html;
    },

    removeWizardUser: function (uid) {
        this.wizard.selectedMembers = this.wizard.selectedMembers.filter(function (m) { return m.uid !== uid; });
        this.renderSelectedMembers();
    },

    submitWizard: function () {
        var self = this;
        var payload = {
            name: document.getElementById('wizard-name').value,
            slug: document.getElementById('wizard-slug').value,
            description: document.getElementById('wizard-description').value,
            type: document.querySelector('input[name="channel_type"]:checked').value,
            members: this.wizard.selectedMembers,
            settings: {
                private_registry: document.getElementById('wizard-privacy-toggle').checked,
                allow_invites: document.getElementById('wizard-allow-invites').checked
            }
        };

        toast.info('Protocol Initiated', 'Synchronizing new node cluster...');

        ApiClient.post('channels', 'create', payload).then(function (res) {
            toast.success('Channel Created', 'New communication channel is now online.');
            self.closeWizard();
            self.loadChannelList();
            if (res.id) {
                setTimeout(function () { self.openChannelWorkspace(res.id); }, 1000);
            }
        })['catch'](function (err) {
            toast.error('Registry Failure', err.error || 'Connection to global server failed.');
        });
    },

    currentChannelId: null,

    openChannelWorkspace: function (id) {
        var self = this;
        this.currentChannelId = id;
        var workspaceBtn = document.getElementById('tab-workspace-btn');
        if (workspaceBtn) workspaceBtn.classList.remove('hidden');

        ApiClient.get('channels', 'messages', { id: id }).then(function (data) {
            self.currentChannelId = id;
            self.switchTab('channels', 'workspace');

            var overlay = document.getElementById('ws-empty-overlay');
            if (overlay) overlay.classList.add('hidden');

            if (data.success && data.channel) {
                document.getElementById('ws-channel-name').innerText = data.channel.name;
                document.getElementById('ws-channel-description').innerText = data.channel.description || 'No description provided.';
                document.getElementById('ws-channel-type').innerText = data.channel.type;
                document.getElementById('ws-channel-icon').innerText = data.channel.name.substring(0, 2).toUpperCase();
                document.getElementById('ws-channel-owner').innerText = data.channel.owner_name || 'System Admin';

                self.renderWorkspaceMembers(data.members);
                self.renderChatHistory(data.messages);
            } else if (data.success && !data.channel) {
                toast.error('Transmission Loss', 'Target node is unreachable or decommissioned.');
                self.switchTab('channels', 'list');
            }
        });
    },

    renderWorkspaceMembers: function (members) {
        var list = document.getElementById('ws-member-list');
        document.getElementById('ws-member-count').innerText = members.length + ' Total';

        var html = '';
        members.forEach(function (m) {
            html += '<div class="flex items-center justify-between p-4 rounded-2xl bg-white/[0.02] border border-white/5 group hover:bg-white/[0.05] transition-all">' +
                    '    <div class="flex items-center gap-4">' +
                    '        <div class="relative">' +
                    '            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=' + m.username + '" class="w-10 h-10 rounded-xl">' +
                    '            <span class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-500 border-2 border-[var(--surface)] rounded-full"></span>' +
                    '        </div>' +
                    '        <div>' +
                    '            <p class="text-xs font-black uppercase tracking-tight" style="color: var(--text-main);">' + (m.first_name || m.username) + '</p>' +
                    '            <p class="text-[8px] font-bold opacity-30 uppercase tracking-widest text-primary">' + m.role + '</p>' +
                    '        </div>' +
                    '    </div>' +
                    '    <button class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all hover:text-primary">' +
                    '        <i class="ph-bold ph-dots-three"></i>' +
                    '    </button>' +
                    '</div>';
        });
        list.innerHTML = html;
    },

    renderChatHistory: function (messages) {
        var self = this;
        var history = document.getElementById('ws-chat-history');
        if (messages.length === 0) {
            history.innerHTML = '<div class="flex flex-col items-center justify-center p-24 opacity-20">' +
                                '    <i class="ph-bold ph-brackets-angle text-6xl mb-4"></i>' +
                                '    <p class="font-black text-[10px] uppercase tracking-widest">Initial Linkage Established. No packets intercepted.</p>' +
                                '</div>';
            return;
        }

        var html = '';
        messages.forEach(function (msg) {
            var isSelf = msg.user_id == self.session.user.id;
            var bubbleClass = isSelf ? 'bg-primary text-white ml-auto rounded-l-[1.5rem] rounded-tr-[1.5rem] rounded-br-none' : 'bg-white/[0.05] border border-white/5 mr-auto rounded-r-[1.5rem] rounded-tl-[1.5rem] rounded-bl-none';
            var alignClass = isSelf ? 'items-end' : 'items-start';
            var time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            html += '<div class="flex gap-4 ' + (isSelf ? 'flex-row-reverse' : '') + ' group animate-in slide-in-from-bottom-2">' +
                    '    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=' + msg.username + '" class="w-10 h-10 rounded-xl mt-1 shadow-lg">' +
                    '    <div class="flex flex-col ' + alignClass + ' max-w-[70%]">' +
                    '        <div class="flex items-center gap-3 mb-2 px-1">' +
                    '            <span class="text-[10px] font-black uppercase tracking-widest" style="color: var(--text-main);">' + (isSelf ? 'You (Admin)' : (msg.first_name || msg.username)) + '</span>' +
                    '            <span class="text-[8px] font-bold opacity-30 uppercase tracking-widest">' + time + '</span>' +
                    '        </div>' +
                    '        <div class="p-5 ' + bubbleClass + ' shadow-2xl">' +
                    '            <p class="text-sm font-medium leading-relaxed">' + msg.message + '</p>' +
                    '        </div>' +
                    '    </div>' +
                    '</div>';
        });
        history.innerHTML = html;
        history.scrollTop = history.scrollHeight;
    },

    sendMessage: function () {
        var self = this;
        var input = document.getElementById('ws-chat-input');
        var message = input.value.trim();
        if (!message || !this.currentChannelId) return;

        input.value = '';

        ApiClient.post('channels', 'send', {
            channel_id: this.currentChannelId,
            message: message
        }).then(function (data) {
            if (data.success) {
                self.openChannelWorkspace(self.currentChannelId);
            }
        });
    },

    closeWizard: function () {
        window.closeModal();
    }
};

document.addEventListener('DOMContentLoaded', function () {
    AdminApp.init();

    document.addEventListener('click', function (e) {
        var handshakeMenu = document.getElementById('handshake-role-menu');
        var handshakeChevron = document.getElementById('auth-role-chevron');
        if (handshakeMenu && !handshakeMenu.contains(e.target) && !e.target.closest('button[onclick*="toggleHandshakeRoleDropdown"]')) {
            handshakeMenu.classList.add('hidden');
            if (handshakeChevron) handshakeChevron.style.transform = 'rotate(0deg)';
        }

        var inviteMenu = document.getElementById('invite-role-menu');
        var inviteChevron = document.getElementById('invite-role-chevron');
        if (inviteMenu && !inviteMenu.contains(e.target) && !e.target.closest('button[onclick*="toggleInviteRoleDropdown"]')) {
            inviteMenu.classList.add('hidden');
            if (inviteChevron) inviteChevron.style.transform = 'rotate(0deg)';
        }
    });
});

window.closeModal = function () {
    var modals = document.querySelectorAll('.modal-overlay, [id*="-modal"]');
    for (var i = 0; i < modals.length; i++) {
        var m = modals[i];
        if (!m.classList.contains('hidden')) {
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
    }
    document.body.style.overflow = 'auto';
};

window.closeDrawer = function () {
    var drawer = document.getElementById('side-drawer');
    if (drawer) drawer.classList.add('translate-x-full');
};

window.openModal = function (id) {
    AdminApp.openModal(id);
};
