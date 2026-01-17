<?= $this->extend('layout') ?>

<?php $this->setVar('showNavbar', true); ?>

<?= $this->section('content') ?>

<div class="max-w-3xl mx-auto animate-fade-in">
    <div class="mb-4 sm:mb-6">
        <div class="flex items-center space-x-3 mb-4">
            <a href="<?= base_url('/transactions') ?>"
                class="p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                <i class="fa-solid fa-arrow-left text-[1.25rem] sm:text-[1.5rem] leading-none text-gray-600"
                    aria-hidden="true"></i>
            </a>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold font-display text-gray-900">Editar Transação</h1>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Atualize os dados da transação</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-6 lg:p-8">
        <form action="<?= base_url('/transactions/' . $transaction['id']) ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PATCH">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nome
                    </label>
                    <input type="text" id="name" name="name" value="<?= esc(old('name', $transaction['name'])) ?>"
                        placeholder="Ex: João Silva"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200">
                </div>

                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                        Título
                    </label>
                    <input type="text" id="title" name="title" value="<?= esc(old('title', $transaction['title'])) ?>"
                        placeholder="Ex: Pagamento de mensalidade"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200">
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    Descrição
                </label>
                <textarea id="description" name="description" rows="3" placeholder="Descrição adicional da transação..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 resize-none"><?= esc(old('description', $transaction['description'] ?? '')) ?></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div>
                    <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tipo
                    </label>
                    <select id="type" name="type"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200">
                        <option value="income" <?= old('type', $transaction['type']) === 'income' ? 'selected' : '' ?>>
                            Entrada</option>
                        <option value="outcome" <?= old('type', $transaction['type']) === 'outcome' ? 'selected' : '' ?>>
                            Saída</option>
                    </select>
                </div>

                <div>
                    <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">
                        Valor
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">R$</span>
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01"
                            value="<?= esc(old('amount', $transaction['amount'])) ?>" placeholder="0.00"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200">
                    </div>
                </div>

                <div>
                    <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">
                        Data
                    </label>
                    <?php
                    $dateValue = old('date', $transaction['date'] ?? '');
                    if ($dateValue) {
                        $dateValue = date('Y-m-d\TH:i', strtotime($dateValue));
                    }
                    ?>
                    <input type="datetime-local" id="date" name="date" value="<?= esc($dateValue) ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200">
                </div>
            </div>

            <div
                class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-4 sm:pt-6 border-t border-gray-200">
                <a href="<?= base_url('/transactions') ?>"
                    class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200 text-center">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-primary text-white font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                    Atualizar Transação
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>