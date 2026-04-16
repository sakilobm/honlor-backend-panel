<div class="relative z-10 max-w-xl w-full text-center space-y-8 py-20 px-6 animate-in fade-in zoom-in duration-700">
    <!-- Shield Animation -->
    <div class="relative inline-block">
        <div class="absolute inset-0 bg-primary/20 blur-3xl animate-pulse rounded-full"></div>
        <div
            class="relative w-32 h-32 bg-white/5 border border-primary/20 rounded-[2.5rem] flex items-center justify-center text-primary shadow-[0_0_50px_rgba(124,106,255,0.2)]">
            <i class="ph-bold ph-shield-warning text-6xl"></i>
        </div>
        <div
            class="absolute -bottom-2 -right-2 w-10 h-10 bg-amber-500 rounded-2xl flex items-center justify-center text-white border-4 border-black border-opacity-20 shadow-lg">
            <i class="ph-bold ph-lock-key-open text-xl"></i>
        </div>
    </div>

    <div class="space-y-4">
        <h1
            class="text-4xl font-black tracking-tight uppercase text-slate-900 border-b-4 border-primary/20 pb-2 inline-block">
            Identity Clearance <span class="text-primary italic">Required</span></h1>
        <p class="text-slate-600 font-bold leading-relaxed max-w-md mx-auto">
            Your account is currently in a <span
                class="text-primary font-black italic underline decoration-primary/30 decoration-4">Zero-Trust
                restricted state</span>. An Identity Administrator must assign you to a Security Cluster before access
            can be granted.
        </p>
    </div>

    <div
        class="bg-white/80 border border-primary/10 backdrop-blur-3xl rounded-[3rem] p-10 shadow-[0_20px_50px_rgba(0,0,0,0.05)] space-y-8 relative overflow-hidden ring-1 ring-black/5">
        <!-- Refresh Icon in Top Right -->
        <button onclick="AdminApp.checkClearance()"
            class="absolute top-8 right-8 p-3 rounded-2xl bg-gray-50 text-slate-400 hover:text-primary transition-all group border border-gray-100 shadow-sm"
            title="Verify Migration Status">
            <i class="ph-bold ph-arrow-clockwise text-xl group-active:rotate-180 transition-transform duration-500"></i>
        </button>

        <div class="flex items-center gap-5 text-left bg-gray-50/50 p-6 rounded-[2rem] border border-gray-100">
            <div
                class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary text-2xl shadow-inner">
                <i class="ph-bold ph-fingerprint"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-400 mb-1">Protocol Status</p>
                <p class="font-black text-slate-800 tracking-tight text-lg">Handshake Pending Migration</p>
            </div>
        </div>

        <div class="h-2 bg-gray-100 rounded-full overflow-hidden shadow-inner">
            <div class="h-full bg-primary animate-[shimmer_2s_infinite] w-1/3 rounded-full shadow-[0_0_15px_#7c6aff]">
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 pt-4">
            <?php $u = \Aether\Session::getUser(); ?>
            <?php if ((int) $u->getRequestPending() === 0): ?>
                <button id="request-btn" onclick="AdminApp.requestRoleHandshake()"
                    class="btn-primary !justify-center flex-1 py-4 text-xs font-black uppercase tracking-widest shadow-xl shadow-primary/20">
                    <i class="ph-bold ph-handshake mr-2"></i>
                    Request Identity Handshake
                </button>
            <?php else: ?>
                <button disabled
                    class="bg-white/5 border border-white/10 text-gray-500 !justify-center flex-1 py-4 text-xs font-black uppercase tracking-widest rounded-2xl cursor-not-allowed">
                    <i class="ph-bold ph-clock-counter-clockwise mr-2"></i>
                    Request Sent: Awaiting Audit
                </button>
            <?php endif; ?>

            <a href="/admin?logout"
                class="btn-secondary !justify-center flex-1 py-4 text-xs font-black uppercase tracking-widest">
                Terminate Session
            </a>
        </div>
    </div>

    <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em]">Code: AETHER_NULL_IDENTITY_CLEARANCE_L0
    </p>
</div>

<style>
    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(300%);
        }
    }
</style>