<?php ob_start(); ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="nx-page-header">
    <h1 class="nx-page-title">Mon profil</h1>
</div>

<div class="nx-card" style="max-width: 600px;">
    <div class="nx-card-header">
        <span class="nx-card-title">Mes informations</span>
    </div>
    <div class="nx-card-body">
        <form action="<?= site_url(($base_route ?? 'etudiant') . '/profil/modifier') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nom</label>
                <input type="text" name="nom" class="form-control"
                       value="<?= esc($compte['nom']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Prénom</label>
                <input type="text" name="prenom" class="form-control"
                       value="<?= esc($compte['prenom']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?= esc($compte['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Adresse</label>
                <input type="text" name="adresse" class="form-control"
                       value="<?= esc($compte['adresse'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Code postal</label>
                <input type="text" name="code_postal" class="form-control"
                       value="<?= esc($compte['code_postal'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Date de naissance</label>
                <input type="date" name="date_naissance" class="form-control"
                       value="<?= esc($compte['date_naissance'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-nexus">
                <i class="fa-solid fa-floppy-disk me-1"></i> Enregistrer
            </button>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?= view($layout ?? 'etudiant/layout', ['title' => $title, 'content' => $content]) ?>
