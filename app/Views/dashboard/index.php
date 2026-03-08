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
        <div class="grid grid-cols-1 gap-4 sm:gap-5">
            <div
                class="card-elevated rounded-2xl overflow-hidden border-primary/30 dark:border-blue-900/60 bg-gradient-to-br from-blue-50 to-white dark:from-blue-900/40 dark:to-gray-900 animate-fade-in">
                <div class="p-5 sm:p-6 lg:p-7">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Saldo atual geral</p>
                            <p
                                class="mt-1 text-3xl sm:text-4xl font-bold font-display <?= ($generalBalance ?? 0) >= 0 ? 'text-gray-900 dark:text-white' : 'text-red-600 dark:text-red-400' ?>">
                                R$ <?= number_format($generalBalance ?? ($summary['balance'] ?? 0), 2, ',', '.') ?>
                            </p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Dinheiro disponível fora das caixinhas (saldo livre para uso imediato).
                            </p>
                        </div>
                        <div
                            class="hidden sm:flex w-12 h-12 rounded-xl bg-primary flex-shrink-0 items-center justify-center text-white shadow-md">
                            <i class="fa-solid fa-wallet text-xl" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div class="flex items-center justify-between py-2 border-t border-gray-100 dark:border-gray-700/80">
                            <span class="text-gray-500 dark:text-gray-400">Saldo em caixinhas</span>
                            <span class="font-semibold text-emerald-700 dark:text-emerald-300">
                                R$ <?= number_format($totalVaultsBalance ?? 0, 2, ',', '.') ?>
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-t border-gray-100 dark:border-gray-700/80">
                            <span class="text-gray-500 dark:text-gray-400">Entradas no período</span>
                            <span class="font-semibold text-emerald-700 dark:text-emerald-300">
                                R$ <?= number_format($summary['income'] ?? 0, 2, ',', '.') ?>
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-t border-gray-100 dark:border-gray-700/80">
                            <span class="text-gray-500 dark:text-gray-400">Saídas no período</span>
                            <span class="font-semibold text-red-600 dark:text-red-300">
                                R$ <?= number_format($summary['outcome'] ?? 0, 2, ',', '.') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($topVaults)): ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5 mt-4">
                <div class="lg:col-span-3 card overflow-hidden">
                    <div
                        class="px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700/80 bg-gray-50/60 dark:bg-gray-800/60">
                        <h2 class="text-lg font-bold font-display text-gray-900 dark:text-white">Caixinhas em destaque</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            As três principais caixinhas em valor reservado neste momento.
                        </p>
                    </div>
                    <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
                        <?php foreach ($topVaults as $vault): ?>
                            <?php $balance = (float) ($vault['balance'] ?? 0); ?>
                            <div class="card-elevated rounded-2xl overflow-hidden">
                                <div class="p-4 sm:p-5 space-y-2">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-primary/10 dark:bg-blue-900/30 flex items-center justify-center text-primary dark:text-blue-400">
                                            <i class="fa-solid fa-piggy-bank text-base" aria-hidden="true"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                                <?= esc($vault['name']) ?>
                                            </p>
                                            <p class="text-[0.7rem] text-gray-500 dark:text-gray-400">
                                                Saldo: R$ <?= number_format($balance, 2, ',', '.') ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php if (!empty($vault['goal'])): ?>
                                        <?php
                                        $goal = (float) $vault['goal'];
                                        $progress = $goal > 0 ? min(100, max(0, ($balance / $goal) * 100)) : null;
                                        ?>
                                        <?php if ($progress !== null): ?>
                                            <div class="mt-2 space-y-1">
                                                <div class="flex items-center justify-between text-[0.7rem]">
                                                    <span class="text-gray-500 dark:text-gray-400">Progresso</span>
                                                    <span class="font-semibold text-gray-800 dark:text-gray-100">
                                                        <?= number_format($progress, 0) ?>%
                                                    </span>
                                                </div>
                                                <div class="h-1.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                                    <div class="h-1.5 rounded-full bg-primary"
                                                        style="width: <?= $progress ?>%;"></div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="card overflow-hidden mt-4">
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
                        <?php
                        $uiType = $transaction['ui_type'] ?? $transaction['type'] ?? '';
                        $isInternal = in_array($uiType, ['deposit', 'withdraw', 'transfer'], true);
                        ?>
                        <li class="px-5 sm:px-6 py-4 hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors <?= $index === 0 ? 'animate-fade-in' : '' ?>"
                            style="<?= $index > 0 ? 'animation-delay: ' . ($index * 60) . 'ms' : '' ?>">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4 min-w-0 flex-1">
                                    <div
                                        class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                                        <?= $uiType === 'income'
                                            ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'
                                            : ($uiType === 'outcome'
                                                ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'
                                                : 'bg-primary/10 dark:bg-blue-900/30 text-primary dark:text-blue-400') ?>">
                                        <?php if ($isInternal): ?>
                                            <i class="fa-solid fa-piggy-bank text-lg" aria-hidden="true"></i>
                                        <?php elseif ($uiType === 'income'): ?>
                                            <i class="fa-solid fa-arrow-trend-up text-lg" aria-hidden="true"></i>
                                        <?php else: ?>
                                            <i class="fa-solid fa-arrow-trend-down text-lg" aria-hidden="true"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-white truncate">
                                            <?= esc($transaction['ui_title'] ?? $transaction['title']) ?></p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                            <?= esc($transaction['ui_name'] ?? $transaction['name']) ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <div class="text-right">
                                        <?php
                                        $amountClasses = 'text-sky-700 dark:text-sky-300';
                                        $prefix = '';
                                        if ($uiType === 'income') {
                                            $amountClasses = 'text-emerald-600 dark:text-emerald-400';
                                            $prefix = '+ ';
                                        } elseif ($uiType === 'outcome') {
                                            $amountClasses = 'text-red-600 dark:text-red-400';
                                            $prefix = '- ';
                                        }
                                        ?>
                                        <p class="font-bold font-display <?= $amountClasses ?>">
                                            <?= $prefix ?>R$
                                            <?= number_format($transaction['amount'], 2, ',', '.') ?></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            <?= date('d/m/Y', strtotime($transaction['date'])) ?></p>
                                    </div>
                                    <?php
                                    $badgeLabel = 'Outro';
                                    $badgeClasses = 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
                                    if ($uiType === 'income') {
                                        $badgeLabel = 'Entrada';
                                        $badgeClasses = 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300';
                                    } elseif ($uiType === 'outcome') {
                                        $badgeLabel = 'Saída';
                                        $badgeClasses = 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300';
                                    } elseif ($uiType === 'deposit') {
                                        $badgeLabel = 'Depósito';
                                        $badgeClasses = 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300';
                                    } elseif ($uiType === 'withdraw') {
                                        $badgeLabel = 'Resgate';
                                        $badgeClasses = 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300';
                                    } elseif ($uiType === 'transfer') {
                                        $badgeLabel = 'Transferência';
                                        $badgeClasses = 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300';
                                    }
                                    ?>
                                    <span
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg <?= $badgeClasses ?>"><?= $badgeLabel ?></span>
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