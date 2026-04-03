/**
 * Honlor Admin App
 * ================
 * Handles interactive logic for Dashboard and Ads Manager.
 * Optimized for High-Fidelity UI Overhaul (v10).
 */

const AdminApp = {
    chart: null,
    currentRange: 7,

    init: function() {
        console.log("AdminApp Initializing...");
        const params = new URLSearchParams(window.location.search);
        const page = params.get('page') || 'dashboard';

        if (page === 'dashboard') {
            this.initDashboard();
        } else if (page === 'ads') {
            this.initAdsManager();
        }
        
        // Handle notifications dropdown close on click outside
        window.onclick = (event) => {
            if (!event.target.matches('.ph-bell') && !event.target.closest('.notification-pane')) {
                const pane = document.getElementById('notification-pane');
                if (pane) pane.classList.add('hidden');
            }
        };
    },

    /**
     * Navigation Logic
     */
    switchSection: function(tag) {
        // In the current PHP framework, we use URL parameters for routing.
        // We navigate to ?page=tag to ensure the PHP template is loaded.
        window.location.search = `?page=${tag}`;
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

            this.renderGrowthChart(data.growth_data);
        }).catch(err => {
            console.error(err);
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

                ApiClient.post('ads', 'create', payload).then(res => {
                    toast.success('Success', res.message);
                    if (window.closeModal) closeModal();
                    form.reset();
                    this.loadAdList();
                }).catch(err => {
                    toast.error('Error', err.error || 'Failed to create campaign.');
                });
            };
        }
    },

    /**
     * Settings Logic
     */
    toggleSetting: function(key, isChecked) {
        const value = isChecked ? 'on' : 'off';
        
        // Use the new update API
        fetch('/api/settings/update.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ key: key, value: value })
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                toast.error('Failed', data.error);
            } else {
                toast.success('Updated', `Setting '${key}' is now ${value}.`);
            }
        })
        .catch(err => {
            toast.error('System Error', 'Could not communicate with the settings server.');
        });
    },

    /**
     * Notification Logic
     */
    toggleNotifications: function() {
        // For now, toggle a simple alert or a placeholder pane
        toast.info('Activity Feed', 'You have 3 new moderation alerts and 1 ad system update.');
    },

    /**
     * Global Modals & Drawers
     */
    openModal: function(type, identity = null) {
        const container = document.getElementById('global-modal-container');
        const target = document.getElementById('modal-content-target');
        if (!container || !target) return;

        let html = '';
        switch(type) {
            case 'ban':
                html = `
                    <div class="stat-card max-w-lg w-full bg-[#0a0a0a] border-red-500/20 p-10 animate-in zoom-in duration-300">
                        <div class="w-16 h-16 rounded-3xl bg-red-500/10 flex items-center justify-center text-red-500 mb-6 mx-auto">
                            <i class="ph-bold ph-prohibit text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-center mb-2">Ban Account Activity</h3>
                        <p class="text-sm text-gray-500 text-center mb-8 font-medium leading-relaxed">
                            Are you absolutely sure you want to suspend <span class="text-white font-bold">${identity}</span>? 
                            This will terminate all active sessions and block access immediately.
                        </p>
                        <div class="grid grid-cols-2 gap-4">
                            <button class="btn-secondary !justify-center py-4" onclick="closeModal()">Dismiss</button>
                            <button class="btn-primary !bg-red-500 !shadow-[0_10px_30px_rgba(239,68,68,0.3)] !justify-center py-4" onclick="AdminApp.executeAction('ban', '${identity}')">Confirm Ban</button>
                        </div>
                    </div>
                `;
                break;
            case 'warn':
                html = `
                    <div class="stat-card max-w-lg w-full bg-[#0a0a0a] border-orange-400/20 p-10 animate-in zoom-in duration-300">
                        <div class="w-16 h-16 rounded-3xl bg-orange-400/10 flex items-center justify-center text-orange-400 mb-6 mx-auto">
                            <i class="ph-bold ph-warning-circle text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-center mb-2">Issue Formal Warning</h3>
                        <div class="space-y-4 mb-8 mt-6">
                            <input type="text" placeholder="Reason for warning..." class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm outline-none focus:border-orange-400/50">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <button class="btn-secondary !justify-center py-4" onclick="closeModal()">Dismiss</button>
                            <button class="btn-primary !bg-orange-400 !shadow-[0_10px_30px_rgba(251,146,60,0.3)] !justify-center py-4" onclick="AdminApp.executeAction('warn', '${identity}')">Send Warning</button>
                        </div>
                    </div>
                `;
                break;
        }

        target.innerHTML = html;
        container.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    },

    openDrawer: function(type, identity) {
        const drawer = document.getElementById('side-drawer');
        const target = document.getElementById('drawer-content-target');
        if (!drawer || !target) return;

        target.innerHTML = `
            <div class="p-8 h-full flex flex-col">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold uppercase tracking-widest text-primary">Record Inspection</h3>
                    <button onclick="closeDrawer()" class="p-2 hover:bg-white/10 rounded-xl transition-all">
                        <i class="ph ph-x text-2xl"></i>
                    </button>
                </div>
                <!-- Profile/Ad Details -->
                <div class="space-y-6 flex-grow">
                     <div class="text-center p-6 bg-white/5 rounded-[2.5rem] border border-white/5">
                         <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=${identity}" class="w-24 h-24 rounded-3xl mx-auto mb-4 border-2 border-primary/20">
                         <h4 class="text-2xl font-bold">${identity}</h4>
                         <p class="text-[10px] font-bold text-gray-500 tracking-widest uppercase mt-1">Verified Node</p>
                     </div>
                     
                     <div class="stat-card !p-6 space-y-4">
                         <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-tighter">Status</span>
                            <span class="badge-success">Operational</span>
                         </div>
                         <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-tighter">Last Login</span>
                            <span class="text-xs font-medium text-white">2h 14m ago</span>
                         </div>
                         <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-tighter">Network IP</span>
                            <span class="text-xs font-medium text-white">192.168.1.1</span>
                         </div>
                     </div>
                </div>
                <button class="w-full btn-primary !justify-center py-4">View Full History</button>
            </div>
        `;

        drawer.classList.remove('translate-x-full');
    },

    executeAction: function(action, identity) {
        toast.info('Processing', `Executing ${action} on ${identity}...`);
        setTimeout(() => {
            toast.success('Confirmed', `Action ${action} finalized for ${identity}.`);
            closeModal();
        }, 1200);
    }
};

document.addEventListener('DOMContentLoaded', () => AdminApp.init());
