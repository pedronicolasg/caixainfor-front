<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="w-full max-w-md mx-auto">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sm:p-8 animate-fade-in">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-primary rounded-2xl shadow-lg mb-4 p-3">
                <img src="<?= base_url('assets/images/logo/logow.svg') ?>" alt="CaixaInf Logo" class="w-full h-full object-contain">
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold font-display text-gray-900 mb-2">Bem-vindo de volta!</h2>
            <p class="text-sm sm:text-base text-gray-600">Entre na sua conta para continuar</p>
        </div>

        <form class="space-y-6" action="<?= base_url('/auth/login') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                    Email
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-at text-[1.25rem] leading-none text-gray-400" aria-hidden="true"></i>
                    </div>
                    <input id="email" name="email" type="email" required 
                           class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200" 
                           placeholder="seu@email.com" value="<?= esc(old('email')) ?>">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                    Senha
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-lock text-[1.25rem] leading-none text-gray-400" aria-hidden="true"></i>
                    </div>
                    <input id="password" name="password" type="password" required 
                           class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200" 
                           placeholder="••••••••">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold font-display text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-700 transform hover:-translate-y-0.5 transition-all duration-200">
                    Entrar
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Não tem uma conta?
                    <a href="<?= base_url('/auth/signup') ?>" class="font-semibold font-display text-primary hover:text-blue-500 transition-colors duration-200">
                        Criar conta
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
