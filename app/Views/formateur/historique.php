<?php ob_start(); ?>

<?php
$total_sessions = count($sessions);

$total_heures = 0;
foreach ($sessions as $s) {
    $debut = strtotime($s['date_debut']);
    $fin   = strtotime($s['date_fin']);
    if ($fin > $debut) {
        $total_heures += ($fin - $debut) / 3600;
    }
}
$total_heures = round($total_heures);

$total_etudiants = array_sum(array_column($sessions, 'places_prises'));
?>

<div class="nx-page-header">
    <h1 class="nx-page-title"><?= esc($title) ?></h1>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="nx-stat-card" style="border-left-color:#00C8FF;">
            <div>
                <div class="nx-stat-label">Sessions animées</div>
                <div class="nx-stat-value"><?= $total_sessions ?></div>
            </div>
            <div class="nx-stat-icon" style="background:rgba(0,200,255,0.1); color:#00C8FF;">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="nx-stat-card" style="border-left-color:#7B2FFF;">
            <div>
                <div class="nx-stat-label">Heures enseignées</div>
                <div class="nx-stat-value"><?= $total_heures ?>h</div>
            </div>
            <div class="nx-stat-icon" style="background:rgba(123,47,255,0.1); color:#7B2FFF;">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="nx-stat-card" style="border-left-color:#10b981;">
            <div>
                <div class="nx-stat-label">Étudiants formés</div>
                <div class="nx-stat-value"><?= $total_etudiants ?></div>
            </div>
            <div class="nx-stat-icon" style="background:rgba(16,185,129,0.1); color:#10b981;">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>
</div>

<div class="nx-card">
    <div class="nx-card-header">
        <span class="nx-card-title">Sessions terminées</span>
    </div>
    <div class="nx-card-body">
        <?php if (empty($sessions)): ?>
            <p class="text-muted">Aucune session terminée pour le moment.</p>
        <?php else: ?>
        <table id="tableHistorique" class="table table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Formation</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Modalité</th>
                    <th>Étudiants</th>
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
                    <td style="font-size:13px;"><?= esc($s['places_prises']) ?></td>
                    <td style="font-size:13px;">
                        <?= !empty($s['lieu']) ? esc($s['lieu']) : '<span style="color:#d1d5db;">—</span>' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php $scripts = '<script>$("#tableHistorique").DataTable({ order: [[2,"desc"]], language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json" } });</script>'; ?>
<?= view('formateur/layout', ['title' => $title, 'content' => $content, 'scripts' => $scripts]) ?>
