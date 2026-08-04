<?php ob_start(); ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="nx-page-header">
    <h1 class="nx-page-title"><?= esc($title) ?></h1>
</div>

<div class="nx-card">
    <div class="nx-card-header">
        <span class="nx-card-title">Liste des comptes</span>
    </div>
    <div class="nx-card-body">
        <table id="tableComptes" class="table table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comptes as $c): ?>
                <tr>
                    <td><?= esc($c['id_compte']) ?></td>
                    <td><?= esc($c['nom']) ?></td>
                    <td><?= esc($c['prenom']) ?></td>
                    <td><?= esc($c['email']) ?></td>
                    <td>
                        <?php if ($c['role'] === 'Formateur'): ?>
                            <span class="badge" style="background:rgba(16,185,129,0.1); color:#10b981; font-size:12px;">
                                <i class="fa-solid fa-chalkboard-teacher fa-xs me-1"></i>Formateur
                            </span>
                        <?php elseif ($c['role'] === 'Admin'): ?>
                            <span class="badge" style="background:rgba(239,68,68,0.1); color:#ef4444; font-size:12px;">
                                <i class="fa-solid fa-shield fa-xs me-1"></i>Admin
                            </span>
                        <?php else: ?>
                            <span class="badge" style="background:rgba(156,163,175,0.1); color:#9ca3af; font-size:12px;">
                                <i class="fa-solid fa-user fa-xs me-1"></i>Étudiant
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($c['role'] !== 'Admin'): ?>
                            <form action="<?= site_url('admin/comptes/toggle-role/' . $c['id_compte']) ?>" method="POST">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-nexus">
                                    <?php if ($c['role'] === 'Etudiant'): ?>
                                        <i class="fa-solid fa-arrow-up me-1"></i> Promouvoir formateur
                                    <?php else: ?>
                                        <i class="fa-solid fa-arrow-down me-1"></i> Rétrograder étudiant
                                    <?php endif; ?>
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
<?= view('admin/layout', ['title' => $title, 'content' => $content]) ?>
