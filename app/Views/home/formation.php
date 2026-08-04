<?= $this->extend('layouts/public') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- En-tête de page -->
<div style="background: linear-gradient(135deg, #0A0F2E, #1a1040); padding: 40px 0 32px;">
    <div class="container">
        <div class="d-flex align-items-center gap-2 mb-2" style="color:rgba(255,255,255,0.45); font-size:13px;">
            <a href="<?= base_url('/') ?>" style="color:rgba(255,255,255,0.45); text-decoration:none;">Accueil</a>
            <span>›</span>
            <span style="color:rgba(255,255,255,0.75);">Formations</span>
        </div>
        <h1 class="fw-bold mb-1" style="color:#fff; font-size:1.8rem;">Nos formations</h1>
        <p style="color:rgba(255,255,255,0.55); font-size:14px; margin:0;">
            Découvrez l'ensemble de nos parcours et trouvez celui qui vous correspond.
        </p>
    </div>
</div>

<!-- Contenu -->
<div class="container py-4">
    <div class="rounded-3" style="background:#fff; border:1px solid #e5e7eb; box-shadow:0 1px 3px rgba(0,0,0,0.06);">

        <div class="px-4 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2"
             style="border-bottom:1px solid #f0f0f0;">
            <span style="font-size:14px; font-weight:600; color:#111827;">
                <i class="fa-solid fa-book-open me-2" style="color:#00C8FF;"></i>Catalogue des formations
            </span>
        </div>

        <div class="p-4">
            <div class="table-responsive">
                <table id="tableFormations" class="table table-hover align-middle" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Pôle</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Durée</th>
                            <th>Prix</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultats as $ligne): ?>
                            <tr>
                                <td>
                                    <span class="badge px-2 py-1"
                                          style="background:rgba(123,47,255,0.1); color:#7B2FFF; font-size:12px; font-weight:500;">
                                        <?= esc($ligne['nom_pole']) ?>
                                    </span>
                                </td>
                                <td class="fw-semibold" style="font-size:13.5px; color:#111827;">
                                    <?= esc($ligne['titre']) ?>
                                </td>
                                <td style="font-size:13px; color:#6b7280; max-width:300px;">
                                    <?= esc(mb_strimwidth($ligne['description'], 0, 100, '…')) ?>
                                </td>
                                <td style="font-size:13px;">
                                    <?php if (!empty($ligne['duree'])): ?>
                                        <i class="fa-regular fa-clock fa-xs me-1" style="color:#00C8FF;"></i><?= esc($ligne['duree']) ?>h
                                    <?php else: ?>
                                        <span style="color:#d1d5db;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size:13px;">
                                    <?php if (!empty($ligne['prix'])): ?>
                                        <?= number_format((float)$ligne['prix'], 0, ',', ' ') ?> €
                                    <?php else: ?>
                                        <span style="color:#d1d5db;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function () {
    $('#tableFormations').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
        pageLength: 10,
        columnDefs: [{ orderable: false, targets: [2] }]
    });
});
</script>
<?= $this->endSection() ?>
