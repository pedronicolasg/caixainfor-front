<?= $this->extend('layout') ?>

<?php $this->setVar('showNavbar', true); ?>

<?= $this->section('content') ?>

<div class="section-spacing animate-fade-in">
    <div class="card p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold font-display text-gray-900 dark:text-white tracking-tight">
                    Dashboard</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Bem-vindo(a) de volta! Resumo mensal das
                    finanças da turma.</p>
            </div>
            <a href="<?= base_url('/transactions/create') ?>"
                class="btn-primary shrink-0 px-5 py-3 text-sm sm:text-base">
                <i class="fa-solid fa-plus mr-2 text-base" aria-hidden="true"></i>
                <span class="hidden sm:inline">Nova Transação</span>
                <span class="sm:hidden">Nova</span>
            </a>
        </div>
    </div>

    <?php if ($summary): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            <div
                class="card-elevated rounded-2xl overflow-hidden border-emerald-100 dark:border-emerald-900/50 bg-gradient-to-br from-emerald-50/80 to-white dark:from-emerald-900/15 dark:to-gray-800 animate-fade-in stagger-1">
                <div class="p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-emerald-500 flex items-center justify-center text-white shadow-sm">
                            <i class="fa-solid fa-arrow-trend-up text-xl" aria-hidden="true"></i>
                        </div>
                    </div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Entradas</p>
                    <p class="mt-1 text-2xl sm:text-3xl font-bold font-display text-gray-900 dark:text-white">R$
                        <?= number_format($summary['income'] ?? 0, 2, ',', '.') ?></p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Total de receitas</p>
                </div>
            </div>

            <div
                class="card-elevated rounded-2xl overflow-hidden border-red-100 dark:border-red-900/50 bg-gradient-to-br from-red-50/80 to-white dark:from-red-900/15 dark:to-gray-800 animate-fade-in stagger-2">
                <div class="p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-red-500 flex items-center justify-center text-white shadow-sm">
                            <i class="fa-solid fa-arrow-trend-down text-xl" aria-hidden="true"></i>
                        </div>
                    </div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Saídas</p>
                    <p class="mt-1 text-2xl sm:text-3xl font-bold font-display text-gray-900 dark:text-white">R$
                        <?= number_format($summary['outcome'] ?? 0, 2, ',', '.') ?></p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Total de despesas</p>
                </div>
            </div>

            <div
                class="card-elevated rounded-2xl overflow-hidden border-primary/20 dark:border-blue-900/50 bg-gradient-to-br from-blue-50/80 to-white dark:from-blue-900/15 dark:to-gray-800 animate-fade-in stagger-3">
                <div class="p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-primary flex items-center justify-center text-white shadow-sm">
                            <i class="fa-solid fa-circle-dollar-to-slot text-xl" aria-hidden="true"></i>
                        </div>
                    </div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Saldo</p>
                    <p
                        class="mt-1 text-2xl sm:text-3xl font-bold font-display <?= ($summary['balance'] ?? 0) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' ?>">
                        R$ <?= number_format($summary['balance'] ?? 0, 2, ',', '.') ?></p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        <?= ($summary['balance'] ?? 0) >= 0 ? 'Saldo positivo' : 'Saldo negativo' ?></p>
                </div>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div
                class="px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold font-display text-gray-900 dark:text-white">Últimas Transações</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Suas transações mais recentes</p>
                    </div>
                    <a href="<?= base_url('/transactions') ?>"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-primary dark:text-blue-400 hover:underline shrink-0">
                        Ver todas
                        <i class="fa-solid fa-chevron-right text-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <?php if (empty($recentTransactions)): ?>
                <div class="p-10 sm:p-12 text-center">
                    <div
                        class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-file-lines text-2xl text-gray-400 dark:text-gray-500" aria-hidden="true"></i>
                    </div>
                    <p class="font-medium text-gray-700 dark:text-gray-300">Nenhuma transação encontrada</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Comece criando sua primeira transação</p>
                    <a href="<?= base_url('/transactions/create') ?>"
                        class="btn-primary mt-5 inline-flex px-5 py-2.5 text-sm">Criar Transação</a>
                </div>
            <?php else: ?>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700/80" role="list">
                    <?php foreach ($recentTransactions as $index => $transaction): ?>
                        <li class="px-5 sm:px-6 py-4 hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors <?= $index === 0 ? 'animate-fade-in' : '' ?>"
                            style="<?= $index > 0 ? 'animation-delay: ' . ($index * 60) . 'ms' : '' ?>">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4 min-w-0 flex-1">
                                    <div
                                        class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 <?= $transaction['type'] === 'income' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' ?>">
                                        <?php if ($transaction['type'] === 'income'): ?>
                                            <i class="fa-solid fa-arrow-trend-up text-lg" aria-hidden="true"></i>
                                        <?php else: ?>
                                            <i class="fa-solid fa-arrow-trend-down text-lg" aria-hidden="true"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-white truncate">
                                            <?= esc($transaction['title']) ?></p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                            <?= esc($transaction['name']) ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <div class="text-right">
                                        <p
                                            class="font-bold font-display <?= $transaction['type'] === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' ?>">
                                            <?= $transaction['type'] === 'income' ? '+' : '-' ?> R$
                                            <?= number_format($transaction['amount'], 2, ',', '.') ?></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            <?= date('d/m/Y', strtotime($transaction['date'])) ?></p>
                                    </div>
                                    <span
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg <?= $transaction['type'] === 'income' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' ?>"><?= $transaction['type'] === 'income' ? 'Entrada' : 'Saída' ?></span>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="card p-12 text-center">
            <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-arrows-rotate text-2xl text-primary dark:text-blue-400 animate-pulse"
                    aria-hidden="true"></i>
            </div>
            <p class="font-medium text-gray-600 dark:text-gray-400">Carregando dados...</p>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>