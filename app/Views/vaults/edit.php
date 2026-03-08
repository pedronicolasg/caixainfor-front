<?= $this->extend('layout') ?>

<?php $this->setVar('showNavbar', true); ?>

<?= $this->section('content') ?>

<div class="max-w-3xl mx-auto animate-fade-in">
    <div class="mb-6 sm:mb-8">
        <a href="<?= base_url('/vaults/' . $vault['id']) ?>"
            class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors mb-4">
            <i class="fa-solid fa-arrow-left text-base" aria-hidden="true"></i>
            Voltar
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold font-display text-gray-900 dark:text-white tracking-tight">
            Editar caixinha
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Altere o nome, imagem ou descrição da caixinha.
        </p>
    </div>

    <div class="card p-5 sm:p-6 lg:p-8">
        <form action="<?= base_url('/vaults/' . $vault['id']) ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PATCH">

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Nome da caixinha <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required
                    value="<?= esc(old('name', $vault['name'] ?? '')) ?>"
                    placeholder="Ex: Viagem técnica, Reserva de emergência" class="input-base">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Descrição (opcional)
                </label>
                <textarea id="description" name="description" rows="3" placeholder="Ex: Reserva para viagem de fim de ano"
                    class="input-base resize-none"><?= esc(old('description', $vault['description'] ?? '')) ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Imagem da caixinha (opcional)
                </label>
                <div class="rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-600 hover:border-primary/50 dark:hover:border-blue-500/50 transition-colors overflow-hidden bg-gray-50/50 dark:bg-gray-800/50">
                    <div id="vault-image-preview" class="aspect-video w-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden text-gray-400 dark:text-gray-500 cursor-pointer">
                        <?php if (!empty($vault['image'])): ?>
                            <img src="<?= esc($vault['image']) ?>" alt="<?= esc($vault['name']) ?>" class="w-full h-full object-cover object-center" id="vault-preview-img">
                        <?php else: ?>
                            <div class="text-center p-4">
                                <i class="fa-solid fa-cloud-arrow-up text-3xl mb-2 block" aria-hidden="true"></i>
                                <span class="text-sm">Clique ou arraste uma imagem (16:9)</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-3 flex flex-wrap items-center gap-2 border-t border-gray-200 dark:border-gray-600">
                        <label for="image" class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium bg-primary/10 dark:bg-blue-900/30 text-primary dark:text-blue-400 hover:bg-primary/20 dark:hover:bg-blue-900/50 cursor-pointer transition-colors">
                            <i class="fa-solid fa-image mr-2" aria-hidden="true"></i>
                            <?= !empty($vault['image']) ? 'Trocar imagem' : 'Selecionar imagem' ?>
                        </label>
                        <?php if (!empty($vault['image'])): ?>
                            <label class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 cursor-pointer transition-colors">
                                <input type="checkbox" name="remove_image" value="1" class="hidden remove-image-cb">
                                <i class="fa-solid fa-trash-can mr-2" aria-hidden="true"></i>
                                Remover imagem
                            </label>
                        <?php endif; ?>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" class="hidden">
                        <span id="vault-image-filename" class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[180px]"></span>
                    </div>
                </div>
                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                    JPG, PNG ou WEBP. A imagem será salva no site e a URL enviada para a API.
                </p>
            </div>

            <div
                class="flex flex-col sm:flex-row justify-end gap-3 pt-4 sm:pt-6 border-t border-gray-200 dark:border-gray-700/80">
                <a href="<?= base_url('/vaults/' . $vault['id']) ?>"
                    class="btn-secondary order-2 sm:order-1 px-6 py-3 text-center">Cancelar</a>
                <button type="submit" class="btn-primary order-1 sm:order-2 px-6 py-3">
                    Salvar alterações
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const fileInput = document.getElementById('image');
    const preview = document.getElementById('vault-image-preview');
    const removeCb = document.querySelector('.remove-image-cb');
    const filenameEl = document.getElementById('vault-image-filename');

    function setPlaceholder() {
        preview.innerHTML = '<div class="text-center p-4"><i class="fa-solid fa-cloud-arrow-up text-3xl mb-2 block" aria-hidden="true"></i><span class="text-sm">Clique ou arraste uma imagem (16:9)</span></div>';
        if (filenameEl) filenameEl.textContent = '';
    }

    function setPreview(file) {
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Pré-visualização" class="w-full h-full object-cover object-center">';
            if (filenameEl) filenameEl.textContent = file.name;
        };
        reader.readAsDataURL(file);
    }

    if (fileInput && preview) {
        preview.addEventListener('click', function (e) { if (e.target.tagName !== 'INPUT') fileInput.click(); });
        preview.style.cursor = 'pointer';

        fileInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (removeCb) removeCb.checked = false;
            if (!file) {
                setPlaceholder();
                return;
            }
            if (!file.type.startsWith('image/')) {
                alert('Selecione um arquivo de imagem (JPG, PNG ou WEBP).');
                fileInput.value = '';
                return;
            }
            setPreview(file);
        });

        preview.addEventListener('dragover', function (e) { e.preventDefault(); this.classList.add('ring-2', 'ring-primary/50'); });
        preview.addEventListener('dragleave', function () { this.classList.remove('ring-2', 'ring-primary/50'); });
        preview.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('ring-2', 'ring-primary/50');
            const file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                fileInput.files = e.dataTransfer.files;
                if (removeCb) removeCb.checked = false;
                setPreview(file);
            }
        });
    }

    if (removeCb && preview) {
        removeCb.addEventListener('change', function () {
            if (this.checked) {
                fileInput.value = '';
                setPlaceholder();
            }
        });
    }
})();
</script>

<?= $this->endSection() ?>
