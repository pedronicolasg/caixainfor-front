<?= $this->extend('layout') ?>

<?php $this->setVar('showNavbar', true); ?>

<?= $this->section('content') ?>

<div class="section-spacing animate-fade-in">
    <div class="card p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold font-display text-gray-900 dark:text-white tracking-tight">
                    Caixinhas</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Organize o saldo em caixinhas para metas específicas sem alterar o total geral.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <a href="<?= base_url('/vaults/transfer') ?>"
                    class="btn-secondary px-4 py-2.5 text-sm inline-flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-left" aria-hidden="true"></i>
                    Transferir entre caixinhas
                </a>
                <a href="<?= base_url('/vaults/create') ?>"
                    class="btn-primary px-4 py-2.5 text-sm inline-flex items-center justify-center gap-2">
                    <i class="fa-solid fa-piggy-bank" aria-hidden="true"></i>
                    Nova caixinha
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        <div
            class="card-elevated rounded-2xl overflow-hidden border-primary/20 dark:border-blue-900/50 bg-gradient-to-br from-blue-50/80 to-white dark:from-blue-900/15 dark:to-gray-800 animate-fade-in stagger-1">
            <div class="p-5 sm:p-6">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Saldo total</p>
                <p class="mt-2 text-2xl sm:text-3xl font-bold font-display text-gray-900 dark:text-white">
                    R$ <?= number_format($summary['balance'] ?? 0, 2, ',', '.') ?>
                </p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Soma de todas as entradas menos saídas (externas)
                </p>
            </div>
        </div>

        <div
            class="card-elevated rounded-2xl overflow-hidden border-emerald-200 dark:border-emerald-900/50 bg-gradient-to-br from-emerald-50/80 to-white dark:from-emerald-900/15 dark:to-gray-800 animate-fade-in stagger-2">
            <div class="p-5 sm:p-6">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Saldo nas caixinhas</p>
                <p class="mt-2 text-2xl sm:text-3xl font-bold font-display text-emerald-600 dark:text-emerald-400">
                    R$ <?= number_format($totalVaultsBalance ?? 0, 2, ',', '.') ?>
                </p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Total já reservado em todas as caixinhas
                </p>
            </div>
        </div>

        <div
            class="card-elevated rounded-2xl overflow-hidden border-gray-200 dark:border-gray-700 bg-gradient-to-br from-gray-50/80 to-white dark:from-gray-900/20 dark:to-gray-800 animate-fade-in stagger-3">
            <div class="p-5 sm:p-6">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Saldo geral disponível</p>
                <p
                    class="mt-2 text-2xl sm:text-3xl font-bold font-display <?= ($generalBalance ?? 0) >= 0 ? 'text-gray-900 dark:text-white' : 'text-red-600 dark:text-red-400' ?>">
                    R$ <?= number_format($generalBalance ?? 0, 2, ',', '.') ?>
                </p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Valor fora das caixinhas, pronto para uso
                </p>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden">
        <?php if (empty($vaults)): ?>
            <div class="p-10 sm:p-12 text-center">
                <div
                    class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-piggy-bank text-2xl text-gray-400 dark:text-gray-500" aria-hidden="true"></i>
                </div>
                <p class="font-medium text-gray-700 dark:text-gray-300">Nenhuma caixinha criada ainda</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Crie caixinhas para reservar dinheiro para metas específicas.
                </p>
                <a href="<?= base_url('/vaults/create') ?>" class="btn-primary mt-5 inline-flex px-5 py-2.5 text-sm">Criar
                    primeira caixinha</a>
            </div>
        <?php else: ?>
            <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-700/80 bg-gray-50/60 dark:bg-gray-800/60">
                <h2 class="text-lg font-bold font-display text-gray-900 dark:text-white">Minhas caixinhas</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    Visualize o saldo de cada caixinha e mova valores quando precisar.
                </p>
            </div>
            <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">
                <?php foreach ($vaults as $vault): ?>
                    <?php
                    $balance = (float) ($vault['balance'] ?? 0);
                    $goal = isset($vault['goal']) ? (float) $vault['goal'] : null;
                    $progress = $goal && $goal > 0 ? min(100, max(0, ($balance / $goal) * 100)) : null;
                    ?>
                    <div class="card-elevated rounded-2xl overflow-hidden flex flex-col cursor-pointer hover:shadow-lg transition-shadow"
                        onclick="window.location='<?= base_url('/vaults/' . $vault['id']) ?>'">
                        <?php if (!empty($vault['image'])): ?>
                            <div class="aspect-[3/2] w-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                                <img src="<?= esc($vault['image']) ?>" alt="<?= esc($vault['name']) ?>"
                                    class="w-full h-full object-cover object-center">
                            </div>
                        <?php else: ?>
                            <?php
                            $hour = (int) date('G');

                            $period = match (true) {
                                $hour >= 5 && $hour < 12 => 'morning',
                                $hour >= 12 && $hour < 18 => 'afternoon',
                                default => 'night',
                            };
                            ?>

                            <div class="aspect-[3/2] w-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                                <img src="<?= base_url("assets/images/caixinha/{$period}.png") ?>" alt="<?= esc($vault['name']) ?>"
                                    class="w-full h-full object-cover object-center">
                            </div>
                        <?php endif; ?>
                        <div class="p-4 sm:p-5 flex-1 flex flex-col gap-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-white truncate">
                                            <?= esc($vault['name']) ?>
                                        </p>
                                        <?php if (!empty($vault['until'])): ?>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Até <?= date('d/m/Y', strtotime($vault['until'])) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Saldo</p>
                                    <p class="text-lg font-bold font-display text-emerald-600 dark:text-emerald-400">
                                        R$ <?= number_format($balance, 2, ',', '.') ?>
                                    </p>
                                </div>
                            </div>

                            <?php if ($goal): ?>
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-500 dark:text-gray-400">Progresso da meta</span>
                                        <span class="font-medium text-gray-700 dark:text-gray-200">
                                            <?= number_format($progress, 0) ?>%
                                        </span>
                                    </div>
                                    <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                        <div class="h-2 rounded-full bg-primary" style="width: <?= $progress ?>%;"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Meta de R$ <?= number_format($goal, 2, ',', '.') ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($vault['daysRemaining'])): ?>
                                <?php
                                $days = (int) $vault['daysRemaining'];
                                $severity = $vault['deadlineSeverity'] ?? null;
                                $badgeClasses = 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                                if ($severity === 'warning') {
                                    $badgeClasses = 'bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200';
                                } elseif ($severity === 'danger') {
                                    $badgeClasses = 'bg-red-50 text-red-800 dark:bg-red-900/30 dark:text-red-200';
                                } elseif ($severity === 'past') {
                                    $badgeClasses = 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
                                }
                                $label = $days < 0
                                    ? 'Prazo encerrado'
                                    : ($days === 0 ? 'Encerra hoje' : "Faltam {$days} dia" . ($days > 1 ? 's' : ''));
                                ?>
                                <div class="mt-2">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.7rem] font-medium <?= $badgeClasses ?>">
                                        <i class="fa-solid fa-clock mr-1.5 text-[0.6rem]" aria-hidden="true"></i>
                                        <?= esc($label) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div
                            class="px-4 sm:px-5 py-3 border-t border-gray-100 dark:border-gray-700/80 bg-gray-50/60 dark:bg-gray-800/60">
                            <div class="flex flex-wrap gap-2">
                                <a href="<?= base_url('/vaults/' . $vault['id'] . '/deposit') ?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors"
                                    onclick="event.stopPropagation();">
                                    <i class="fa-solid fa-arrow-right-arrow-left" aria-hidden="true"></i>
                                    Guardar
                                </a>
                                <a href="<?= base_url('/vaults/' . $vault['id'] . '/withdraw') ?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors"
                                    onclick="event.stopPropagation();">
                                    <i class="fa-solid fa-arrow-up-from-bracket" aria-hidden="true"></i>
                                    Resgatar
                                </a>
                                <a href="<?= base_url('/vaults/' . $vault['id'] . '/delete') ?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors ml-auto"
                                    onclick="event.stopPropagation();">
                                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>