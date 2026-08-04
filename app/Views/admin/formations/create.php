<?php ob_start(); ?>
<div class="nx-page-header">
    <h1 class="nx-page-title"><?= esc($title) ?></h1>
    <a href="<?= site_url('admin/formations') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Retour à la liste
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
            <li><?= esc($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
<div class="nx-card">
    <div class="nx-card-header">
        <span class="nx-card-title">Informations de la formation</span>
    </div>
<div class="nx-card-body">
<form action="<?= site_url('admin/formations/store') ?>" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="titre" class="form-label fw-semibold">Titre <span style="color:red">*</span></label><br>
        <input type="text" id="titre" name="titre" class="form-control"
               value="<?= esc($old['titre'] ?? '') ?>"
               maxlength="150" required>
    </div>
    <br>

    <div class="mb-3">
        <label for="description" class="form-label fw-semibold">Description</label><br>
        <textarea id="description" name="description" rows="4" cols="60" class="form-control"><?= esc($old['description'] ?? '') ?></textarea>
    </div>
    <br>

    <div class="mb-3">
        <label for="duree" class="form-label fw-semibold">Durée (en heures)</label><br>
        <input type="number" id="duree" name="duree" class="form-control"
               value="<?= esc($old['duree'] ?? '') ?>" min="1">
    </div>
    <br>

    <div class="mb-3">
        <label for="prix" class="form-label fw-semibold">Prix (€)</label><br>
        <input type="number" id="prix" name="prix" class="form-control"
               value="<?= esc($old['prix'] ?? '') ?>" min="0" step="0.01">
    </div>
    <br>

    <div class="mb-3">
        <label for="image" class="form-label fw-semibold">Image de la formation</label><br>
        <input type="file"
               id="image"
               name="image"
               class="form-control"
           accept="image/jpeg,image/png,image/webp">
    <small style="color:gray;">Formats acceptés : JPG, PNG, WebP. Taille max : 2 Mo. (Optionnel)</small>
</div>
    <br>

    <div class="mb-3">
        <label for="prerequis" class="form-label fw-semibold">Prérequis</label><br>
        <textarea id="prerequis" name="prerequis" rows="3" cols="60" class="form-control"><?= esc($old['prerequis'] ?? '') ?></textarea>
    </div>
    <br>

        <div class="mb-3">
        <label for="id_pole" class="form-label fw-semibold">Pôle <span style="color:red">*</span></label><br>
        <select id="id_pole" name="id_pole" class="form-select" required>
            <option value="">— Sélectionner un pôle —</option>
            <?php foreach ($poles as $pole): ?>
                <option value="<?= $pole['id_pole'] ?>"
                    <?= (isset($old['id_pole']) && $old['id_pole'] == $pole['id_pole']) ? 'selected' : '' ?>>
                    <?= esc($pole['nom_pole']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <br>

    <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn-nexus btn">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Enregistrer
                </button>
                <a href="<?= site_url('admin/formations') ?>" class="btn btn-outline-secondary">
                    Annuler
                </a>
            </div>
</form>
</div>
</div>
<?php $content = ob_get_clean(); ?>
<?= view('admin/layout', ['title' => $title, 'content' => $content]) ?>