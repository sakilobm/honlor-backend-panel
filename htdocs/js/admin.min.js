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
        const page = params.get('current_page') || 'dashboard';

        if (page === 'dashboard') {
            this.initDashboard();
        } else if (page === 'ads') {
            this.initAdsManager();
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

    loadAdList: function() {
        const tbody = document.getElementById('ads-table-body');
        if (!tbody) return;

        ApiClient.get('ads', 'list').then(ads => {
            tbody.innerHTML = '';
            
            // Update badge count if exists
            const badge = document.getElementById('ads-count-badge');
            if (badge) badge.innerText = ads.length + ' Total';

            ads.forEach(ad => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-white/5 transition-colors group';
                
                const status = ad.status || 'Active';
                const badgeClass = status === 'Active' ? 'badge-success' : (status === 'Paused' ? 'badge-warning' : 'badge-neutral');
                const ctr = ad.impressions > 0 ? ((ad.clicks / ad.impressions) * 100).toFixed(1) : '0.0';

                tr.innerHTML = `
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                <i class="ph ph-megaphone-simple text-xl"></i>
                            </div>
                            <div>
                                <p class="font-bold text-sm text-white">${ad.name}</p>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">CAM-${ad.id}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5"><span class="${badgeClass}">${status}</span></td>
                    <td class="px-6 py-5 text-sm font-bold text-gray-300">$${parseFloat(ad.budget).toLocaleString()}</td>
                    <td class="px-6 py-5 text-center"><span class="text-sm font-bold text-white">${ctr}%</span></td>
                    <td class="px-6 py-5 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="p-2 hover:bg-primary/10 hover:text-primary rounded-xl transition-all text-gray-500" title="Edit"><i class="ph ph-pencil-simple text-xl"></i></button>
                            <button class="p-2 hover:bg-orange-500/10 hover:text-orange-400 rounded-xl transition-all text-gray-500" title="Pause"><i class="ph ph-pause-circle text-xl"></i></button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }).catch(err => {
            console.error(err);
        });
    }
};

document.addEventListener('DOMContentLoaded', () => AdminApp.init());
