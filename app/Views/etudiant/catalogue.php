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
    <a href="<?= site_url(($base_route ?? 'etudiant') . '/mes-inscriptions') ?>" class="btn-nexus btn">
        <i class="fa-solid fa-list me-1"></i> Mes inscriptions
    </a>
</div>

<div class="nx-card">
    <div class="nx-card-header">
        <span class="nx-card-title">Sessions disponibles</span>
    </div>
    <div class="nx-card-body">
        <table id="tableSessions" class="table table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Formation</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Modalité</th>
                    <th>Place disponible</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessions as $s): ?>
                <tr>
                    <td><?= esc($s['id_session']) ?></td>
                    <td>
                        <div class="fw-semibold" style="font-size:13.5px;"><?= esc($s['titre_formation']) ?></div>
                    </td>
                    
                    <td style="font-size:13px;"><?= date('d/m/Y', strtotime($s['date_debut'])) ?></td>
                    <td style="font-size:13px;"><?= date('d/m/Y', strtotime($s['date_fin'])) ?></td>
                    <td style="font-size:13px;">
                        <?php if ($s['modalite'] === 'Presentielle'): ?>
                            <span class="badge" style="background:rgba(156,163,175,0.1); color:#9ca3af; font-size:12px;">
                                <i class="fa-solid fa-user fa-xs me-1"></i>Présentielle
                            </span>
                        <?php elseif ($s['modalite'] === 'Distancielle'): ?>
                            <span class="badge" style="background:rgba(59,130,246,0.1); color:#3b82f6; font-size:12px;">
                                <i class="fa-solid fa-laptop fa-xs me-1"></i>Distancielle
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:13px;">
                        <?= esc($s['place_max'] - $s['places_prises']) ?>
                    </td>
                    <td>
                        <?php if ($s['deja_inscrit']): ?>
                            <span class="badge" style="background:rgba(16,185,129,0.1); color:#10b981; font-size:12px;">
                                <i class="fa-solid fa-check fa-xs me-1"></i>Déjà inscrit
                            </span>
                        <?php else: ?>
                            <?php $places = $s['place_max'] - $s['places_prises']; ?>
                            <form action="<?= site_url(($base_route ?? 'etudiant') . '/inscrire/' . $s['id_session']) ?>" method="POST">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-nexus" <?= $places <= 0 ? 'disabled' : '' ?>>
                                    <i class="fa-solid fa-user-plus me-1"></i> S'inscrire
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
