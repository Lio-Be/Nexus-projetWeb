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
</div>

<div class="nx-card">
    <div class="nx-card-header">
        <span class="nx-card-title">Mon planning</span>
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
                    <th>Statut</th>
                    <th>Inscrits/Places</th>
                    <th>Lieu</th>
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
                    <td>
                        <?php if ($s['modalite'] === 'Distancielle'): ?>
                            <span class="badge" style="background:rgba(59,130,246,0.1); color:#3b82f6; font-size:12px;">
                                <i class="fa-solid fa-laptop fa-xs me-1"></i>Distancielle
                            </span>
                        <?php else: ?>
                            <span class="badge" style="background:rgba(156,163,175,0.1); color:#9ca3af; font-size:12px;">
                                <i class="fa-solid fa-user fa-xs me-1"></i>Présentielle
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:13px;">
                        <?php
                        $statuts = ['Planifiee' => 'Planifiée', 'Ouverte' => 'Ouverte', 'En_cours' => 'En cours', 'Terminee' => 'Terminée', 'Annulee' => 'Annulée'];
                        $label = $statuts[$s['statut']] ?? $s['statut'];
                        ?>
                        <span class="badge" style="background:rgba(156,163,175,0.1); color:#9ca3af; font-size:12px;">
                            <?= esc($label) ?>
                        </span>
                    </td>
                    <td style="font-size:13px;">
                        <?= esc($s['places_prises']) ?> / <?= esc($s['place_max']) ?>
                    </td>
                    <td style="font-size:13px;">
                        <?= !empty($s['lieu']) ? esc($s['lieu']) : '<span style="color:#d1d5db;">—</span>' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php $scripts = '<script>$("#tableSessions").DataTable({ order: [[2,"asc"]], language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json" } });</script>'; ?>
<?= view('formateur/layout', ['title' => $title, 'content' => $content, 'scripts' => $scripts]) ?>
