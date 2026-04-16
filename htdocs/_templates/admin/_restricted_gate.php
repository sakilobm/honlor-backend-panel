<?php use Aether\Session; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php Session::loadTemplate('admin/_head'); ?>
    <style>
        body { background-color: #ffffff !important; }
        .light body { background-color: #ffffff !important; }
    </style>
</head>
<body class="min-h-screen transition-colors duration-500 overflow-y-auto relative bg-[#F8FAFC]">
    <!-- Animated Security Background -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/5 blur-[120px] rounded-full animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-400/5 blur-[120px] rounded-full animate-pulse" style="animation-delay: 1s;"></div>
    </div>
    
    <script>
        window.isRestricted = true;
        window.adminId = '<?php echo Session::getUser()->id; ?>';
    </script>

    <main class="min-h-screen w-full flex items-center justify-center relative z-10 p-6">
        <div id="content-container" class="w-full flex justify-center">
            <?php Session::loadTemplate('admin/clearance_pending', ['user' => Session::getUser()]); ?>
        </div>
    </main>

    <?php Session::loadTemplate('admin/_toastv3'); ?>

    <!-- Infrastructure Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/toastv3.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/apis.js?v=<?= time() ?>"></script>
    
    <!-- Application Logic (Scoped for the gate) -->
    <script src="<?= get_config('base_path') ?>js/admin.js?v=<?= time() ?>"></script>
</body>
</html>
