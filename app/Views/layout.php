<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'INFORMÁTICA 3 - Caixa 2026') ?></title>
    <link rel="manifest" href="<?= base_url('manifest.json') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/app.css') ?>">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" as="style" crossorigin onload="this.onload=null;this.rel='stylesheet'">
    <link rel="shortcut icon" href="<?= base_url('assets/images/logo/logo.svg') ?>" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?= base_url('assets/images/icons/apple-touch-icon.png') ?>" />

    
    <!-- SEO -->
    <meta name="description" content="Site oficial da turma de Informática 3 (2024-2026) da EEEP Professora Elsa Maria Porto Costa Lima para gerenciamento de caixa.">
    <meta name="keywords" content="Informática, Caixa, Gerenciamento, Turma">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Pedro Nícolas Gomes de Souza">
    <meta name="application-name" content="INFOR-3 Caixa 2026">
        
    <!-- PWA -->
    <meta name="theme-color" content="#0185c6">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Caixa INFOR-3">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const shouldUseDark = theme === 'dark' || (!theme && prefersDark);
            
            if (shouldUseDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>

<body class="bg-gradient-to-br from-gray-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen flex flex-col">
    <?php
    $showNavbar = isset($showNavbar) ? $showNavbar : (session()->get('access_token') ? true : false);
    if ($showNavbar):
        ?>
        <div class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-800 shadow-md">
            <div class="flex items-center justify-between px-4 py-3">
                <a href="<?= base_url('/dashboard') ?>" class="flex items-center space-x-2">
                    <img src="<?= base_url('assets/images/logo/logo.svg') ?>" alt="CaixaInf Logo" class="w-8 h-8">
                    <span class="text-xl font-bold font-display bg-primary bg-clip-text text-transparent dark:text-white">Caixa 2026</span>
                </a>
                <div class="flex items-center space-x-2">
                    <button id="themeToggleMobile"
                        class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
                        aria-label="Alternar tema">
                        <i id="themeIconMobile" class="fa-solid fa-moon text-[1.25rem] leading-none" aria-hidden="true"></i>
                    </button>
                    <button id="mobileMenuBtn"
                        class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fa-solid fa-bars text-[1.5rem] leading-none" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="sidebar"
            class="fixed inset-y-0 left-0 z-40 w-64 bg-alt transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0">
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-center h-16 px-4 border-b border-primary">
                    <a href="<?= base_url('/dashboard') ?>" class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg p-2">
                            <img src="<?= base_url('assets/images/logo/logow.svg') ?>" alt="CaixaInf Logo"
                                class="w-full h-full object-contain">
                        </div>
                        <span class="text-xl font-bold font-display text-white">Caixa 2026</span>
                    </a>
                </div>

                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <a href="<?= base_url('/dashboard') ?>"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 group <?= uri_string() === 'dashboard' ? 'bg-white/20 shadow-lg' : '' ?>">
                        <i class="fa-solid fa-house text-[1.25rem] leading-none" aria-hidden="true"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <a href="<?= base_url('/transactions') ?>"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 group <?= strpos(uri_string(), 'transactions') !== false ? 'bg-white/20 shadow-lg' : '' ?>">
                        <i class="fa-solid fa-receipt text-[1.25rem] leading-none" aria-hidden="true"></i>
                        <span class="font-medium">Transações</span>
                    </a>
                </nav>

                <div class="px-4 py-4 border-t border-primary">
                    <div class="flex items-center space-x-3 mb-4 px-4 py-2 rounded-lg bg-white/5">
                        <?php
                        $userEmail = session()->get('user_email');
                        if ($userEmail):
                            $gravatarUrl = gravatar_url($userEmail, 64, 'identicon');
                            $userInitial = strtoupper(substr($userEmail, 0, 1));
                            ?>
                            <div class="relative">
                                <img src="<?= esc($gravatarUrl) ?>" alt="Avatar de <?= esc($userEmail) ?>"
                                    class="w-8 h-8 rounded-full border-2 border-white/20 object-cover"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center border-2 border-white/20"
                                    style="display: none;">
                                    <span class="text-white text-sm font-semibold"><?= esc($userInitial) ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div
                                class="w-8 h-8 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center border-2 border-white/20">
                                <span class="text-white text-sm font-semibold">U</span>
                            </div>
                        <?php endif; ?>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">
                                <?= esc(explode('@', $userEmail ?? 'Usuário')[0]) ?>
                            </p>
                        </div>
                        <button id="themeToggle"
                            class="p-2 rounded-lg text-white hover:bg-white/10 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/20"
                            aria-label="Alternar tema">
                            <i id="themeIcon" class="fa-solid fa-moon text-[1.25rem] leading-none" aria-hidden="true"></i>
                        </button>
                    </div>
                    <a href="<?= base_url('/auth/logout') ?>"
                        class="flex items-center space-x-3 px-4 py-3 rounded-lg text-white bg-red-500/20 hover:bg-red-500/30 transition-all duration-200 group">
                        <i class="fa-solid fa-right-from-bracket text-[1.25rem] leading-none" aria-hidden="true"></i>
                        <span class="font-medium">Sair</span>
                    </a>
                </div>
            </div>
        </div>

        <div id="overlay"
            class="fixed inset-0 bg-black/50 dark:bg-black/70 z-30 opacity-0 invisible transition-opacity duration-300 lg:hidden"></div>

        <div class="lg:pl-64 flex flex-col flex-1">
            <div class="fixed top-20 lg:top-4 right-4 z-50 space-y-2 max-w-md w-full px-4 lg:px-0">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 dark:border-green-400 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg shadow-lg animate-slide-in-right"
                        role="alert">
                        <div class="flex items-center">
                            <i class="fa-solid fa-circle-check mr-2 text-[1.25rem] leading-none" aria-hidden="true"></i>
                            <p class="font-medium"><?= esc(session()->getFlashdata('success')) ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 dark:border-red-400 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg shadow-lg animate-slide-in-right"
                        role="alert">
                        <div class="flex items-center">
                            <i class="fa-solid fa-circle-xmark mr-2 text-[1.25rem] leading-none" aria-hidden="true"></i>
                            <p class="font-medium"><?= esc(session()->getFlashdata('error')) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <main class="pt-16 lg:pt-0 flex-1">
                <div class="p-4 lg:p-8">
                    <?= $this->renderSection('content') ?>
                </div>
            </main>

            <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto">
                <div class="px-4 py-6 lg:px-8">
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                        <div class="flex items-center space-x-3">
                            <img src="<?= base_url('assets/images/logo/logo.svg') ?>" alt="CaixaInf Logo" class="w-7 h-7">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                © 2026 <a href="https://linktr.ee/pedronicolasg" target="_blank" rel="noopener noreferrer"
                                    class="text-primary dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium font-display transition-colors duration-200">Pedro
                                    Nícolas Gomes de Souza</a>. Licenciado sob <a href="https://github.com/pedronicolasg/caixainfor-front/blob/main/LICENSE"
                                    target="_blank" rel="noopener noreferrer"
                                    class="text-primary dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium font-display transition-colors duration-200">MIT</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    <?php else: ?>
        <div class="fixed top-4 right-4 z-50 space-y-2 max-w-md w-full px-4">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 dark:border-green-400 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg shadow-lg animate-slide-in-right"
                    role="alert">
                    <div class="flex items-center">
                        <i class="fa-solid fa-circle-check mr-2 text-[1.25rem] leading-none" aria-hidden="true"></i>
                        <p class="font-medium"><?= esc(session()->getFlashdata('success')) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 dark:border-red-400 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg shadow-lg animate-slide-in-right"
                    role="alert">
                    <div class="flex items-center">
                        <i class="fa-solid fa-circle-xmark mr-2 text-[1.25rem] leading-none" aria-hidden="true"></i>
                        <p class="font-medium"><?= esc(session()->getFlashdata('error')) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <main class="flex-1 flex items-center justify-center">
            <div class="w-full max-w-md p-4">
                <?= $this->renderSection('content') ?>
            </div>
        </main>

        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto w-full">
            <div class="px-4 py-6">
                <div class="flex flex-col items-center justify-center space-y-3">
                    <div class="flex items-center space-x-3">
                        <img src="<?= base_url('assets/images/logo/logo.svg') ?>" alt="CaixaInf Logo" class="w-7 h-7">
                        <span class="text-lg font-bold font-display text-gray-900 dark:text-white">Caixa 2026</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                        © 2026 <a href="https://linktr.ee/pedronicolasg" target="_blank" rel="noopener noreferrer"
                            class="text-primary dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium font-display transition-colors duration-200">Pedro
                            Nícolas Gomes de Souza</a>. Licenciado sob <a href="https://github.com/pedronicolasg/caixainfor-front/blob/main/LICENSE"
                            target="_blank" rel="noopener noreferrer"
                            class="text-primary dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium font-display transition-colors duration-200">MIT</a>.
                    </p>
                </div>
            </div>
        </footer>
    <?php endif; ?>

    <script>
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        if (mobileMenuBtn && sidebar && overlay) {
            mobileMenuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('opacity-0');
                overlay.classList.toggle('invisible');
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0');
                overlay.classList.add('invisible');
            });
        }

        function getTheme() {
            const stored = localStorage.getItem('theme');
            if (stored) return stored;
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        function setTheme(theme) {
            localStorage.setItem('theme', theme);
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            updateThemeIcon();
        }

        function toggleTheme() {
            const currentTheme = getTheme();
            setTheme(currentTheme === 'dark' ? 'light' : 'dark');
        }

        function updateThemeIcon() {
            const themeIcon = document.getElementById('themeIcon');
            const themeIconMobile = document.getElementById('themeIconMobile');
            const isDark = document.documentElement.classList.contains('dark');
            const iconClass = isDark 
                ? 'fa-solid fa-sun text-[1.25rem] leading-none' 
                : 'fa-solid fa-moon text-[1.25rem] leading-none';
            
            if (themeIcon) {
                themeIcon.className = iconClass;
            }
            if (themeIconMobile) {
                themeIconMobile.className = iconClass;
            }
        }

        setTheme(getTheme());

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });

        const themeToggle = document.getElementById('themeToggle');
        const themeToggleMobile = document.getElementById('themeToggleMobile');
        if (themeToggle) {
            themeToggle.addEventListener('click', toggleTheme);
        }
        if (themeToggleMobile) {
            themeToggleMobile.addEventListener('click', toggleTheme);
        }

        document.addEventListener('DOMContentLoaded', function () {
            updateThemeIcon();
            
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s, transform 0.5s';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateX(100%)';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>

</html>