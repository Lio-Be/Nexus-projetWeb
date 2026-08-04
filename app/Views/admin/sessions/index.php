<?php ob_start(); ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="nx-page-header">
    <h1 class="nx-page-title"><?= esc($title) ?></h1>
    <a href="<?= site_url('admin/sessions/create') ?>" class="btn-nexus btn">
        <i class="fa-solid fa-plus me-1"></i> Nouvelle session
    </a>
</div>

<div class="nx-card">
    <div class="nx-card-header">
        <span class="nx-card-title">Liste des sessions</span>
    </div>
    <div class="nx-card-body">
        <table id="tableSessions" class="table table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Formation</th>
                    <th>Mode</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Places</th>
                    <th>Formateur</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessions as $s): ?>
                <tr>
                    <td><?= esc($s['id_session']) ?></td>
                    <td>
                        <div class="fw-semibold" style="font-size:13.5px;"><?= esc($s['titre_formation']) ?></div>
                        <div style="font-size:11px; color:#9ca3af;"><?= esc($s['nom_pole']) ?></div>
                    </td>
                    <td>
                        <?php if (($s['modalite'] ?? '') === 'En_ligne'): ?>
                            <span class="badge" style="background:rgba(0,200,255,0.12); color:#0284c7; font-size:12px;">
                                <i class="fa-solid fa-wifi fa-xs me-1"></i>En ligne
                            </span>
                        <?php else: ?>
                            <span class="badge" style="background:rgba(123,47,255,0.1); color:#7B2FFF; font-size:12px;">
                                <i class="fa-solid fa-location-dot fa-xs me-1"></i>Présentiel
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:13px;"><?= date('d/m/Y', strtotime($s['date_debut'])) ?></td>
                    <td style="font-size:13px;"><?= date('d/m/Y', strtotime($s['date_fin'])) ?></td>
                    <td style="font-size:13px;">
                        <?= !empty($s['place_max']) ? esc($s['place_max']) : '<span style="color:#d1d5db;">—</span>' ?>
                    </td>
                    <td style="font-size:13px;">
                        <?php if ($s['nom_formateur']): ?>
                            <?= esc($s['prenom_formateur'] . ' ' . $s['nom_formateur']) ?>
                        <?php else: ?>
                            <span style="color:#d1d5db;">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= site_url('admin/sessions/edit/' . $s['id_session']) ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-pen"></i> Modifier
                        </a>
                        <button class="btn btn-sm btn-outline-danger btn-delete"
                                data-id="<?= $s['id_session'] ?>"
                                data-nom="<?= esc($s['titre_formation']) ?> (<?= date('d/m/Y', strtotime($s['date_debut'])) ?>)">
                            <i class="fa-solid fa-trash"></i> Supprimer
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php $scripts = '<script>

const table = $("#tableSessions").DataTable({
    language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json" },
    order: [[3, "desc"]],
    columnDefs: [{ orderable: false, targets: 7 }]
});

const deleteBaseUrl = "' . site_url('admin/sessions/delete/') . '";

$(document).on("click", ".btn-delete", function() {
    const id  = $(this).data("id");
    const nom = $(this).data("nom");
    const $row = $(this).closest("tr");

    if (!confirm("Supprimer la session « " + nom + " » ?")) return;

    $.ajax({
        url: deleteBaseUrl + id,
        type: "GET",
        dataType: "json",
        headers: { "X-Requested-With": "XMLHttpRequest" },
        success: function(response) {
            if (response.success) {
                table.row($row).remove().draw();
                showNotification(response.message, true);
            } else {
                showNotification(response.message, false);
            }
        },
        error: function() {
            showNotification("Erreur lors de la suppression.", false);
        }
    });
});

</script>'; ?>

<?= view('admin/layout', ['title' => $title, 'content' => $content, 'scripts' => $scripts]) ?>
