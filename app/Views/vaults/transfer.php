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
            Transferir entre caixinhas
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Movimente valores diretamente de uma caixinha para outra, mantendo o saldo total inalterado.
        </p>
    </div>

    <div class="card p-5 sm:p-6 lg:p-8">
        <?php if (count($vaults) < 2): ?>
            <div class="text-center py-8">
                <p class="font-medium text-gray-700 dark:text-gray-300">
                    Você precisa de pelo menos duas caixinhas para fazer uma transferência.
                </p>
                <a href="<?= base_url('/vaults/create') ?>"
                    class="btn-primary mt-4 inline-flex px-5 py-2.5 text-sm">Criar nova caixinha</a>
            </div>
        <?php else: ?>
            <form action="<?= base_url('/vaults/transfer') ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="from_vault_id"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Caixinha de origem <span class="text-red-500">*</span>
                        </label>
                        <select id="from_vault_id" name="from_vault_id" required class="input-base">
                            <option value="">Selecione...</option>
                            <?php foreach ($vaults as $vault): ?>
                                <option value="<?= esc($vault['id']) ?>"
                                    <?= old('from_vault_id') == $vault['id'] ? 'selected' : '' ?>>
                                    <?= esc($vault['name']) ?> — Saldo:
                                    R$ <?= number_format($vault['balance'] ?? 0, 2, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="to_vault_id"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Caixinha de destino <span class="text-red-500">*</span>
                        </label>
                        <select id="to_vault_id" name="to_vault_id" required class="input-base">
                            <option value="">Selecione...</option>
                            <?php foreach ($vaults as $vault): ?>
                                <option value="<?= esc($vault['id']) ?>"
                                    <?= old('to_vault_id') == $vault['id'] ? 'selected' : '' ?>>
                                    <?= esc($vault['name']) ?> — Saldo:
                                    R$ <?= number_format($vault['balance'] ?? 0, 2, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Valor a transferir <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01" required
                            value="<?= esc(old('amount')) ?>" placeholder="R$0,00" class="input-base pl-11">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        O saldo da caixinha de origem será reduzido e o da caixinha de destino será aumentado na mesma
                        quantia.
                    </p>
                </div>

                <div
                    class="flex flex-col sm:flex-row justify-end gap-3 pt-4 sm:pt-6 border-t border-gray-200 dark:border-gray-700/80">
                    <a href="<?= base_url('/vaults') ?>"
                        class="btn-secondary order-2 sm:order-1 px-6 py-3 text-center">Cancelar</a>
                    <button type="submit" class="btn-primary order-1 sm:order-2 px-6 py-3">
                        Confirmar transferência
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

