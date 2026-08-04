<?php ob_start(); ?>

<div class="nx-page-header">
    <h1 class="nx-page-title"><?= esc($title) ?></h1>
    <a href="<?= site_url('admin/inscriptions') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Retour
    </a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

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
        <span class="nx-card-title">Informations de l'inscription</span>
    </div>
    <div class="nx-card-body">
        <form action="<?= site_url('admin/inscriptions/store') ?>" method="POST">
            <?= csrf_field() ?>

             <div class="row g-3 mb-3">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Compte <span class="text-danger">*</span></label>
                    <select name="id_compte" class="form-select" required>
                        <option value="">— Choisir un compte —</option>
                        <?php foreach ($comptes as $c): ?>
                            <option value="<?= $c['id_compte'] ?>"
                                <?= (($old['id_compte'] ?? '') == $c['id_compte']) ? 'selected' : '' ?>>
                                <?= esc($c['prenom'] . ' ' . $c['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Session <span class="text-danger">*</span></label>
                    <select name="id_session" class="form-select" required>
                        <option value="">— Choisir une session —</option>
                        <?php foreach ($sessions as $s): ?>
                            <option value="<?= $s['id_session'] ?>"
                                <?= (($old['id_session'] ?? '') == $s['id_session']) ? 'selected' : '' ?>>
                                <?= esc($s['titre_formation'] . ' (' . date('d/m/Y', strtotime($s['date_debut'])) . ' - ' . date('d/m/Y', strtotime($s['date_fin'])) . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn-nexus btn">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Enregistrer
                </button>
                <a href="<?= site_url('admin/inscriptions') ?>" class="btn btn-outline-secondary">Annuler</a>
            </div>

        </form>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?= view('admin/layout', ['title' => $title, 'content' => $content]) ?>

