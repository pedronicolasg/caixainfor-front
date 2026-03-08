<?= $this->extend('layout') ?>

<?php $this->setVar('showNavbar', true); ?>

<?= $this->section('content') ?>

<div class="max-w-3xl mx-auto animate-fade-in">
    <div class="mb-6 sm:mb-8">
        <a href="<?= base_url('/vaults') ?>"
            class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors mb-4">
            <i class="fa-solid fa-arrow-left text-base" aria-hidden="true"></i>
            Voltar
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold font-display text-gray-900 dark:text-white tracking-tight">
            Excluir caixinha
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Escolha o que fazer com o saldo e o histórico da caixinha
            <strong class="text-gray-900 dark:text-gray-100"><?= esc($vault['name']) ?></strong>.
        </p>
    </div>

    <div class="card p-5 sm:p-6 lg:p-8 space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Saldo atual da caixinha</p>
                <p class="text-2xl font-bold font-display text-emerald-600 dark:text-emerald-400">
                    R$ <?= number_format($vault['balance'] ?? 0, 2, ',', '.') ?>
                </p>
            </div>
        </div>

        <div
            class="p-4 rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/60 flex gap-3">
            <div
                class="w-9 h-9 rounded-xl bg-red-100 dark:bg-red-900/40 flex items-center justify-center text-red-600 dark:text-red-300 shrink-0">
                <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
            </div>
            <div class="text-sm text-red-800 dark:text-red-200 space-y-1">
                <p class="font-semibold">Atenção! Esta ação não pode ser desfeita.</p>
                <p>
                    Dependendo da opção escolhida, o saldo desta caixinha poderá ser transferido para o saldo geral ou
                    permanentemente descartado.
                </p>
            </div>
        </div>

        <form action="<?= base_url('/vaults/' . $vault['id'] . '/delete') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div class="space-y-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    O que você deseja fazer?
                </label>

                <div class="space-y-3">
                    <label
                        class="flex gap-3 p-3 rounded-2xl border cursor-pointer transition-colors <?= (old('mode', 'move_to_general') === 'move_to_general') ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/10' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/60' ?>">
                        <input type="radio" name="mode" value="move_to_general" class="mt-1"
                            <?= old('mode', 'move_to_general') === 'move_to_general' ? 'checked' : '' ?>>
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                Transferir saldo para o geral
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Todo o saldo da caixinha será movido para o saldo geral e o histórico será preservado.
                            </p>
                        </div>
                    </label>

                    <label
                        class="flex gap-3 p-3 rounded-2xl border cursor-pointer transition-colors <?= (old('mode') === 'destroy') ? 'border-red-400 bg-red-50 dark:bg-red-900/10' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/60' ?>">
                        <input type="radio" name="mode" value="destroy" class="mt-1"
                            <?= old('mode') === 'destroy' ? 'checked' : '' ?>>
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-red-700 dark:text-red-300">
                                Descartar saldo e histórico
                            </p>
                            <p class="text-xs text-red-600 dark:text-red-200">
                                Todas as transações desta caixinha serão removidas e o saldo será perdido
                                permanentemente.
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            <div
                class="flex flex-col sm:flex-row justify-end gap-3 pt-4 sm:pt-6 border-t border-gray-200 dark:border-gray-700/80">
                <a href="<?= base_url('/vaults') ?>"
                    class="btn-secondary order-2 sm:order-1 px-6 py-3 text-center">Cancelar</a>
                <button type="submit"
                    class="order-1 sm:order-2 inline-flex items-center justify-center font-display font-semibold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 rounded-xl shadow-sm hover:shadow transition-all duration-200 px-6 py-3">
                    Confirmar exclusão
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

