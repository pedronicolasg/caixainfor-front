<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="w-full max-w-md mx-auto animate-fade-in">
    <div class="card shadow-xl p-6 sm:p-8">
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-primary text-white shadow-lg mb-5 p-3">
                <img src="<?= base_url('assets/images/logo/logow.svg') ?>" alt="CaixaInf Logo"
                    class="w-full h-full object-contain">
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold font-display text-gray-900 dark:text-white mb-1.5">Bem-vindo de
                volta!</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Entre na sua conta para continuar</p>
        </div>

        <form class="space-y-5" action="<?= base_url('/auth/login') ?>" method="POST">
            <?= csrf_field() ?>

            <div>
                <label for="email"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                <div class="relative">
                    <input id="email" name="email" type="email" required class="input-base pl-11"
                        placeholder="seu@email.com" value="<?= esc(old('email')) ?>">
                </div>
            </div>

            <div>
                <label for="password"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Senha</label>
                <div class="relative">
                    <input id="password" name="password" type="password" required class="input-base pl-11"
                        placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn-primary w-full py-3.5 text-base">
                Entrar
            </button>

            <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                Não tem uma conta?
                <a href="<?= base_url('/auth/signup') ?>"
                    class="font-semibold text-primary dark:text-blue-400 hover:underline">
                    Criar conta
                </a>
            </p>
        </form>
    </div>
</div>
<?= $this->endSection() ?>