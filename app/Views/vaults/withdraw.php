<?= $this->extend('layout') ?>

<?php $this->setVar('showNavbar', true); ?>

<?= $this->section('content') ?>

<div class="max-w-xl mx-auto animate-fade-in">
    <div class="mb-6 sm:mb-8">
        <a href="<?= base_url('/vaults') ?>"
            class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors mb-4">
            <i class="fa-solid fa-arrow-left text-base" aria-hidden="true"></i>
            Voltar
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold font-display text-gray-900 dark:text-white tracking-tight">
            Resgatar da caixinha
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Mova dinheiro da caixinha
            <strong class="text-gray-900 dark:text-gray-100"><?= esc($vault['name']) ?></strong>
            de volta para o saldo geral.
        </p>
    </div>

    <div class="card p-5 sm:p-6 lg:p-8">
        <form action="<?= base_url('/vaults/' . $vault['id'] . '/withdraw') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div class="space-y-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">Saldo atual da caixinha</p>
                <p class="text-2xl font-bold font-display text-emerald-600 dark:text-emerald-400">
                    R$ <?= number_format($vault['balance'] ?? 0, 2, ',', '.') ?>
                </p>
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Valor a resgatar <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" id="amount" name="amount" step="0.01" min="0.01" required
                        value="<?= esc(old('amount')) ?>" placeholder="R$0,00" class="input-base pl-11">
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Este valor será movido desta caixinha para o saldo geral.
                </p>
            </div>

            <div
                class="flex flex-col sm:flex-row justify-end gap-3 pt-4 sm:pt-6 border-t border-gray-200 dark:border-gray-700/80">
                <a href="<?= base_url('/vaults') ?>"
                    class="btn-secondary order-2 sm:order-1 px-6 py-3 text-center">Cancelar</a>
                <button type="submit" class="btn-primary order-1 sm:order-2 px-6 py-3">
                    Confirmar resgate
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

