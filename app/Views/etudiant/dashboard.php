<?php ob_start(); ?>

<div class="nx-page-header">
    <h1 class="nx-page-title">Bonjour, <?= esc(session()->get('prenom')) ?> 👋</h1>
</div>

<!-- Stats row : 3 cartes -->
<?php
$total     = count($inscriptions);
$en_cours  = count(array_filter($inscriptions, fn($i) => $i['etat_parcours'] === 'En_cours'));
$termines  = count(array_filter($inscriptions, fn($i) => $i['etat_parcours'] === 'Termine'));
?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="nx-stat-card" style="border-left-color:#00C8FF;">
            <div>
                <div class="nx-stat-label">Total inscriptions</div>
                <div class="nx-stat-value"><?= $total ?></div>
            </div>
            <div class="nx-stat-icon" style="background:rgba(0,200,255,0.1); color:#00C8FF;">
                <i class="fa-solid fa-graduation-cap"></i>
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

<!-- Lien rapide -->
<div class="nx-card">
    <div class="nx-card-header">
        <span class="nx-card-title">Mes dernières inscriptions</span>
        <a href="<?= site_url('etudiant/mes-inscriptions') ?>" class="btn btn-sm btn-outline-primary">Voir tout</a>
    </div>
    <div class="nx-card-body">
        <?php if (empty($inscriptions)): ?>
            <p class="text-muted">Vous n'avez aucune inscription pour le moment.</p>
        <?php else: ?>
            <ul class="list-unstyled mb-0">
                <?php foreach (array_slice($inscriptions, 0, 3) as $i): ?>
                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="fw-semibold" style="font-size:13.5px;"><?= esc($i['titre']) ?></span>
                        <span style="font-size:12px; color:#6b7280;"><?= date('d/m/Y', strtotime($i['date_debut'])) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?= view('etudiant/layout', ['title' => $title, 'content' => $content]) ?>