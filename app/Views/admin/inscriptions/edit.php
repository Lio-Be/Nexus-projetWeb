<?php ob_start(); ?>

<div class="nx-page-header">
    <h1 class="nx-page-title"><?= esc($title) ?></h1>
    <a href="<?= site_url('admin/inscriptions') ?>" class="btn btn-outline-secondary btn-sm">
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

<?php $val = !empty($old) ? $old : $inscription; ?>

<div class="nx-card">
    <div class="nx-card-header">
        <span class="nx-card-title">Modifier l'inscription</span>
    </div>
    <div class="nx-card-body">
        <form action="<?= site_url('admin/inscriptions/update/' . $inscription['id_compte'] . '/' . $inscription['id_session']) ?>" method="POST">
            <?= csrf_field() ?>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Compte</label>
                        <input type="text" class="form-control" value="<?= esc($inscription['prenom_compte'] . ' ' . $inscription['nom_compte']) ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Formation</label>
                        <input type="text" class="form-control" value="<?= esc($inscription['titre']) ?>" disabled>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">État parcours <span class="text-danger">*</span></label>
                        <select name="etat_parcours" class="form-select" required>
                            <?php $statuts = ['Inscrit' => 'Inscrit', 'En_cours' => 'En cours', 'Termine' => 'Terminé', 'Abandonne' => 'Abandonné']; ?>
                            <?php foreach ($statuts as $val_etat => $label): ?>
                                <option value="<?= $val_etat ?>" <?= (($val['etat_parcours'] ?? 'Inscrit') === $val_etat) ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Note admin</label>
                        <textarea name="note_admin" class="form-control" rows="3"><?= esc($val['note_admin'] ?? '') ?></textarea>
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

<div class="nx-card mt-3">
    <div class="nx-card-header">
        <span class="nx-card-title">Paiement</span>
    </div>
    <div class="nx-card-body">

        <?php if (! $inscription['date_envoi_paiement']): ?>
            <p class="text-muted">Aucun paiement envoyé.</p>

        <?php elseif ($inscription['date_validation_paiement']): ?>
            <span class="badge" style="background:rgba(16,185,129,0.1); color:#10b981; font-size:12px;">
            <i class="fa-solid fa-check fa-xs me-1"></i>Validé le <?= date('d/m/Y', strtotime($inscription['date_validation_paiement'])) ?>
             </span>

        <?php elseif ($inscription['raison_refus_paiement']): ?>
            <span class="badge" style="background:rgba(239,68,68,0.1); color:#ef4444; font-size:12px;">
            <i class="fa-solid fa-xmark fa-xs me-1"></i>Refusé 
            </span>
            <div class="mt-2">
                <strong>Raison du refus :</strong>
                <p><?= esc($inscription['raison_refus_paiement']) ?></p>
            </div>

        <?php else: ?>
            <p class="text-muted mb-3">Paiement envoyé le <?= date('d/m/Y', strtotime($inscription['date_envoi_paiement'])) ?> — en attente de validation.</p>

            <div class="d-flex gap-3 align-items-start">
                <form action="<?= site_url('admin/inscriptions/approuver/' . $inscription['id_compte'] . '/' . $inscription['id_session']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-check me-1"></i> Approuver
                    </button>
                </form>

                <form action="<?= site_url('admin/inscriptions/refuser/' . $inscription['id_compte'] . '/' . $inscription['id_session']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-2">
                        <textarea name="raison_refus_paiement" class="form-control" rows="2" placeholder="Raison du refus..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-xmark me-1"></i> Refuser
                    </button>
                </form>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?= view('admin/layout', ['title' => $title, 'content' => $content]) ?>

