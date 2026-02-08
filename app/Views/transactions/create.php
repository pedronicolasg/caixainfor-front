<?= $this->extend('layout') ?>

<?php $this->setVar('showNavbar', true); ?>

<?= $this->section('content') ?>

<div class="max-w-3xl mx-auto animate-fade-in">
    <div class="mb-6 sm:mb-8">
        <a href="<?= base_url('/transactions') ?>"
            class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors mb-4">
            <i class="fa-solid fa-arrow-left text-base" aria-hidden="true"></i>
            Voltar
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold font-display text-gray-900 dark:text-white tracking-tight">Nova
            Transação</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Adicione uma nova transação financeira</p>
    </div>

    <div class="card p-5 sm:p-6 lg:p-8">
        <form action="<?= base_url('/transactions') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nome
                        <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" required value="<?= esc(old('name')) ?>"
                        placeholder="Ex: João Silva" class="input-base">
                </div>
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Título
                        <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" required value="<?= esc(old('title')) ?>"
                        placeholder="Ex: Pagamento de mensalidade" class="input-base">
                </div>
            </div>

            <div>
                <label for="description"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Descrição</label>
                <textarea id="description" name="description" rows="3" placeholder="Descrição adicional da transação..."
                    class="input-base resize-none"><?= esc(old('description')) ?></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo
                        <span class="text-red-500">*</span></label>
                    <select id="type" name="type" required class="input-base">
                        <option value="">Selecione...</option>
                        <option value="income" <?= old('type') === 'income' ? 'selected' : '' ?>>Entrada</option>
                        <option value="outcome" <?= old('type') === 'outcome' ? 'selected' : '' ?>>Saída</option>
                    </select>
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Valor
                        <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01" required
                            value="<?= esc(old('amount')) ?>" placeholder="R$0.00" class="input-base pl-11">
                    </div>
                </div>
                <div>
                    <label for="date"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Data</label>
                    <input type="datetime-local" id="date" name="date" value="<?= esc(old('date')) ?>"
                        class="input-base">
                </div>
            </div>

            <div
                class="flex flex-col sm:flex-row justify-end gap-3 pt-4 sm:pt-6 border-t border-gray-200 dark:border-gray-700/80">
                <a href="<?= base_url('/transactions') ?>"
                    class="btn-secondary order-2 sm:order-1 px-6 py-3 text-center">Cancelar</a>
                <button type="submit" class="btn-primary order-1 sm:order-2 px-6 py-3">Criar Transação</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>