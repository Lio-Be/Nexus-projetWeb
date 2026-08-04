<?php ob_start(); ?>

<div class="nx-page-header">
    <h1 class="nx-page-title"><?= esc($title) ?></h1>
    <a href="<?= site_url('admin/formations') ?>" class="btn btn-outline-secondary btn-sm">
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
        <span class="nx-card-title">Modifier la formation</span>
    </div>
    <div class="nx-card-body">
        <form action="<?= site_url('admin/formations/update/' . $formation['id_formation']) ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="titre" class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
                <input type="text" id="titre" name="titre" class="form-control"
                       value="<?= esc($old['titre'] ?? $formation['titre']) ?>"
                       maxlength="150" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Description</label>
                <textarea id="description" name="description" rows="4" class="form-control"><?= esc($old['description'] ?? $formation['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="duree" class="form-label fw-semibold">Durée (en heures)</label>
                <input type="number" id="duree" name="duree" class="form-control"
                       value="<?= esc($old['duree'] ?? $formation['duree']) ?>" min="1">
            </div>

            <div class="mb-3">
                <label for="prix" class="form-label fw-semibold">Prix (€)</label>
                <input type="number" id="prix" name="prix" class="form-control"
                       value="<?= esc($old['prix'] ?? $formation['prix']) ?>" min="0" step="0.01">
            </div>

            <div class="mb-3">
                <label for="image" class="form-label fw-semibold">Image de la formation</label>
                <?php if (!empty($formation['image'])): ?>
                    <div class="mb-2">
                        <img src="<?= base_url('uploads/formations/' . esc($formation['image'])) ?>"
                             alt="Image actuelle"
                             style="max-width:200px; max-height:150px; border:1px solid #ccc; border-radius:6px;">
                        <p class="text-muted" style="font-size:0.85em;">Image actuelle : <?= esc($formation['image']) ?></p>
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" class="form-control"
                       accept="image/jpeg,image/png,image/webp">
                <small class="text-muted">Laisser vide pour conserver l'image actuelle. Formats : JPG, PNG, WebP. Max : 2 Mo.</small>
            </div>

            <div class="mb-3">
                <label for="prerequis" class="form-label fw-semibold">Prérequis</label>
                <textarea id="prerequis" name="prerequis" rows="3" class="form-control"><?= esc($old['prerequis'] ?? $formation['prerequis']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="id_pole" class="form-label fw-semibold">Pôle <span class="text-danger">*</span></label>
                <?php $selectedPole = $old['id_pole'] ?? $formation['id_pole']; ?>
                <select id="id_pole" name="id_pole" class="form-select" required>
                    <option value="">— Sélectionner un pôle —</option>
                    <?php foreach ($poles as $pole): ?>
                        <option value="<?= $pole['id_pole'] ?>"
                            <?= ($selectedPole == $pole['id_pole']) ? 'selected' : '' ?>>
                            <?= esc($pole['nom_pole']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn-nexus btn">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Mettre à jour
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
