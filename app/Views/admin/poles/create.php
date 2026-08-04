<?php ob_start(); ?>

<div class="nx-page-header">
    <h1 class="nx-page-title"><?= esc($title) ?></h1>
    <a href="<?= site_url('admin/poles') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Retour
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Erreurs de validation :</strong>
        <ul class="mb-0 mt-2">
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="nx-card">
    <div class="nx-card-header">
        <span class="nx-card-title">Informations du pôle</span>
    </div>
    <div class="nx-card-body">
        <form action="<?= site_url('admin/poles/store') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="nom_pole" class="form-label fw-semibold">
                    Nom du pôle <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control"
                       id="nom_pole"
                       name="nom_pole"
                       value="<?= esc($old['nom_pole'] ?? '') ?>"
                       maxlength="100"
                       required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Description</label>
                <textarea class="form-control"
                          id="description"
                          name="description"
                          rows="4"><?= esc($old['description'] ?? '') ?></textarea>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn-nexus btn">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Enregistrer
                </button>
                <a href="<?= site_url('admin/poles') ?>" class="btn btn-outline-secondary">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?= view('admin/layout', ['title' => $title, 'content' => $content]) ?>