<?= $this->extend('layout') ?>

<?php $this->setVar('showNavbar', true); ?>

<?= $this->section('content') ?>

<div class="space-y-6 animate-fade-in">
    <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl sm:text-3xl font-bold font-display text-gray-900 mb-2">Transações</h1>
                <p class="text-sm sm:text-base text-gray-600">Gerencie todas as suas transações financeiras</p>
            </div>
            <a href="<?= base_url('/transactions/create') ?>"
                class="inline-flex items-center justify-center px-4 sm:px-6 py-2 sm:py-3 bg-primary text-white text-sm sm:text-base font-medium font-display rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                <i class="fa-solid fa-plus mr-2 text-[1.25rem] leading-none" aria-hidden="true"></i>
                <span class="hidden sm:inline">Nova Transação</span>
                <span class="sm:hidden">Nova</span>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
        <h2 class="text-lg font-semibold font-display text-gray-900 mb-4 flex items-center">
            <i class="fa-solid fa-filter w-5 h-5 mr-2 text-[1.25rem] leading-none text-primary" aria-hidden="true"></i>
            Filtros
        </h2>
        <form method="GET" action="<?= base_url('/transactions') ?>"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                <select id="type" name="type"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200">
                    <option value="">Todos</option>
                    <option value="income" <?= $filters['type'] === 'income' ? 'selected' : '' ?>>Entrada</option>
                    <option value="outcome" <?= $filters['type'] === 'outcome' ? 'selected' : '' ?>>Saída</option>
                </select>
            </div>
            <div>
                <label for="startDate" class="block text-sm font-medium text-gray-700 mb-2">Data Inicial</label>
                <input type="date" id="startDate" name="startDate" value="<?= esc($filters['startDate']) ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200">
            </div>
            <div>
                <label for="endDate" class="block text-sm font-medium text-gray-700 mb-2">Data Final</label>
                <input type="date" id="endDate" name="endDate" value="<?= esc($filters['endDate']) ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200">
            </div>
            <div>
                <label for="orderBy" class="block text-sm font-medium text-gray-700 mb-2">Ordenar por</label>
                <select id="orderBy" name="orderBy"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200">
                    <option value="created_at" <?= $filters['orderBy'] === 'created_at' ? 'selected' : '' ?>>Data de
                        Criação</option>
                    <option value="date" <?= $filters['orderBy'] === 'date' ? 'selected' : '' ?>>Data</option>
                    <option value="amount" <?= $filters['orderBy'] === 'amount' ? 'selected' : '' ?>>Valor</option>
                </select>
            </div>
            <div class="flex flex-col sm:flex-row items-end space-y-2 sm:space-y-0 sm:space-x-2">
                <button type="submit"
                    class="flex-1 sm:flex-initial px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm hover:shadow">
                    Filtrar
                </button>
                <a href="<?= base_url('/transactions') ?>"
                    class="flex-1 sm:flex-initial px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200 text-center">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <?php if (empty($transactions)): ?>
            <div class="p-8 sm:p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-file-lines text-[2rem] leading-none text-gray-400" aria-hidden="true"></i>
                </div>
                <p class="text-gray-500 font-medium">Nenhuma transação encontrada</p>
                <p class="text-gray-400 text-sm mt-1">Comece criando sua primeira transação</p>
                <a href="<?= base_url('/transactions/create') ?>"
                    class="mt-4 inline-flex items-center px-4 py-2 bg-primary text-white font-medium font-display rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    Criar Transação
                </a>
            </div>
        <?php else: ?>
            <div class="lg:hidden divide-y divide-gray-100">
                <?php foreach ($transactions as $transaction): ?>
                    <div class="p-4 hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span
                                        class="text-primary text-xs font-semibold"><?= strtoupper(substr($transaction['name'], 0, 1)) ?></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate"><?= esc($transaction['title']) ?>
                                    </p>
                                    <p class="text-xs text-gray-500 truncate"><?= esc($transaction['name']) ?></p>
                                </div>
                            </div>
                            <span
                                class="px-2.5 py-1 text-xs font-semibold rounded-full flex-shrink-0 ml-2 <?= $transaction['type'] === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= $transaction['type'] === 'income' ? 'Entrada' : 'Saída' ?>
                            </span>
                        </div>
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p
                                    class="text-lg font-bold font-display <?= $transaction['type'] === 'income' ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= $transaction['type'] === 'income' ? '+' : '-' ?> R$
                                    <?= number_format($transaction['amount'], 2, ',', '.') ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-1"><?= date('d/m/Y', strtotime($transaction['date'])) ?></p>
                            </div>
                        </div>
                        <?php if ($transaction['description']): ?>
                            <p class="text-xs text-gray-500 mb-3 line-clamp-2"><?= esc($transaction['description']) ?></p>
                        <?php endif; ?>
                        <div class="flex items-center space-x-4 pt-3 border-t border-gray-100">
                            <a href="<?= base_url('/transactions/' . $transaction['id'] . '/edit') ?>"
                                class="flex-1 text-center px-3 py-2 text-sm text-primary hover:text-blue-700 hover:bg-indigo-50 rounded-lg transition-colors duration-200">
                                Editar
                            </a>
                            <a href="<?= base_url('/transactions/' . $transaction['id'] . '/delete') ?>"
                                class="flex-1 text-center px-3 py-2 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                onclick="return confirm('Tem certeza que deseja deletar esta transação?')">
                                Deletar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Nome</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Título</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Descrição</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Tipo</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Valor</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Data</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <?php foreach ($transactions as $transaction): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                            <span
                                                class="text-primary text-xs font-semibold"><?= strtoupper(substr($transaction['name'], 0, 1)) ?></span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900"><?= esc($transaction['name']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-900 font-medium"><?= esc($transaction['title']) ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-500"><?= esc($transaction['description'] ?? '-') ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-3 py-1 inline-flex text-xs font-semibold rounded-full <?= $transaction['type'] === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= $transaction['type'] === 'income' ? 'Entrada' : 'Saída' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="text-sm font-bold font-display <?= $transaction['type'] === 'income' ? 'text-green-600' : 'text-red-600' ?>">
                                        <?= $transaction['type'] === 'income' ? '+' : '-' ?> R$
                                        <?= number_format($transaction['amount'], 2, ',', '.') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('d/m/Y', strtotime($transaction['date'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-3">
                                        <a href="<?= base_url('/transactions/' . $transaction['id'] . '/edit') ?>"
                                            class="text-primary hover:text-blue-900 transition-colors duration-200 flex items-center">
                                            <i class="fa-solid fa-pen-to-square mr-1 text-[1rem] leading-none"
                                                aria-hidden="true"></i>
                                            Editar
                                        </a>
                                        <a href="<?= base_url('/transactions/' . $transaction['id'] . '/delete') ?>"
                                            class="text-red-600 hover:text-red-900 transition-colors duration-200 flex items-center"
                                            onclick="return confirm('Tem certeza que deseja deletar esta transação?')">
                                            <i class="fa-solid fa-trash mr-1 text-[1rem] leading-none" aria-hidden="true"></i>
                                            Deletar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($pagination && $pagination['totalPages'] > 1): ?>
                <div class="bg-gray-50 px-4 sm:px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                        <div class="text-sm text-gray-700 text-center sm:text-left">
                            Mostrando <span
                                class="font-semibold"><?= (($pagination['page'] - 1) * $pagination['limit']) + 1 ?></span>
                            até <span
                                class="font-semibold"><?= min($pagination['page'] * $pagination['limit'], $pagination['total']) ?></span>
                            de <span class="font-semibold"><?= $pagination['total'] ?></span> resultados
                        </div>
                        <nav class="flex items-center space-x-2 flex-wrap justify-center">
                            <?php if ($pagination['page'] > 1): ?>
                                <a href="?page=<?= $pagination['page'] - 1 ?>"
                                    class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                    Anterior
                                </a>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $pagination['page'] - 2);
                            $end = min($pagination['totalPages'], $pagination['page'] + 2);
                            for ($i = $start; $i <= $end; $i++):
                                ?>
                                <?php if ($i == $pagination['page']): ?>
                                    <span class="px-3 sm:px-4 py-2 bg-primary text-white rounded-lg text-sm font-semibold">
                                        <?= $i ?>
                                    </span>
                                <?php else: ?>
                                    <a href="?page=<?= $i ?>"
                                        class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                        <?= $i ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($pagination['page'] < $pagination['totalPages']): ?>
                                <a href="?page=<?= $pagination['page'] + 1 ?>"
                                    class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                    Próxima
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>