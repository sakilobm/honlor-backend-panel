<?php use Aether\Session; ?>
<aside id="sidebar" class="w-72 border-r transition-all duration-300 md:relative fixed inset-y-0 left-0 z-50 -translate-x-full md:translate-x-0" style="background-color: var(--surface); border-color: var(--border-color);">
    <div class="p-8 pb-4">
        <!-- Brand -->
        <div class="flex items-center gap-3 mb-8">
            <div class="p-2 bg-primary rounded-xl shrink-0">
                <i class="ph-bold ph-hexagon text-white text-xl"></i>
            </div>
            <div>
                <h1 class="font-bold text-xl tracking-tight">Honlor</h1>
                <p class="text-[10px] uppercase font-bold tracking-widest leading-tight" style="color: var(--text-muted);">Admin workspace</p>
            </div>
        </div>
        
        <!-- Primary Nav -->
        <nav class="space-y-1">
            <a href="/admin?current_page=dashboard" class="nav-item <?= Session::getCurrentPageIdentifier() === 'dashboard' ? 'active' : '' ?>">
                <i class="ph-bold ph-squares-four text-xl"></i>
                <span>Dashboard</span>
            </a>
            <a href="/admin?current_page=users" class="nav-item <?= Session::getCurrentPageIdentifier() === 'users' ? 'active' : '' ?>">
                <i class="ph-bold ph-users text-xl"></i>
                <span>Users</span>
            </a>
            <a href="/admin?current_page=messages" class="nav-item <?= Session::getCurrentPageIdentifier() === 'messages' ? 'active' : '' ?>">
                <i class="ph-bold ph-chats-circle text-xl"></i>
                <span>Messages</span>
            </a>
            <a href="/admin?current_page=channels" class="nav-item <?= Session::getCurrentPageIdentifier() === 'channels' ? 'active' : '' ?>">
                <i class="ph-bold ph-graph text-xl"></i>
                <span>Channels</span>
            </a>
        </nav>
    </div>
    
    <!-- Management Section -->
    <div class="p-8 py-4 flex-grow overflow-y-auto">
        <h3 class="px-4 mb-4 text-[10px] uppercase font-bold tracking-widest" style="color: var(--text-muted);">Management</h3>
        <nav class="space-y-1">
            <a href="/admin?current_page=ads" class="nav-item <?= Session::getCurrentPageIdentifier() === 'ads' ? 'active' : '' ?>">
                <i class="ph-bold ph-megaphone text-xl"></i>
                <span>Ads Manager</span>
            </a>
            <a href="/admin?current_page=reports" class="nav-item <?= Session::getCurrentPageIdentifier() === 'reports' ? 'active' : '' ?>">
                <i class="ph-bold ph-shield-warning text-xl"></i>
                <span>Reports</span>
            </a>
            <a href="/admin?current_page=analytics" class="nav-item <?= Session::getCurrentPageIdentifier() === 'analytics' ? 'active' : '' ?>">
                <i class="ph-bold ph-activity text-xl"></i>
                <span>Analytics</span>
            </a>
        </nav>
    </div>
    
    <!-- Footer / Profile -->
    <div class="p-8 border-t" style="border-color: var(--border-color);">
        <div class="flex items-center gap-3 p-4 rounded-3xl cursor-pointer transition-all hover:brightness-110" style="background-color: var(--glass-bg);">
            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Marcus" class="w-10 h-10 rounded-xl bg-primary/20" alt="Avatar">
            <div class="overflow-hidden">
                <p class="font-bold text-sm truncate"><?php $u = Session::getUser(); echo $u ? htmlspecialchars($u->getUsername()) : 'Admin'; ?></p>
                <a href="/admin?logout=1" class="text-[10px] uppercase font-bold text-red-500 hover:text-red-400">Logout Session</a>
            </div>
        </div>
    </div>
</aside>
