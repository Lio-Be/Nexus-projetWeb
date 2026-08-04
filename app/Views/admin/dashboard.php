<?php ob_start(); ?>

<div class="nx-page-header">
    <h1 class="nx-page-title">Tableau de bord</h1>
    <span style="font-size:13px; color:#6b7280;">
        Bienvenue, <?= esc(session()->get('prenom') . ' ' . session()->get('nom')) ?>
    </span>
</div>

<!-- Cartes statistiques cliquables -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <a href="<?= site_url('admin/formations') ?>" class="text-decoration-none">
            <div class="nx-stat-card" style="border-left-color:#00C8FF; cursor:pointer; transition:box-shadow 0.15s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                <div>
                    <div class="nx-stat-label">Formations</div>
                    <div class="nx-stat-value"><?= $nbFormations ?></div>
                </div>
                <div class="nx-stat-icon" style="background:rgba(0,200,255,0.1); color:#00C8FF;">
                    <i class="fa-solid fa-book-open"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="<?= site_url('admin/poles') ?>" class="text-decoration-none">
            <div class="nx-stat-card" style="border-left-color:#7B2FFF; cursor:pointer; transition:box-shadow 0.15s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                <div>
                    <div class="nx-stat-label">Pôles</div>
                    <div class="nx-stat-value"><?= $nbPoles ?></div>
                </div>
                <div class="nx-stat-icon" style="background:rgba(123,47,255,0.1); color:#7B2FFF;">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="<?= site_url('admin/sessions') ?>" class="text-decoration-none">
            <div class="nx-stat-card" style="border-left-color:#10b981; cursor:pointer; transition:box-shadow 0.15s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                <div>
                    <div class="nx-stat-label">Sessions</div>
                    <div class="nx-stat-value"><?= $nbSessions ?></div>
                </div>
                <div class="nx-stat-icon" style="background:rgba(16,185,129,0.1); color:#10b981;">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="<?= site_url('admin/comptes') ?>" class="text-decoration-none">
            <div class="nx-stat-card" style="border-left-color:#f59e0b; cursor:pointer; transition:box-shadow 0.15s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                <div>
                    <div class="nx-stat-label">Étudiants</div>
                    <div class="nx-stat-value"><?= $nbEtudiants ?></div>
                </div>
                <div class="nx-stat-icon" style="background:rgba(245,158,11,0.1); color:#f59e0b;">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?= view('admin/layout', ['title' => $title, 'content' => $content]) ?>
