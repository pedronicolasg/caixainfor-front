<?= $this->extend('layout') ?>

<?php $this->setVar('showNavbar', true); ?>

<?= $this->section('content') ?>

<div class="section-spacing animate-fade-in">
    <div class="card overflow-hidden">
        <?php if (!empty($vault['image'])): ?>
            <div class="aspect-video w-full min-h-[200px] sm:min-h-[240px] overflow-hidden bg-gray-100 dark:bg-gray-700">
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

            <div class="aspect-video w-full min-h-[200px] overflow-hidden bg-gray-100 sm:min-h-[240px] dark:bg-gray-700">
                <img src="<?= base_url("assets/images/caixinha/{$period}.png") ?>" alt="<?= esc($vault['name']) ?>"
                    class="h-full w-full object-cover object-center">
            </div>
        <?php endif; ?>

        <div class="p-5 sm:p-6 lg:p-7 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1
                        class="text-2xl sm:text-3xl font-bold font-display text-gray-900 dark:text-white tracking-tight">
                        <?= esc($vault['name']) ?>
                    </h1>
                    <?php if (!empty($vault['description'])): ?>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-2xl">
                            <?= esc($vault['description']) ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($vault['until'])): ?>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Prazo até <?= date('d/m/Y', strtotime($vault['until'])) ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="flex flex-wrap gap-2 sm:gap-3 items-center">
                    <a href="<?= base_url('/vaults/' . $vault['id'] . '/deposit') ?>"
                        class="btn-primary px-4 py-2.5 text-xs sm:text-sm inline-flex items-center gap-2">
                        <i class="fa-solid fa-arrow-right-arrow-left" aria-hidden="true"></i>
                        Guardar
                    </a>
                    <a href="<?= base_url('/vaults/' . $vault['id'] . '/withdraw') ?>"
                        class="btn-secondary px-4 py-2.5 text-xs sm:text-sm inline-flex items-center gap-2">
                        <i class="fa-solid fa-arrow-up-from-bracket" aria-hidden="true"></i>
                        Resgatar
                    </a>
                    <div class="relative" id="vault-more-actions-wrap">
                        <button type="button" id="vault-more-actions-btn" aria-expanded="false" aria-haspopup="true"
                            aria-controls="vault-more-actions-menu"
                            class="btn-secondary px-4 py-2.5 text-xs sm:text-sm inline-flex items-center gap-2">
                            Mais ações
                            <i class="fa-solid fa-chevron-down text-xs transition-transform vault-more-chevron"
                                aria-hidden="true"></i>
                        </button>
                        <div id="vault-more-actions-menu" role="menu" hidden
                            class="absolute right-0 mt-2 w-52 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow-lg py-1 z-20">
                            <a href="<?= base_url('/vaults/' . $vault['id'] . '/edit') ?>" role="menuitem"
                                class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <i class="fa-solid fa-pen-to-square text-gray-500 dark:text-gray-400"
                                    aria-hidden="true"></i>
                                Editar
                            </a>
                            <a href="<?= base_url('/vaults/' . $vault['id'] . '/export/pdf') ?>" role="menuitem"
                                class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <i class="fa-solid fa-file-pdf text-gray-500 dark:text-gray-400" aria-hidden="true"></i>
                                Exportar PDF
                            </a>
                            <a href="<?= base_url('/vaults/' . $vault['id'] . '/delete') ?>" role="menuitem"
                                class="flex items-center gap-3 px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                onclick="return confirm('Tem certeza que deseja gerenciar a exclusão desta caixinha?')">
                                <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                Excluir
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
                <div
                    class="rounded-2xl border border-gray-100 dark:border-gray-700/80 bg-gray-50/60 dark:bg-gray-800/60 p-4 sm:p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Saldo atual
                    </p>
                    <p class="mt-2 text-2xl font-bold font-display text-emerald-600 dark:text-emerald-400">
                        R$ <?= number_format($vault['balance'] ?? 0, 2, ',', '.') ?>
                    </p>
                </div>
                <div
                    class="rounded-2xl border border-gray-100 dark:border-gray-700/80 bg-gray-50/60 dark:bg-gray-800/60 p-4 sm:p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Meta
                    </p>
                    <?php if (!empty($vault['goal'])): ?>
                        <?php
                        $goal = (float) $vault['goal'];
                        $balance = (float) ($vault['balance'] ?? 0);
                        $progress = $goal > 0 ? min(100, max(0, ($balance / $goal) * 100)) : null;
                        ?>
                        <p class="mt-1 text-sm text-gray-700 dark:text-gray-200">
                            R$ <?= number_format($goal, 2, ',', '.') ?>
                        </p>
                        <?php if ($progress !== null): ?>
                            <div class="mt-2 space-y-1">
                                <div class="flex items-center justify-between text-[0.7rem]">
                                    <span class="text-gray-500 dark:text-gray-400">Progresso</span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-100">
                                        <?= number_format($progress, 0) ?>%
                                    </span>
                                </div>
                                <div class="h-1.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                    <div class="h-1.5 rounded-full bg-primary" style="width: <?= $progress ?>%;"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Nenhuma meta definida.
                        </p>
                    <?php endif; ?>
                </div>
                <div
                    class="rounded-2xl border border-gray-100 dark:border-gray-700/80 bg-gray-50/60 dark:bg-gray-800/60 p-4 sm:p-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Prazo
                    </p>
                    <?php if ($daysRemaining !== null): ?>
                        <?php
                        $badgeClasses = 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                        if ($deadlineSeverity === 'warning') {
                            $badgeClasses = 'bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200';
                        } elseif ($deadlineSeverity === 'danger') {
                            $badgeClasses = 'bg-red-50 text-red-800 dark:bg-red-900/30 dark:text-red-200';
                        } elseif ($deadlineSeverity === 'past') {
                            $badgeClasses = 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
                        }
                        $label = $daysRemaining < 0
                            ? 'Prazo encerrado'
                            : ($daysRemaining === 0 ? 'Encerra hoje' : "Faltam {$daysRemaining} dia" . ($daysRemaining > 1 ? 's' : ''));
                        ?>
                        <span
                            class="inline-flex items-center mt-2 px-2.5 py-1 rounded-full text-[0.7rem] font-medium <?= $badgeClasses ?>">
                            <i class="fa-solid fa-clock mr-1.5 text-[0.6rem]" aria-hidden="true"></i>
                            <?= esc($label) ?>
                        </span>
                    <?php else: ?>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Nenhum prazo definido.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4">
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-100 dark:border-gray-700/80 pb-3 mb-3">
                    <div>
                        <h2 class="text-lg font-bold font-display text-gray-900 dark:text-white">
                            Histórico de transações da caixinha
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            Apenas movimentações que impactam esta caixinha.
                        </p>
                    </div>
                </div>

                <?php if (empty($transactions)): ?>
                    <div class="py-8 text-center">
                        <p class="font-medium text-gray-700 dark:text-gray-300">Nenhuma transação encontrada.</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Use os botões acima para começar a movimentar esta caixinha.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="lg:hidden divide-y divide-gray-100 dark:divide-gray-700/80">
                        <?php foreach ($transactions as $transaction): ?>
                            <?php
                            $uiType = $transaction['ui_type'] ?? $transaction['type'] ?? '';
                            $isInternal = in_array($uiType, ['deposit', 'withdraw', 'transfer', 'transfer_in', 'transfer_out'], true);
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
                            } elseif ($uiType === 'transfer_in') {
                                $badgeLabel = 'Crédito';
                                $badgeClasses = 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300';
                            } elseif ($uiType === 'transfer_out') {
                                $badgeLabel = 'Débito';
                                $badgeClasses = 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300';
                            }
                            $amountClasses = 'text-sky-700 dark:text-sky-300';
                            $prefix = '';
                            if ($uiType === 'income' || $uiType === 'transfer_in') {
                                $amountClasses = 'text-emerald-600 dark:text-emerald-400';
                                $prefix = '+ ';
                            } elseif ($uiType === 'outcome' || $uiType === 'transfer_out') {
                                $amountClasses = 'text-red-600 dark:text-red-400';
                                $prefix = '- ';
                            }
                            $displayTitle = $transaction['ui_title'] ?? $transaction['title'] ?? '';
                            $displayName = $transaction['ui_name'] ?? $transaction['name'] ?? '';
                            $fromVaultId = $transaction['from_vault_id']
                                ?? $transaction['source_vault_id']
                                ?? $transaction['origin_vault_id']
                                ?? null;
                            $toVaultId = $transaction['to_vault_id']
                                ?? $transaction['target_vault_id']
                                ?? $transaction['destination_vault_id']
                                ?? null;
                            $fromVaultName = $transaction['from_vault_name']
                                ?? $transaction['source_vault_name']
                                ?? $transaction['origin_vault_name']
                                ?? (($fromVaultId !== null && isset($vaultsById[$fromVaultId]))
                                    ? ($vaultsById[$fromVaultId]['name'] ?? null)
                                    : null);
                            $toVaultName = $transaction['to_vault_name']
                                ?? $transaction['target_vault_name']
                                ?? $transaction['destination_vault_name']
                                ?? (($toVaultId !== null && isset($vaultsById[$toVaultId]))
                                    ? ($vaultsById[$toVaultId]['name'] ?? null)
                                    : null);
                            $currentVaultName = $vault['name'] ?? null;

                            if (in_array($uiType, ['transfer', 'transfer_in', 'transfer_out'], true)) {
                                if ($uiType === 'transfer_in') {
                                    if (!empty($fromVaultName)) {
                                        $displayTitle = 'Transferência de ' . $fromVaultName;
                                    } elseif (!empty($displayTitle) && stripos($displayTitle, 'Vault ') === 0) {
                                        $displayTitle = 'Transferência recebida';
                                    }

                                    if (!empty($currentVaultName)) {
                                        $displayName = $currentVaultName;
                                    }
                                } elseif ($uiType === 'transfer_out') {
                                    if (!empty($toVaultName)) {
                                        $displayTitle = 'Transferência para ' . $toVaultName;
                                    } elseif (!empty($displayTitle) && stripos($displayTitle, 'Vault ') === 0) {
                                        $displayTitle = 'Transferência enviada';
                                    }

                                    if (!empty($currentVaultName)) {
                                        $displayName = $currentVaultName;
                                    }
                                } else {
                                    if (!empty($fromVaultName) && !empty($toVaultName)) {
                                        $displayTitle = $fromVaultName . ' → ' . $toVaultName;
                                    } elseif (!empty($fromVaultName)) {
                                        $displayTitle = 'Saída de ' . $fromVaultName;
                                    } elseif (!empty($toVaultName)) {
                                        $displayTitle = 'Entrada em ' . $toVaultName;
                                    }

                                    if (empty($displayName) || stripos($displayName, 'vault') !== false) {
                                        $displayName = 'Transferência entre caixinhas';
                                    }
                                }
                            }

                            $vaultLabel = null;
                            if (in_array($uiType, ['transfer', 'transfer_in', 'transfer_out'], true)) {
                                if (!empty($fromVaultName) && !empty($toVaultName)) {
                                    $vaultLabel = $fromVaultName . ' → ' . $toVaultName;
                                } elseif (!empty($fromVaultName)) {
                                    $vaultLabel = $fromVaultName . ' → —';
                                } elseif (!empty($toVaultName)) {
                                    $vaultLabel = '— → ' . $toVaultName;
                                } else {
                                    $vaultLabel = 'Transferência entre caixinhas';
                                }
                            } elseif (isset($transaction['vault_id']) && $transaction['vault_id'] !== null) {
                                $v = $vaultsById[$transaction['vault_id']] ?? null;
                                $vaultLabel = $v ? $v['name'] : ('Caixinha #' . $transaction['vault_id']);
                            } else {
                                $vaultLabel = 'Saldo geral';
                            }
                            ?>
                            <div class="p-4 hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex items-center gap-3 min-w-0 flex-1">
                                        <div
                                            class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-primary/10 dark:bg-blue-900/30 text-primary dark:text-blue-400">
                                            <?php if ($isInternal): ?>
                                                <i class="fa-solid fa-piggy-bank text-lg" aria-hidden="true"></i>
                                            <?php else: ?>
                                                <?= strtoupper(substr($transaction['name'] ?? '', 0, 1)) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 dark:text-white truncate">
                                                <?= esc($displayTitle) ?>
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                <?= esc($displayName) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        class="px-3 py-1 text-xs font-semibold rounded-lg <?= $badgeClasses ?>"><?= $badgeLabel ?></span>
                                </div>
                                <div class="flex items-center justify-between mb-3">
                                    <p class="text-lg font-bold font-display <?= $amountClasses ?>"><?= $prefix ?>R$
                                        <?= number_format($transaction['amount'], 2, ',', '.') ?>
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <?= date('d/m/Y', strtotime($transaction['date'] ?? $transaction['created_at'] ?? 'now')) ?>
                                    </p>
                                </div>
                                <?php if (!empty($transaction['description']) || !empty($vaultLabel)): ?>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                        <?php if (!empty($transaction['description'])): ?>                 <?= esc($transaction['description']) ?>
                                        <?php endif; ?>
                                        <span
                                            class="block text-xs mt-1 text-gray-400 dark:text-gray-500"><?= esc($vaultLabel) ?></span>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700/80">
                            <thead class="bg-gray-50 dark:bg-gray-800/80">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                        Nome</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                        Título</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                        Descrição</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                        Tipo</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                        Valor</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                        Data</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                        Origem/Destino</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/80">
                                <?php foreach ($transactions as $transaction): ?>
                                    <?php
                                    $uiType = $transaction['ui_type'] ?? $transaction['type'] ?? '';
                                    $isInternal = in_array($uiType, ['deposit', 'withdraw', 'transfer', 'transfer_in', 'transfer_out'], true);
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
                                    } elseif ($uiType === 'transfer_in') {
                                        $badgeLabel = 'Crédito';
                                        $badgeClasses = 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300';
                                    } elseif ($uiType === 'transfer_out') {
                                        $badgeLabel = 'Débito';
                                        $badgeClasses = 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300';
                                    }
                                    $amountClasses = 'text-sky-700 dark:text-sky-300';
                                    $prefix = '';
                                    if ($uiType === 'income' || $uiType === 'transfer_in') {
                                        $amountClasses = 'text-emerald-600 dark:text-emerald-400';
                                        $prefix = '+ ';
                                    } elseif ($uiType === 'outcome' || $uiType === 'transfer_out') {
                                        $amountClasses = 'text-red-600 dark:text-red-400';
                                        $prefix = '- ';
                                    }
                                    $displayTitle = $transaction['ui_title'] ?? $transaction['title'] ?? '';
                                    $displayName = $transaction['ui_name'] ?? $transaction['name'] ?? '';
                                    $fromVaultId = $transaction['from_vault_id']
                                        ?? $transaction['source_vault_id']
                                        ?? $transaction['origin_vault_id']
                                        ?? null;
                                    $toVaultId = $transaction['to_vault_id']
                                        ?? $transaction['target_vault_id']
                                        ?? $transaction['destination_vault_id']
                                        ?? null;
                                    $fromVaultName = $transaction['from_vault_name']
                                        ?? $transaction['source_vault_name']
                                        ?? $transaction['origin_vault_name']
                                        ?? (($fromVaultId !== null && isset($vaultsById[$fromVaultId]))
                                            ? ($vaultsById[$fromVaultId]['name'] ?? null)
                                            : null);
                                    $toVaultName = $transaction['to_vault_name']
                                        ?? $transaction['target_vault_name']
                                        ?? $transaction['destination_vault_name']
                                        ?? (($toVaultId !== null && isset($vaultsById[$toVaultId]))
                                            ? ($vaultsById[$toVaultId]['name'] ?? null)
                                            : null);
                                    $currentVaultName = $vault['name'] ?? null;

                                    if (in_array($uiType, ['transfer', 'transfer_in', 'transfer_out'], true)) {
                                        if ($uiType === 'transfer_in') {
                                            if (!empty($fromVaultName)) {
                                                $displayTitle = 'Transferência de ' . $fromVaultName;
                                            } elseif (!empty($displayTitle) && stripos($displayTitle, 'Vault ') === 0) {
                                                $displayTitle = 'Transferência recebida';
                                            }

                                            if (!empty($currentVaultName)) {
                                                $displayName = $currentVaultName;
                                            }
                                        } elseif ($uiType === 'transfer_out') {
                                            if (!empty($toVaultName)) {
                                                $displayTitle = 'Transferência para ' . $toVaultName;
                                            } elseif (!empty($displayTitle) && stripos($displayTitle, 'Vault ') === 0) {
                                                $displayTitle = 'Transferência enviada';
                                            }

                                            if (!empty($currentVaultName)) {
                                                $displayName = $currentVaultName;
                                            }
                                        } else {
                                            if (!empty($fromVaultName) && !empty($toVaultName)) {
                                                $displayTitle = $fromVaultName . ' → ' . $toVaultName;
                                            } elseif (!empty($fromVaultName)) {
                                                $displayTitle = 'Saída de ' . $fromVaultName;
                                            } elseif (!empty($toVaultName)) {
                                                $displayTitle = 'Entrada em ' . $toVaultName;
                                            }

                                            if (empty($displayName) || stripos($displayName, 'vault') !== false) {
                                                $displayName = 'Transferência entre caixinhas';
                                            }
                                        }
                                    }

                                    $vaultLabel = null;
                                    if (in_array($uiType, ['transfer', 'transfer_in', 'transfer_out'], true)) {
                                        if (!empty($fromVaultName) && !empty($toVaultName)) {
                                            $vaultLabel = $fromVaultName . ' → ' . $toVaultName;
                                        } elseif (!empty($fromVaultName)) {
                                            $vaultLabel = $fromVaultName . ' → —';
                                        } elseif (!empty($toVaultName)) {
                                            $vaultLabel = '— → ' . $toVaultName;
                                        } else {
                                            $vaultLabel = 'Transferência entre caixinhas';
                                        }
                                    } elseif (isset($transaction['vault_id']) && $transaction['vault_id'] !== null) {
                                        $v = $vaultsById[$transaction['vault_id']] ?? null;
                                        $vaultLabel = $v ? $v['name'] : ('Caixinha #' . $transaction['vault_id']);
                                    } else {
                                        $vaultLabel = 'Saldo geral';
                                    }
                                    ?>
                                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold bg-primary/10 dark:bg-blue-900/30 text-primary dark:text-blue-400">
                                                    <?php if ($isInternal): ?>
                                                        <i class="fa-solid fa-piggy-bank text-sm" aria-hidden="true"></i>
                                                    <?php else: ?>
                                                        <?= strtoupper(substr($transaction['name'] ?? '', 0, 1)) ?>
                                                    <?php endif; ?>
                                                </div>
                                                <span
                                                    class="text-sm font-medium text-gray-900 dark:text-white"><?= esc($displayName) ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="text-sm font-medium text-gray-900 dark:text-white"><?= esc($displayTitle) ?></span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="text-sm text-gray-500 dark:text-gray-400"><?= esc($transaction['description'] ?? '—') ?></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-lg <?= $badgeClasses ?>">
                                                <?= $badgeLabel ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-bold font-display <?= $amountClasses ?>">
                                                <?= $prefix ?>R<?= ' ' . number_format($transaction['amount'], 2, ',', '.') ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <?= date('d/m/Y', strtotime($transaction['date'] ?? $transaction['created_at'] ?? 'now')) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-xs text-gray-700 dark:text-gray-300">
                                                <i class="fa-solid <?= in_array($uiType, ['transfer', 'transfer_in', 'transfer_out'], true) ? 'fa-arrow-right-arrow-left' : (isset($transaction['vault_id']) && $transaction['vault_id'] !== null ? 'fa-piggy-bank' : 'fa-wallet') ?> mr-1.5 text-[0.7rem]"
                                                    aria-hidden="true"></i>
                                                <?= esc($vaultLabel) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var btn = document.getElementById('vault-more-actions-btn');
        var menu = document.getElementById('vault-more-actions-menu');
        var chevron = document.querySelector('.vault-more-chevron');
        if (!btn || !menu) return;
        function open() {
            menu.hidden = false;
            btn.setAttribute('aria-expanded', 'true');
            if (chevron) chevron.style.transform = 'rotate(180deg)';
            document.addEventListener('click', closeOnOutside);
        }
        function close() {
            menu.hidden = true;
            btn.setAttribute('aria-expanded', 'false');
            if (chevron) chevron.style.transform = '';
            document.removeEventListener('click', closeOnOutside);
        }
        function closeOnOutside(e) {
            if (!menu.contains(e.target) && e.target !== btn) close();
        }
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            menu.hidden ? open() : close();
        });
    })();
</script>
<?= $this->endSection() ?>