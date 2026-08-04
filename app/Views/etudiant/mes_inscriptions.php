<?php ob_start(); ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="nx-page-header">
    <h1 class="nx-page-title"><?= esc($title) ?></h1>
    <a href="<?= site_url(($base_route ?? 'etudiant') . '/catalogue') ?>" class="btn-nexus btn">
        <i class="fa-solid fa-plus me-1"></i> Nouvelle inscription
    </a>
</div>

<div class="nx-card">
    <div class="nx-card-header">
        <span class="nx-card-title">Mes inscriptions</span>
    </div>
    <div class="nx-card-body">
        <table id="tableSessions" class="table table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>Formation</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>État parcours</th>
                    <th>Paiement</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inscriptions as $i): ?>
                <tr>
                    <td>
                        <div class="fw-semibold" style="font-size:13.5px;"><?= esc($i['titre']) ?></div>
                    </td>
                    
                    <td style="font-size:13px;"><?= date('d/m/Y', strtotime($i['date_debut'])) ?></td>
                    <td style="font-size:13px;"><?= date('d/m/Y', strtotime($i['date_fin'])) ?></td>
                    <td style="font-size:13px;">
                        <?php if ($i['etat_parcours'] === 'Inscrit'): ?>
                            <span class="badge" style="background:rgba(156,163,175,0.1); color:#9ca3af; font-size:12px;">
                                <i class="fa-solid fa-user fa-xs me-1"></i>Inscrit
                            </span>
                        <?php elseif ($i['etat_parcours'] === 'En_cours'): ?>
                            <span class="badge" style="background:rgba(59,130,246,0.1); color:#3b82f6; font-size:12px;">
                                <i class="fa-solid fa-spinner fa-xs me-1"></i>En cours
                            </span>
                        <?php elseif ($i['etat_parcours'] === 'Termine'): ?>
                            <span class="badge" style="background:rgba(16,185,129,0.1); color:#10b981; font-size:12px;">
                                <i class="fa-solid fa-check fa-xs me-1"></i>Terminé
                            </span>
                        <?php else: ?>
                            <span class="badge" style="background:rgba(239,68,68,0.1); color:#ef4444; font-size:12px;">
                                <i class="fa-solid fa-xmark fa-xs me-1"></i>Abandonné
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                    <?php if ($i['date_validation_paiement'] !== NULL): ?>
                            <span class="badge" style="background:rgba(16,185,129,0.1); color:#10b981; font-size:12px;">
                                <i class="fa-solid fa-check fa-xs me-1"></i>Validé
                            </span>
                        <?php elseif ($i['date_envoi_paiement'] !== NULL): ?>
                            <span class="badge" style="background:rgba(246, 199, 59, 0.1); color:#d97706; font-size:12px;">
                                <i class="fa-solid fa-spinner fa-xs me-1"></i>En attente de validation
                            </span>
                        <?php else: ?>
                            <div style="font-size:12px; color:#9ca3af;">
                                Virer vers : <strong>BE01 1234 5678 9012</strong><br>
                                Communication : <strong><?= generate_structured_com($i['id_compte'], $i['id_session']) ?></strong><br>
                                Montant : <strong><?= esc($i['prix']) ?> €</strong>
                            </div>
                            <form action="<?= site_url(($base_route ?? 'etudiant') . '/paiement/envoyer/' . $i['id_compte'] . '/' . $i['id_session']) ?>" method="POST" class="mt-1">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-nexus mt-1">
                                    <i class="fa-solid fa-paper-plane me-1"></i> Envoyer
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?= view($layout ?? 'etudiant/layout', ['title' => $title, 'content' => $content]) ?>
