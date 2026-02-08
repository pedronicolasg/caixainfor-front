<?= $this->extend('layout') ?>

<?php $this->setVar('showNavbar', true); ?>

<?= $this->section('content') ?>

<div class="section-spacing animate-fade-in">
    <div class="card p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold font-display text-gray-900 dark:text-white tracking-tight">
                    Transações</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gerencie todas as suas transações financeiras
                </p>
            </div>
            <a href="<?= base_url('/transactions/create') ?>"
                class="btn-primary shrink-0 px-5 py-3 text-sm sm:text-base">
                <i class="fa-solid fa-plus mr-2 text-base" aria-hidden="true"></i>
                <span class="hidden sm:inline">Nova Transação</span>
                <span class="sm:hidden">Nova</span>
            </a>
        </div>
    </div>

    <?php
    $hasActiveFilters = !empty($filters['type']) || !empty($filters['startDate']) || !empty($filters['endDate']) || ($filters['orderBy'] ?? '') !== 'created_at' || ($filters['order'] ?? 'desc') !== 'desc';
    $exportQuery = http_build_query(array_filter([
        'type' => $filters['type'] ?? '',
        'startDate' => $filters['startDate'] ?? '',
        'endDate' => $filters['endDate'] ?? '',
        'orderBy' => $filters['orderBy'] ?? 'created_at',
        'order' => $filters['order'] ?? 'desc',
    ]));
    ?>
    <div class="card overflow-hidden">
        <div
            class="p-4 sm:p-5 bg-gradient-to-br from-gray-50 to-primary/5 dark:from-gray-800/80 dark:to-primary/10 border-b border-gray-100 dark:border-gray-700/80">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-primary/10 dark:bg-blue-900/30 flex items-center justify-center text-primary dark:text-blue-400">
                        <i class="fa-solid fa-sliders text-lg" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold font-display text-gray-900 dark:text-white">Filtros</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Refine as transações por tipo, período e
                            ordem</p>
                    </div>
                </div>
                <?php if ($hasActiveFilters && !empty($transactions)): ?>
                    <div class="flex items-center gap-2 sm:ml-auto" id="export-dropdown-wrap">
                        <div class="relative">
                            <button type="button" id="export-dropdown-btn" aria-expanded="false" aria-haspopup="true"
                                aria-controls="export-dropdown-menu"
                                class="btn-primary inline-flex items-center gap-2 px-4 py-2.5 text-sm">
                                <i class="fa-solid fa-file-export" aria-hidden="true"></i>
                                <span>Exportar</span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform export-chevron"
                                    aria-hidden="true"></i>
                            </button>
                            <div id="export-dropdown-menu" role="menu" hidden
                                class="absolute right-0 mt-2 w-52 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow-lg py-1 z-20">
                                <a href="<?= base_url('transactions/export/excel?' . $exportQuery) ?>" role="menuitem"
                                    class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <span
                                        class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400"><i
                                            class="fa-solid fa-file-excel" aria-hidden="true"></i></span>
                                    <span>Exportar para Excel</span>
                                </a>
                                <a href="<?= base_url('transactions/export/pdf?' . $exportQuery) ?>" role="menuitem"
                                    class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <span
                                        class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400"><i
                                            class="fa-solid fa-file-pdf" aria-hidden="true"></i></span>
                                    <span>Exportar para PDF</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <form method="GET" action="<?= base_url('/transactions') ?>" class="p-5 sm:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 sm:gap-5">
                <div class="sm:col-span-2 lg:col-span-2">
                    <label for="type"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo</label>
                    <select id="type" name="type" class="input-base py-2.5">
                        <option value="">Todos os tipos</option>
                        <option value="income" <?= ($filters['type'] ?? '') === 'income' ? 'selected' : '' ?>>Entrada
                        </option>
                        <option value="outcome" <?= ($filters['type'] ?? '') === 'outcome' ? 'selected' : '' ?>>Saída
                        </option>
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label for="startDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data
                        inicial</label>
                    <input type="date" id="startDate" name="startDate" value="<?= esc($filters['startDate'] ?? '') ?>"
                        class="input-base py-2.5" placeholder="dd/mm/aaaa">
                </div>

                <div class="lg:col-span-2">
                    <label for="endDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data
                        final</label>
                    <input type="date" id="endDate" name="endDate" value="<?= esc($filters['endDate'] ?? '') ?>"
                        class="input-base py-2.5" placeholder="dd/mm/aaaa">
                </div>

                <div class="lg:col-span-3">
                    <label for="orderBy" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ordenar
                        por</label>
                    <select id="orderBy" name="orderBy" class="input-base py-2.5">
                        <option value="created_at" <?= ($filters['orderBy'] ?? 'created_at') === 'created_at' ? 'selected' : '' ?>>Data de criação</option>
                        <option value="date" <?= ($filters['orderBy'] ?? '') === 'date' ? 'selected' : '' ?>>Data da
                            transação</option>
                        <option value="amount" <?= ($filters['orderBy'] ?? '') === 'amount' ? 'selected' : '' ?>>Valor
                        </option>
                    </select>
                </div>

                <div class="lg:col-span-3">
                    <label for="order"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ordem</label>
                    <select id="order" name="order" class="input-base py-2.5">
                        <option value="desc" <?= ($filters['order'] ?? 'desc') === 'desc' ? 'selected' : '' ?>>Mais recente
                            primeiro</option>
                        <option value="asc" <?= ($filters['order'] ?? '') === 'asc' ? 'selected' : '' ?>>Mais antigo
                            primeiro</option>
                    </select>
                </div>

                <div class="sm:col-span-2 lg:col-span-6">
                    <div class="pt-1 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 sm:gap-3">
                        <a href="<?= base_url('/transactions') ?>"
                            class="btn-secondary w-full sm:w-auto px-5 py-2.5 text-sm text-center inline-flex items-center justify-center gap-2">
                            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                            Limpar
                        </a>

                        <button type="submit"
                            class="btn-primary w-full sm:w-auto px-5 py-2.5 text-sm inline-flex items-center justify-center gap-2">
                            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                            Aplicar filtros
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>

    <div class="card overflow-hidden">
        <?php if (empty($transactions)): ?>
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
            <div class="lg:hidden divide-y divide-gray-100 dark:divide-gray-700/80">
                <?php foreach ($transactions as $transaction): ?>
                    <div class="p-4 hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div
                                    class="w-10 h-10 rounded-xl bg-primary/10 dark:bg-blue-900/30 flex items-center justify-center shrink-0 text-primary dark:text-blue-400 text-sm font-semibold">
                                    <?= strtoupper(substr($transaction['name'], 0, 1)) ?>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-white truncate">
                                        <?= esc($transaction['title']) ?>
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                        <?= esc($transaction['name']) ?>
                                    </p>
                                </div>
                            </div>
                            <span
                                class="px-2.5 py-1 text-xs font-semibold rounded-lg shrink-0 <?= $transaction['type'] === 'income' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' ?>"><?= $transaction['type'] === 'income' ? 'Entrada' : 'Saída' ?></span>
                        </div>
                        <div class="flex items-center justify-between mb-3">
                            <p
                                class="text-lg font-bold font-display <?= $transaction['type'] === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' ?>">
                                <?= $transaction['type'] === 'income' ? '+' : '-' ?> R$
                                <?= number_format($transaction['amount'], 2, ',', '.') ?>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <?= date('d/m/Y', strtotime($transaction['date'])) ?>
                            </p>
                        </div>
                        <?php if (!empty($transaction['description'])): ?>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3 line-clamp-2">
                                <?= esc($transaction['description']) ?>
                            </p>
                        <?php endif; ?>
                        <div class="flex gap-2 pt-3 border-t border-gray-100 dark:border-gray-700/80">
                            <a href="<?= base_url('/transactions/' . $transaction['id'] . '/edit') ?>"
                                class="flex-1 text-center px-3 py-2.5 text-sm font-medium text-primary dark:text-blue-400 hover:bg-primary/10 dark:hover:bg-blue-500/20 rounded-xl transition-colors">Editar</a>
                            <a href="<?= base_url('/transactions/' . $transaction['id'] . '/delete') ?>"
                                class="flex-1 text-center px-3 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-500/10 dark:hover:bg-red-500/20 rounded-xl transition-colors"
                                onclick="return confirm('Tem certeza que deseja deletar esta transação?')">Deletar</a>
                        </div>
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
                                class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/80">
                        <?php foreach ($transactions as $transaction): ?>
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-primary/10 dark:bg-blue-900/30 flex items-center justify-center text-primary dark:text-blue-400 text-xs font-semibold">
                                            <?= strtoupper(substr($transaction['name'], 0, 1)) ?>
                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-900 dark:text-white"><?= esc($transaction['name']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white"><?= esc($transaction['title']) ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm text-gray-500 dark:text-gray-400"><?= esc($transaction['description'] ?? '—') ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-3 py-1 text-xs font-semibold rounded-lg <?= $transaction['type'] === 'income' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' ?>"><?= $transaction['type'] === 'income' ? 'Entrada' : 'Saída' ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="text-sm font-bold font-display <?= $transaction['type'] === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' ?>"><?= $transaction['type'] === 'income' ? '+' : '-' ?>
                                        R$ <?= number_format($transaction['amount'], 2, ',', '.') ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <?= date('d/m/Y', strtotime($transaction['date'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-4">
                                        <a href="<?= base_url('/transactions/' . $transaction['id'] . '/edit') ?>"
                                            class="text-sm font-medium text-primary dark:text-blue-400 hover:underline inline-flex items-center gap-1"><i
                                                class="fa-solid fa-pen-to-square text-sm" aria-hidden="true"></i> Editar</a>
                                        <a href="<?= base_url('/transactions/' . $transaction['id'] . '/delete') ?>"
                                            class="text-sm font-medium text-red-600 dark:text-red-400 hover:underline inline-flex items-center gap-1"
                                            onclick="return confirm('Tem certeza que deseja deletar esta transação?')"><i
                                                class="fa-solid fa-trash text-sm" aria-hidden="true"></i> Deletar</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php
            if ($pagination && $pagination['totalPages'] > 1):
                $pagQuery = array_filter([
                    'type' => $filters['type'] ?? '',
                    'startDate' => $filters['startDate'] ?? '',
                    'endDate' => $filters['endDate'] ?? '',
                    'orderBy' => $filters['orderBy'] ?? 'created_at',
                    'order' => $filters['order'] ?? 'desc',
                ]);
                $pagBase = $pagQuery ? http_build_query($pagQuery) . '&' : '';
                ?>
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-800/50">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 text-center sm:text-left">
                            Mostrando <span
                                class="font-semibold"><?= (($pagination['page'] - 1) * $pagination['limit']) + 1 ?></span> até
                            <span
                                class="font-semibold"><?= min($pagination['page'] * $pagination['limit'], $pagination['total']) ?></span>
                            de <span class="font-semibold"><?= $pagination['total'] ?></span> resultados
                        </p>
                        <nav class="flex items-center gap-2 flex-wrap justify-center" aria-label="Paginação">
                            <?php if ($pagination['page'] > 1): ?>
                                <a href="?<?= $pagBase ?>page=<?= $pagination['page'] - 1 ?>"
                                    class="btn-secondary px-4 py-2 text-sm">Anterior</a>
                            <?php endif; ?>
                            <?php
                            $start = max(1, $pagination['page'] - 2);
                            $end = min($pagination['totalPages'], $pagination['page'] + 2);
                            for ($i = $start; $i <= $end; $i++):
                                ?>
                                <?php if ($i == $pagination['page']): ?>
                                    <span class="px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold"
                                        aria-current="page"><?= $i ?></span>
                                <?php else: ?>
                                    <a href="?<?= $pagBase ?>page=<?= $i ?>" class="btn-secondary px-4 py-2 text-sm"><?= $i ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <?php if ($pagination['page'] < $pagination['totalPages']): ?>
                                <a href="?<?= $pagBase ?>page=<?= $pagination['page'] + 1 ?>"
                                    class="btn-secondary px-4 py-2 text-sm">Próxima</a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php if ($hasActiveFilters && !empty($transactions)): ?>
    <script>
        (function () {
            var btn = document.getElementById('export-dropdown-btn');
            var menu = document.getElementById('export-dropdown-menu');
            var chevron = document.querySelector('.export-chevron');
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
<?php endif; ?>
<?= $this->endSection() ?>