<footer class="p-12 border-t mt-32" style="border-color: var(--border-color);">
    <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8 text-sm font-medium opacity-60">
        <div class="text-center md:text-left">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(get_config('project_title', 'Application')) ?>. Built with Precision.</p>
            <p class="text-[10px] mt-1 font-bold uppercase tracking-widest text-primary">Aether Catalyst Framework v12.4</p>
        </div>
        <div class="flex gap-10">
            <a href="#" class="hover:text-primary transition-colors">Privacy Policy</a>
            <a href="#" class="hover:text-primary transition-colors">Terms of Service</a>
            <a href="#" class="hover:text-primary transition-colors">Documentation</a>
        </div>
    </div>
</footer>
