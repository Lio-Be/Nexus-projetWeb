<?php ob_start(); ?>

<!-- Notification AJAX (gérée par showNotification() du layout)  -->
<div id="nx-notification"></div>

<!-- Message flash classique (non-AJAX)  -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!--  En-tête de page  -->
<div class="nx-page-header">
    <h1 class="nx-page-title"><?= esc($title) ?></h1>
    <a href="<?= site_url('admin/poles/create') ?>" class="btn-nexus btn">
        <i class="fa-solid fa-plus me-1"></i> Nouveau pôle
    </a>
</div>

<!--  Tableau dans une card  -->
<div class="nx-card">
    <div class="nx-card-header">
        <span class="nx-card-title">Liste des pôles</span>
    </div>
    <div class="nx-card-body">
        <table id="tablePoles" class="table table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom du pôle</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($poles as $pole): ?>
                <tr>
                    <td><?= esc($pole['id_pole']) ?></td>
                    <td><div class="fw-semibold" style="font-size:13.5px;"><?= esc($pole['nom_pole']) ?></div></td>
                    <td><div style="font-size:13px; color:#9ca3af;"><?= esc(substr($pole['description'] ?? '', 0, 80)) ?>...</div></td>
                    <td>
                        <a href="<?= site_url('admin/poles/edit/' . $pole['id_pole']) ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-pen"></i> Modifier
                        </a>
                        <button class="btn btn-sm btn-outline-danger btn-delete"
                                data-id="<?= $pole['id_pole'] ?>"
                                data-nom="<?= esc($pole['nom_pole']) ?>">
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

// Initialisation DataTables
const table = $("#tablePoles").DataTable({
    language: {
        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
    },
    order: [[1, "asc"]],
    columnDefs: [
        { orderable: false, targets: 3 }
    ]
});

// URL de base pour le delete 
const deleteBaseUrl = "' . site_url('admin/poles/delete/') . '";

//  Gestion du clic sur "Supprimer" 
$(document).on("click", ".btn-delete", function() {
    const id   = $(this).data("id");
    const nom  = $(this).data("nom");
    const $row = $(this).closest("tr");

    if (!confirm("Supprimer le pôle « " + nom + " » ?")) return;

    $.ajax({
        url:      deleteBaseUrl + id,
        type:     "GET",
        dataType: "json",
        headers:  { "X-Requested-With": "XMLHttpRequest" },
        success: function(response) {
            if (response.success) {
                table.row($row).remove().draw();
                showNotification(response.message, true);
            } else {
                showNotification(response.message, false);
            }
        },
        error: function(xhr) {
            showNotification("Erreur lors de la suppression. Veuillez réessayer.", false);
            console.error("Erreur AJAX :", xhr.status, xhr.responseText);
        }
    });
});

</script>'; ?>

<?= view('admin/layout', [
    'title'   => $title,
    'content' => $content,
    'scripts' => $scripts,
]) ?>