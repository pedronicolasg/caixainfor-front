<?= $this->extend('layout') ?>

<?php $this->setVar('showNavbar', true); ?>

<?= $this->section('content') ?>

<div class="space-y-6 animate-fade-in">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl sm:text-3xl font-bold font-display text-gray-900 dark:text-white mb-2">Dashboard</h1>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">Bem-vindo(a) de volta! Aqui está o resumo mensal das
                    finanças da turma.</p>
            </div>
            <a href="<?= base_url('/transactions/create') ?>"
                class="inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-primary text-white text-sm sm:text-base font-medium font-display rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                <i class="fa-solid fa-plus mr-2 text-[1.25rem] leading-none" aria-hidden="true"></i>
                <span class="hidden sm:inline">Nova Transação</span>
                <span class="sm:hidden">Nova</span>
            </a>
        </div>
    </div>

    <?php if ($summary): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div
                class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-300 border border-green-100 dark:border-green-800 overflow-hidden group">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid fa-arrow-trend-up text-[2rem] leading-none text-white"
                                aria-hidden="true"></i>
                        </div>
                    </div>
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Entradas</h3>
                    <p class="text-3xl font-bold font-display text-gray-900 dark:text-white">R$
                        <?= number_format($summary['income'] ?? 0, 2, ',', '.') ?>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Total de receitas</p>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-300 border border-red-100 dark:border-red-800 overflow-hidden group">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid fa-arrow-trend-down text-[2rem] leading-none text-white"
                                aria-hidden="true"></i>
                        </div>
                    </div>
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Saídas</h3>
                    <p class="text-3xl font-bold font-display text-gray-900 dark:text-white">R$
                        <?= number_format($summary['outcome'] ?? 0, 2, ',', '.') ?>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Total de despesas</p>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-300 border border-indigo-100 dark:border-indigo-800 overflow-hidden group">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid fa-circle-dollar-to-slot text-[2rem] leading-none text-white"
                                aria-hidden="true"></i>
                        </div>
                    </div>
                    <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Saldo</h3>
                    <p
                        class="text-3xl font-bold font-display <?= ($summary['balance'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' ?>">
                        R$ <?= number_format($summary['balance'] ?? 0, 2, ',', '.') ?>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        <?= ($summary['balance'] ?? 0) >= 0 ? 'Saldo positivo' : 'Saldo negativo' ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold font-display text-gray-900 dark:text-white">Últimas Transações</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Suas transações mais recentes</p>
                    </div>
                    <a href="<?= base_url('/transactions') ?>"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-colors duration-200">
                        Ver todas
                        <i class="fa-solid fa-chevron-right ml-1 text-[1rem] leading-none" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <?php if (empty($recentTransactions)): ?>
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-file-lines text-[2rem] leading-none text-gray-400 dark:text-gray-500" aria-hidden="true"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Nenhuma transação encontrada</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Comece criando sua primeira transação</p>
                    <a href="<?= base_url('/transactions/create') ?>"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-primary text-white font-medium font-display rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        Criar Transação
                    </a>
                </div>
            <?php else: ?>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($recentTransactions as $index => $transaction): ?>
                        <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 <?= $index === 0 ? 'animate-fade-in' : '' ?>"
                            style="animation-delay: <?= $index * 100 ?>ms">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 flex-1 min-w-0">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-10 h-10 rounded-lg flex items-center justify-center <?= $transaction['type'] === 'income' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' ?>">
                                            <?php if ($transaction['type'] === 'income'): ?>
                                                <i class="fa-solid fa-arrow-trend-up text-[1.25rem] leading-none text-green-600 dark:text-green-400"
                                                    aria-hidden="true"></i>
                                            <?php else: ?>
                                                <i class="fa-solid fa-arrow-trend-down text-[1.25rem] leading-none text-red-600 dark:text-red-400"
                                                    aria-hidden="true"></i>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate"><?= esc($transaction['title']) ?>
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?= esc($transaction['name']) ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4 ml-4">
                                    <div class="text-right">
                                        <p
                                            class="text-sm font-bold font-display <?= $transaction['type'] === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' ?>">
                                            <?= $transaction['type'] === 'income' ? '+' : '-' ?> R$
                                            <?= number_format($transaction['amount'], 2, ',', '.') ?>
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= date('d/m/Y', strtotime($transaction['date'])) ?></p>
                                    </div>
                                    <span
                                        class="px-2.5 py-1 text-xs font-semibold rounded-full <?= $transaction['type'] === 'income' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' ?>">
                                        <?= $transaction['type'] === 'income' ? 'Entrada' : 'Saída' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-12 text-center border border-gray-100 dark:border-gray-700">
            <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                <i class="fa-solid fa-arrows-rotate text-[2rem] leading-none text-primary dark:text-blue-400" aria-hidden="true"></i>
            </div>
            <p class="text-gray-600 dark:text-gray-400 font-medium">Carregando dados...</p>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>