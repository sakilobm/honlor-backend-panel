<div class="min-h-[80vh] flex items-center justify-center p-6">
    <div class="max-w-xl w-full text-center space-y-8 animate-in fade-in zoom-in duration-700">
        <!-- Shield Animation -->
        <div class="relative inline-block">
            <div class="absolute inset-0 bg-primary/20 blur-3xl animate-pulse rounded-full"></div>
            <div class="relative w-32 h-32 bg-white/5 border border-primary/20 rounded-[2.5rem] flex items-center justify-center text-primary shadow-[0_0_50px_rgba(124,106,255,0.2)]">
                <i class="ph-bold ph-shield-warning text-6xl"></i>
            </div>
            <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-amber-500 rounded-2xl flex items-center justify-center text-white border-4 border-black border-opacity-20 shadow-lg">
                <i class="ph-bold ph-lock-key-open text-xl"></i>
            </div>
        </div>

        <div class="space-y-4">
            <h1 class="text-4xl font-black tracking-tight uppercase">Identity Clearance <span class="text-primary italic">Required</span></h1>
            <p class="text-gray-400 font-medium leading-relaxed max-w-md mx-auto">
                Your account is currently in a <span class="text-white font-bold italic underline decoration-primary decoration-4">Zero-Trust restricted state</span>. An Identity Administrator must assign you to a Security Cluster before access can be granted.
            </p>
        </div>

        <div class="glass-card !p-8 border-primary/10 bg-primary/[0.02] space-y-6">
            <div class="flex items-center gap-4 text-left">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary text-xl">
                    <i class="ph-bold ph-fingerprint"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Protocol Status</p>
                    <p class="font-bold text-white tracking-tight">Handshake Pending Migration</p>
                </div>
            </div>

            <div class="h-1 bg-white/5 rounded-full overflow-hidden">
                <div class="h-full bg-primary animate-[shimmer_2s_infinite] w-1/3 rounded-full shadow-[0_0_10px_#7c6aff]"></div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="/admin?logout" class="btn-secondary !justify-center flex-1 py-4 text-xs font-black uppercase tracking-widest">
                    Terminate Session
                </a>
                <button onclick="window.location.reload()" class="btn-primary !justify-center flex-1 py-4 text-xs font-black uppercase tracking-widest shadow-xl shadow-primary/20">
                    Verify Status
                    <i class="ph-bold ph-arrow-clockwise ml-2"></i>
                </button>
            </div>
        </div>

        <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em]">Code: AETHER_NULL_IDENTITY_CLEARANCE_L0</p>
    </div>
</div>

<style>
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(300%); }
}
</style>
