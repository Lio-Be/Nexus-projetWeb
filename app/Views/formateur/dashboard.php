<?php ob_start(); ?>

<?php
$total    = count($sessions);
$en_cours = count(array_filter($sessions, fn($s) => $s['statut'] === 'En_cours'));
$termines = count(array_filter($sessions, fn($s) => $s['statut'] === 'Terminee'));
?>

<div class="nx-page-header">
    <h1 class="nx-page-title">Bonjour, <?= esc(session()->get('prenom')) ?></h1>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="nx-stat-card" style="border-left-color:#00C8FF;">
            <div>
                <div class="nx-stat-label">Sessions assignées</div>
                <div class="nx-stat-value"><?= $total ?></div>
            </div>
            <div class="nx-stat-icon" style="background:rgba(0,200,255,0.1); color:#00C8FF;">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="nx-stat-card" style="border-left-color:#3b82f6;">
            <div>
                <div class="nx-stat-label">En cours</div>
                <div class="nx-stat-value"><?= $en_cours ?></div>
            </div>
            <div class="nx-stat-icon" style="background:rgba(59,130,246,0.1); color:#3b82f6;">
                <i class="fa-solid fa-spinner"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="nx-stat-card" style="border-left-color:#10b981;">
            <div>
                <div class="nx-stat-label">Terminées</div>
                <div class="nx-stat-value"><?= $termines ?></div>
            </div>
            <div class="nx-stat-icon" style="background:rgba(16,185,129,0.1); color:#10b981;">
                <i class="fa-solid fa-check"></i>
            </div>
        </div>
    </div>
</div>

<div class="nx-card">
    <div class="nx-card-header">
        <span class="nx-card-title">Prochaines sessions</span>
        <a href="<?= site_url('formateur/planning') ?>" class="btn btn-sm btn-nexus">Voir tout</a>
    </div>
    <div class="nx-card-body">
        <?php if (empty($sessions)): ?>
            <p class="text-muted">Vous n'avez aucune session assignée pour le moment.</p>
        <?php else: ?>
            <ul class="list-unstyled mb-0">
                <?php foreach (array_slice($sessions, 0, 5) as $s): ?>
                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="fw-semibold" style="font-size:13.5px;"><?= esc($s['titre_formation']) ?></span>
                        <span style="font-size:12px; color:#6b7280;"><?= date('d/m/Y', strtotime($s['date_debut'])) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?= view('formateur/layout', ['title' => $title, 'content' => $content]) ?>