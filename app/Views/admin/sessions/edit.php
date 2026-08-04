<?php ob_start(); ?>

<div class="nx-page-header">
    <h1 class="nx-page-title"><?= esc($title) ?></h1>
    <a href="<?= site_url('admin/sessions') ?>" class="btn btn-outline-secondary btn-sm">
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

<?php $val = !empty($old) ? $old : $session; ?>

<div class="nx-card">
    <div class="nx-card-header">
        <span class="nx-card-title">Modifier la session</span>
    </div>
    <div class="nx-card-body">
        <form action="<?= site_url('admin/sessions/update/' . $session['id_session']) ?>" method="POST">
            <?= csrf_field() ?>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Formation <span class="text-danger">*</span></label>
                    <select name="id_formation" class="form-select" required>
                        <?php foreach ($formations as $f): ?>
                            <option value="<?= $f['id_formation'] ?>"
                                <?= ($val['id_formation'] == $f['id_formation']) ? 'selected' : '' ?>>
                                <?= esc($f['titre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Formateur <span class="text-danger">*</span></label>
                    <select name="id_formateur" class="form-select" required>
                        <option value="">— Choisir un formateur —</option>
                        <?php foreach ($formateurs as $f): ?>
                            <option value="<?= $f['id_compte'] ?>"
                                <?= (($val['id_formateur'] ?? '') == $f['id_compte']) ? 'selected' : '' ?>>
                                <?= esc($f['prenom'] . ' ' . $f['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Date de début <span class="text-danger">*</span></label>
                    <input type="date" name="date_debut" class="form-control"
                           value="<?= esc($val['date_debut']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Date de fin <span class="text-danger">*</span></label>
                    <input type="date" name="date_fin" class="form-control"
                           value="<?= esc($val['date_fin']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Places max <span class="text-danger">*</span></label>
                    <input type="number" name="place_max" class="form-control" min="1"
                           value="<?= esc($val['place_max'] ?? '') ?>" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Modalité <span class="text-danger">*</span></label>
                    <select name="modalite" class="form-select" required>
                        <option value="Presentiel" <?= (($val['modalite'] ?? '') === 'Presentiel') ? 'selected' : '' ?>>Présentiel</option>
                        <option value="En_ligne"   <?= (($val['modalite'] ?? '') === 'En_ligne')   ? 'selected' : '' ?>>En ligne</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Statut <span class="text-danger">*</span></label>
                    <select name="statut" class="form-select" required>
                        <?php
                        $statuts = ['A_venir' => 'À venir', 'Ouverte' => 'Ouverte', 'En_cours' => 'En cours', 'Terminee' => 'Terminée', 'Annulee' => 'Annulée'];
                        foreach ($statuts as $val_statut => $label_statut):
                        ?>
                            <option value="<?= $val_statut ?>"
                                <?= (($val['statut'] ?? 'A_venir') === $val_statut) ? 'selected' : '' ?>>
                                <?= $label_statut ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fa-solid fa-location-dot me-1" style="color:#7B2FFF;"></i>Lieu
                    <span style="font-size:12px; color:#9ca3af; font-weight:400;">(si présentiel)</span>
                </label>
                <input type="text" name="lieu" class="form-control" maxlength="255"
                       value="<?= esc($val['lieu'] ?? '') ?>">
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">
                    <i class="fa-solid fa-wifi me-1" style="color:#00C8FF;"></i>Lien visioconférence
                    <span style="font-size:12px; color:#9ca3af; font-weight:400;">(si en ligne)</span>
                </label>
                <input type="url" name="lien_visio" class="form-control" maxlength="255"
                       value="<?= esc($val['lien_visio'] ?? '') ?>">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn-nexus btn">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Enregistrer
                </button>
                <a href="<?= site_url('admin/sessions') ?>" class="btn btn-outline-secondary">Annuler</a>
            </div>

        </form>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?= view('admin/layout', ['title' => $title, 'content' => $content]) ?>
