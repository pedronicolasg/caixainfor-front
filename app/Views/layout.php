<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'INFORMÁTICA 3 - Caixa 2026') ?></title>
    <link rel="manifest" href="<?= base_url('manifest.json') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/app.css') ?>">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" as="style"
        crossorigin onload="this.onload=null;this.rel='stylesheet'">
    <link rel="shortcut icon" href="<?= base_url('assets/images/logo/logo.svg') ?>" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?= base_url('assets/images/icons/apple-touch-icon.png') ?>" />


    <!-- SEO -->
    <meta name="description"
        content="Site oficial da turma de Informática 3 (2024-2026) da EEEP Professora Elsa Maria Porto Costa Lima para gerenciamento de caixa.">
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
        (function () {
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

<body class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <?php
    $showNavbar = isset($showNavbar) ? $showNavbar : (session()->get('access_token') ? true : false);
    if ($showNavbar):
        ?>
        <header
            class="lg:hidden fixed top-0 left-0 right-0 z-50 h-16 flex items-center justify-between px-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
            <a href="<?= base_url('/dashboard') ?>" class="flex items-center gap-3 min-w-0 py-2">
                <div
                    class="w-10 h-10 rounded-xl bg-primary/10 dark:bg-primary/20 flex items-center justify-center shrink-0 p-2">
                    <img src="<?= base_url('assets/images/logo/logo.svg') ?>" alt="CaixaInf Logo"
                        class="w-full h-full object-contain">
                </div>
                <span class="text-lg font-bold font-display text-gray-900 dark:text-white truncate">Caixa 2026</span>
            </a>
            <div class="flex items-center gap-2 flex-shrink-0">
                <button id="themeToggleMobile" type="button"
                    class="p-2.5 rounded-xl text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    aria-label="Alternar tema">
                    <i id="themeIconMobile" class="fa-solid fa-moon text-lg leading-none" aria-hidden="true"></i>
                </button>
                <button id="mobileMenuBtn" type="button"
                    class="p-2.5 rounded-xl text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors font-medium"
                    aria-label="Abrir menu">
                    <i class="fa-solid fa-bars text-xl leading-none" aria-hidden="true"></i>
                </button>
            </div>
        </header>

        <aside id="sidebar"
            class="sidebar fixed inset-y-0 left-0 z-40 w-72 flex flex-col transform -translate-x-full transition-transform duration-300 ease-out lg:translate-x-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 shadow-xl lg:shadow-none">
            <div class="flex flex-col h-full min-h-0">
                <div class="shrink-0 px-5 pt-6 pb-4 border-b border-gray-100 dark:border-gray-700/80">
                    <a href="<?= base_url('/dashboard') ?>" class="flex items-center gap-3 min-w-0 group">
                        <div
                            class="w-11 h-11 rounded-xl bg-primary flex items-center justify-center shrink-0 p-2.5 shadow-lg shadow-primary/25 group-hover:shadow-primary/30 transition-shadow">
                            <img src="<?= base_url('assets/images/logo/logow.svg') ?>" alt="CaixaInf Logo"
                                class="w-full h-full object-contain">
                        </div>
                        <div class="min-w-0">
                            <span class="block text-lg font-bold font-display text-gray-900 dark:text-white truncate">Caixa
                                2026</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400 truncate">Informática 3</span>
                        </div>
                    </a>
                </div>

                <nav class="flex-1 px-4 py-6 overflow-y-auto" aria-label="Navegação principal">
                    <p class="px-4 mb-3 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        Menu</p>
                    <ul class="space-y-1.5">
                        <li>
                            <a href="<?= base_url('/dashboard') ?>"
                                class="sidebar-nav-link flex items-center gap-3 rounded-xl font-medium transition-all duration-200 px-4 py-3 <?= uri_string() === 'dashboard' ? 'bg-blue-50 dark:bg-blue-900/25 text-primary dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                                <span
                                    class="nav-icon w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-lg <?= uri_string() === 'dashboard' ? 'bg-primary/15 dark:bg-blue-500/25 text-primary dark:text-blue-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' ?>">
                                    <i class="fa-solid fa-house leading-none" aria-hidden="true"></i>
                                </span>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('/transactions') ?>"
                                class="sidebar-nav-link flex items-center gap-3 rounded-xl font-medium transition-all duration-200 px-4 py-3 <?= strpos(uri_string(), 'transactions') !== false ? 'bg-blue-50 dark:bg-blue-900/25 text-primary dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
                                <span
                                    class="nav-icon w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-lg <?= strpos(uri_string(), 'transactions') !== false ? 'bg-primary/15 dark:bg-blue-500/25 text-primary dark:text-blue-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' ?>">
                                    <i class="fa-solid fa-receipt leading-none" aria-hidden="true"></i>
                                </span>
                                <span>Transações</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="shrink-0 p-4 pt-0 space-y-3">
                    <div
                        class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600/50">
                        <?php
                        $userEmail = session()->get('user_email');
                        if ($userEmail):
                            $gravatarUrl = gravatar_url($userEmail, 64, 'identicon');
                            $userInitial = strtoupper(substr($userEmail, 0, 1));
                            ?>
                            <div class="relative shrink-0">
                                <img src="<?= esc($gravatarUrl) ?>" alt="Avatar de <?= esc($userEmail) ?>"
                                    class="w-10 h-10 rounded-xl object-cover ring-2 ring-white dark:ring-gray-600 shadow-sm"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white text-sm font-bold ring-2 ring-white dark:ring-gray-600 shadow-sm"
                                    style="display: none;">
                                    <?= esc($userInitial) ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div
                                class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white text-sm font-bold ring-2 ring-white dark:ring-gray-600 shadow-sm shrink-0">
                                U</div>
                        <?php endif; ?>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                <?= esc(explode('@', $userEmail ?? 'Usuário')[0]) ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Minha conta</p>
                        </div>
                        <button id="themeToggle" type="button"
                            class="p-2.5 rounded-lg text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-blue-400 hover:bg-gray-200/80 dark:hover:bg-gray-600 transition-colors shrink-0"
                            aria-label="Alternar tema">
                            <i id="themeIcon" class="fa-solid fa-moon text-lg leading-none" aria-hidden="true"></i>
                        </button>
                    </div>
                    <a href="<?= base_url('/auth/logout') ?>"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 border border-red-100 dark:border-red-800/50 transition-colors">
                        <span
                            class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-right-from-bracket text-lg leading-none" aria-hidden="true"></i>
                        </span>
                        <span>Sair</span>
                    </a>
                </div>
            </div>
        </aside>

        <div id="overlay"
            class="fixed inset-0 z-30 bg-black/50 opacity-0 invisible transition-opacity duration-300 lg:hidden"
            aria-hidden="true"></div>

        <div class="lg:pl-72 flex flex-col flex-1 min-h-screen">
            <div class="fixed top-20 lg:top-6 right-4 lg:right-8 z-50 flex flex-col gap-3 max-w-md w-[calc(100%-2rem)] lg:w-full"
                role="region" aria-label="Notificações">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 shadow-lg animate-slide-in-right"
                        role="alert">
                        <i class="fa-solid fa-circle-check text-xl text-emerald-500 dark:text-emerald-400 shrink-0"
                            aria-hidden="true"></i>
                        <p class="font-medium text-sm"><?= esc(session()->getFlashdata('success')) ?></p>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 shadow-lg animate-slide-in-right"
                        role="alert">
                        <i class="fa-solid fa-circle-xmark text-xl text-red-500 dark:text-red-400 shrink-0"
                            aria-hidden="true"></i>
                        <p class="font-medium text-sm"><?= esc(session()->getFlashdata('error')) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <main class="flex-1 pt-14 lg:pt-0">
                <div class="content-width px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    <?= $this->renderSection('content') ?>
                </div>
            </main>

            <footer class="mt-auto border-t border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800">
                <div class="content-width px-4 py-5 sm:px-6 lg:px-8">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <img src="<?= base_url('assets/images/logo/logo.svg') ?>" alt="CaixaInf Logo" class="w-7 h-7">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                © 2026 <a href="https://linktr.ee/pedronicolasg" target="_blank" rel="noopener noreferrer"
                                    class="font-medium text-primary dark:text-blue-400 hover:underline">Pedro Nícolas Gomes
                                    de Souza</a>. Licenciado sob <a
                                    href="https://github.com/pedronicolasg/caixainfor-front/blob/main/LICENSE"
                                    target="_blank" rel="noopener noreferrer"
                                    class="font-medium text-primary dark:text-blue-400 hover:underline">MIT</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    <?php else: ?>
        <div class="fixed top-4 right-4 z-50 flex flex-col gap-3 max-w-md w-[calc(100%-2rem)]" role="region"
            aria-label="Notificações">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 shadow-lg animate-slide-in-right"
                    role="alert">
                    <i class="fa-solid fa-circle-check text-xl text-emerald-500 dark:text-emerald-400 shrink-0"
                        aria-hidden="true"></i>
                    <p class="font-medium text-sm"><?= esc(session()->getFlashdata('success')) ?></p>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 shadow-lg animate-slide-in-right"
                    role="alert">
                    <i class="fa-solid fa-circle-xmark text-xl text-red-500 dark:text-red-400 shrink-0" aria-hidden="true"></i>
                    <p class="font-medium text-sm"><?= esc(session()->getFlashdata('error')) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <main class="flex-1 flex items-center justify-center px-4 py-8">
            <div class="w-full max-w-md">
                <?= $this->renderSection('content') ?>
            </div>
        </main>

        <footer class="mt-auto w-full border-t border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800">
            <div class="px-4 py-5">
                <div class="flex flex-col items-center justify-center gap-2 text-center">
                    <div class="flex items-center gap-2">
                        <img src="<?= base_url('assets/images/logo/logo.svg') ?>" alt="CaixaInf Logo" class="w-7 h-7">
                        <span class="text-base font-bold font-display text-gray-900 dark:text-white">Caixa 2026</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        © 2026 <a href="https://linktr.ee/pedronicolasg" target="_blank" rel="noopener noreferrer"
                            class="font-medium text-primary dark:text-blue-400 hover:underline">Pedro Nícolas Gomes de
                            Souza</a>. Licenciado sob <a
                            href="https://github.com/pedronicolasg/caixainfor-front/blob/main/LICENSE" target="_blank"
                            rel="noopener noreferrer"
                            class="font-medium text-primary dark:text-blue-400 hover:underline">MIT</a>.
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