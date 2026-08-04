<?php ob_start(); ?>

<!-- Message de succès classique (non-AJAX, flashdata) -->
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>


<div class="nx-page-header">
    <h1 class="nx-page-title"><?= esc($title) ?></h1>
    <a href="<?= site_url('admin/formations/create') ?>" class="btn-nexus btn">
        <i class="fa-solid fa-plus me-1"></i> Nouvelle formation
    </a>
</div>


<!--Tableau des formations-->
<div class="nx-card">
    <div class="nx-card-header">
        <span class="nx-card-title">Liste des formations</span>
    </div>
    <div class="nx-card-body">
        <table id="table-formations" class="table table-hover align-middle w-100">
    <thead>
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Titre</th>
            <th>Pôle</th>
            <th>Durée (h)</th>
            <th>Prix (€)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($formations as $f): ?>
        <tr>
            <td><?= esc($f['id_formation']) ?></td>
            <td>
                <?php if (!empty($f['image'])): ?>
                    <img src="<?= base_url('uploads/formations/' . esc($f['image'])) ?>"
                        alt="<?= esc($f['titre']) ?>"
                        style="width:60px; height:45px; object-fit:cover;">
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
            <td>
                <div class="fw-semibold" style="font-size:13.5px;"><?= esc($f['titre']) ?></div>
            </td>
            <td><div class="fw-semibold" style="font-size:13.5px;"><?= esc($f['nom_pole']) ?></div></td>
            <td><div class="fw-semibold" style="font-size:13.5px;"><?= esc($f['duree'] !== null ? esc($f['duree']) : '—' )?></div></td>
            <td><div class="fw-semibold" style="font-size:13.5px;"><?= esc($f['prix'] !== null ? number_format($f['prix'], 2, ',', ' ') . ' €' : '—' )?></div></td>
            <td>
               
                <a href="<?= site_url('admin/formations/edit/' . $f['id_formation']) ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-pen"></i> Modifier
                        </a>
                        <button class="btn btn-sm btn-outline-danger btn-delete"
                                data-id="<?= $f['id_formation'] ?>"
                                data-titre="<?= esc($f['titre']) ?>">
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
const table = $("#table-formations").DataTable({
    language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"},
    order: [[1, "asc"]],
    columnDefs: [
        { orderable: false, targets: 6 }
    ]
});

// URL de base pour le delete (générée par PHP) 
const deleteBaseUrl = "' . site_url('admin/formations/delete/') . '";

// Gestion du clic sur "Supprimer" 
$(document).on("click", ".btn-delete", function() {
    const id    = $(this).data("id");       // Récupère data-id="5"
    const titre = $(this).data("titre");    // Récupère data-titre="Unity..."
    const $row  = $(this).closest("tr");   // Référence à la ligne <tr>

    // Confirmation avant suppression
    if (!confirm("Supprimer la formation « " + titre + " » ?")) {
        return;  // L admin a annulé
    }

    // Requête AJAX
    $.ajax({
        url:      deleteBaseUrl + id,
        type:     "GET",
        dataType: "json",
        headers: {
            "X-Requested-With": "XMLHttpRequest"  // Identifie comme AJAX pour CI4
        },
        success: function(response) {
            if (response.success) {
                // Supprimer la ligne via l API DataTables
                table.row($row).remove().draw();
                showNotification(response.message, true);
            } else {
                showNotification(response.message, false);
            }
        },
        error: function(xhr) {
            // Erreur serveur (500, réseau, etc.)
            showNotification("Erreur lors de la suppression. Veuillez réessayer.", false);
            console.error("Erreur AJAX :", xhr.status, xhr.responseText);
        }
    });
});
</script>'; ?>

<?= view('admin/layout', ['title' => $title, 'content' => $content, 'scripts' => $scripts]) ?>