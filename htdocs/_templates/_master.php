<?php use Aether\Session; ?>
<!doctype html>
<html lang="en">
<head>
    <?php Session::loadTemplate('core/_head'); ?>
</head>

<body class="selection:bg-primary/30 selection:text-primary overflow-x-hidden">
    <!-- Custom Ball Cursor (GSAP) -->
    <div id="ball"></div>

    <?php Session::loadTemplate('core/_nav'); ?>

    <main id="main-content" class="min-h-screen">
        <?php
        if (isset($content)) {
            echo $content;
        } else {
            Session::loadTemplate(Session::currentScript());
        }
        ?>
    </main>

    <?php Session::loadTemplate('core/_footer'); ?>
    <div class="toast-panel" id="toast-container"></div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/toastv3.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/ball.js"></script>
    <script src="<?= get_config('base_path') ?>assets/js/apis.js"></script>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const isLight = html.classList.toggle('light');
            const theme = isLight ? 'light' : 'dark';
            localStorage.setItem('app-theme', theme);
            localStorage.setItem('admin-theme', theme); // Sync with admin
            
            // Update theme icon if exists
            const icon = document.getElementById('theme-icon');
            if (icon) {
                if (isLight) {
                    icon.classList.replace('ph-moon', 'ph-sun');
                } else {
                    icon.classList.replace('ph-sun', 'ph-moon');
                }
            }
        }
    </script>
</body>
</html>
