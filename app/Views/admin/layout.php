<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'NEXUS Admin') ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        /* ── Variables de couleur globales ── */
        :root {
            --nx-bg:        #0A0F2E;  /* Fond sombre de la sidebar */
            --nx-accent:    #00C8FF;  /* Bleu cyan principal */
            --nx-purple:    #7B2FFF;  /* Violet secondaire */
            --nx-sidebar-w: 240px;    /* Largeur fixe de la sidebar */
        }

        /* ── Base ── */
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        /* ── Sidebar fixe à gauche ── */
        #nx-sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--nx-sidebar-w);
            height: 100vh;
            background: var(--nx-bg);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform 0.25s ease;
        }

        /* ── Logo / Marque ── */
        .nx-brand {
            padding: 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .nx-brand img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }
        .nx-brand-name {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 3px;
            background: linear-gradient(90deg, #00C8FF, #7B2FFF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ── Navigation principale ── */
        .nx-nav {
            flex: 1;
            padding: 16px 0;
            overflow-y: auto;
        }
        /* Titre de section (ex : "Gestion", "Navigation") */
        .nx-nav-section {
            font-size: 10px;
            color: rgba(255,255,255,0.3);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 12px 20px 4px;
        }
        /* Lien de navigation */
        .nx-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            font-size: 13.5px;
            border-left: 3px solid transparent;
            transition: all 0.15s;
        }
        .nx-nav-item:hover {
            color: #fff;
            background: rgba(255,255,255,0.05);
            text-decoration: none;
        }
        /* Lien actif : accent cyan + barre à gauche */
        .nx-nav-item.active {
            color: var(--nx-accent);
            background: rgba(0,200,255,0.08);
            border-left-color: var(--nx-accent);
        }
        .nx-nav-item i {
            width: 18px;
            text-align: center;
            font-size: 14px;
            opacity: 0.8;
        }
        .nx-nav-item.active i { opacity: 1; }

        /* ── Footer sidebar (utilisateur connecté) ── */
        .nx-sidebar-footer {
            padding: 14px 20px;
            border-top: 1px solid rgba(255,255,255,0.07);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        /* Avatar circulaire avec initiales */
        .nx-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00C8FF, #7B2FFF);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            flex-shrink: 0;
        }
        .nx-user-name {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
        }
        .nx-user-role {
            font-size: 11px;
            color: rgba(255,255,255,0.4);
        }
        /* Icône de déconnexion, alignée à droite */
        .nx-logout {
            margin-left: auto;
            color: rgba(255,255,255,0.3);
            font-size: 14px;
            text-decoration: none;
            transition: color 0.15s;
        }
        .nx-logout:hover { color: #ff6b6b; }

        /* ── Barre du haut (topbar) ── */
        #nx-topbar {
            position: fixed;
            top: 0;
            left: var(--nx-sidebar-w);  /* Démarre après la sidebar */
            right: 0;
            height: 56px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 99;
        }
        /* Fil d'Ariane (ex : Admin › Dashboard) */
        .nx-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #9ca3af;
        }
        .nx-breadcrumb-current {
            color: #111827;
            font-weight: 500;
        }
        /* Badge de rôle (ex : "Admin") */
        .nx-role-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 12px;
            border-radius: 20px;
            background: linear-gradient(90deg, #00C8FF, #7B2FFF);
            color: #fff;
            letter-spacing: 0.5px;
        }

        /* ── Zone de contenu principal ── */
        #nx-content {
            margin-left: var(--nx-sidebar-w);  /* Évite que le contenu passe sous la sidebar */
            margin-top: 56px;                  /* Évite que le contenu passe sous la topbar */
            padding: 24px;
            min-height: calc(100vh - 56px);
        }

        /* ── Cartes génériques ── */
        .nx-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .nx-card-header {
            padding: 14px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nx-card-title {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
        }
        .nx-card-body { padding: 20px; }

        /* ── Cartes statistiques (dashboard) ── */
        .nx-stat-card {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-left: 4px solid;  /* Couleur définie dynamiquement dans la vue */
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .nx-stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        .nx-stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
        }
        .nx-stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        /* ── Bouton principal dégradé ── */
        .btn-nexus {
            background: linear-gradient(90deg, #00C8FF, #7B2FFF);
            color: #fff;
            border: none;
            font-size: 13px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: opacity 0.15s;
        }
        .btn-nexus:hover { opacity: 0.88; color: #fff; }

        /* ── Notification AJAX (coin haut-droite, cachée par défaut) ── */
        #nx-notification {
            position: fixed;
            top: 68px;
            right: 20px;
            z-index: 999;
            min-width: 280px;
            display: none;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        #nx-notification.success { background: #ecfdf5; color: #065f46; border-left: 4px solid #10b981; }
        #nx-notification.error   { background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; }

        /* ── En-tête de page ── */
        .nx-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .nx-page-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
        }

        /* ── Tables ── */
        .table thead th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #6b7280;
            font-weight: 600;
            border-bottom: 2px solid #f0f0f0;
            background: #fafafa;
        }
        .table td { vertical-align: middle; font-size: 13.5px; }

        /* ── Bouton hamburger (visible uniquement mobile < 768px) ── */
        #nx-burger {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: #111827;
            cursor: pointer;
            padding: 0 8px 0 0;
            line-height: 1;
        }

        /* ── Overlay sombre derrière la sidebar ouverte sur mobile ── */
        #nx-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 99;
        }

        /* ── Responsive : sidebar en tiroir sur mobile ── */
        @media (max-width: 768px) {
            #nx-sidebar { transform: translateX(-100%); }   /* Caché hors écran par défaut */
            #nx-sidebar.open { transform: translateX(0); }  /* Visible quand .open est ajouté par JS */
            #nx-topbar, #nx-content { left: 0; margin-left: 0; }
            #nx-burger { display: block; }                  /* Bouton hamburger visible */
        }
    </style>
</head>
<body>

<!-- Notification flash AJAX (coin haut-droite, cachée par défaut via CSS display:none) -->
<div id="nx-notification"></div>

<!-- Overlay cliquable pour fermer la sidebar sur mobile -->
<div id="nx-overlay" onclick="nxToggleSidebar()"></div>

<!-- SIDEBAR : navigation principale fixe à gauche -->
<div id="nx-sidebar">

    <!-- Logo + nom de l'application -->
    <a href="<?= site_url('admin/dashboard') ?>" class="nx-brand">
        <img src="<?= base_url('img/Logo_Nexus.png') ?>" alt="NEXUS">
        <span class="nx-brand-name">NEXUS</span>
    </a>

    <!-- Navigation principale -->
    <nav class="nx-nav">
        <div class="nx-nav-section">Navigation</div>

        <a href="<?= site_url('admin/dashboard') ?>"
           class="nx-nav-item <?= (uri_string() === 'admin/dashboard') ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge"></i>
            Dashboard
        </a>

        <div class="nx-nav-section">Gestion</div>

        <a href="<?= site_url('admin/poles') ?>"
           class="nx-nav-item <?= (str_starts_with(uri_string(), 'admin/poles')) ? 'active' : '' ?>">
            <i class="fa-solid fa-building-columns"></i>
            Pôles
        </a>

        <a href="<?= site_url('admin/formations') ?>"
           class="nx-nav-item <?= (str_starts_with(uri_string(), 'admin/formations')) ? 'active' : '' ?>">
            <i class="fa-solid fa-book-open"></i>
            Formations
        </a>

        <a href="<?= site_url('admin/sessions') ?>"
           class="nx-nav-item <?= (str_starts_with(uri_string(), 'admin/sessions')) ? 'active' : '' ?>">
            <i class="fa-solid fa-calendar-days"></i>
            Sessions
        </a>

        <a href="<?= site_url('admin/inscriptions') ?>"
           class="nx-nav-item <?= (str_starts_with(uri_string(), 'admin/inscriptions')) ? 'active' : '' ?>">
            <i class="fa-solid fa-users"></i>
            Inscriptions
        </a>

        <a href="<?= site_url('admin/comptes') ?>"
           class="nx-nav-item <?= (str_starts_with(uri_string(), 'admin/comptes')) ? 'active' : '' ?>">
            <i class="fa-solid fa-user-gear"></i>
            Comptes
        </a>
    </nav>

    <!-- Footer sidebar : utilisateur connecté + bouton de déconnexion -->
    <div class="nx-sidebar-footer">
        <div class="nx-avatar">
            <?= strtoupper(substr(session()->get('prenom') ?? 'A', 0, 1) . substr(session()->get('nom') ?? '', 0, 1)) ?>
        </div>
        <div>
            <div class="nx-user-name">
                <?= esc(session()->get('prenom') . ' ' . session()->get('nom')) ?>
            </div>
            <div class="nx-user-role">
                <?= esc(session()->get('role') ?? 'Admin') ?>
            </div>
        </div>
        <a href="<?= site_url('logout') ?>" class="nx-logout" title="Déconnexion">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
    </div>
</div>

<!-- TOPBAR : barre du haut avec breadcrumb et badge de rôle -->
<div id="nx-topbar">
    <!-- Bouton hamburger : visible uniquement sur mobile, ouvre la sidebar -->
    <button id="nx-burger" onclick="nxToggleSidebar()"><i class="fa-solid fa-bars"></i></button>
    <div class="nx-breadcrumb">
        <span>Admin</span>
        <span>›</span>
        <span class="nx-breadcrumb-current"><?= esc($title ?? '') ?></span>
    </div>
    <div>
        <span class="nx-role-badge"><?= esc(session()->get('role') ?? 'Admin') ?></span>
    </div>
</div>

<!-- CONTENU PRINCIPAL : zone scrollable à droite de la sidebar -->
<div id="nx-content">
    <?= $content ?>
</div>

<!-- Scripts : jQuery, DataTables, Bootstrap -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts globaux : sidebar mobile + notification AJAX -->
<script>
/* Ouvre ou ferme la sidebar sur mobile en basculant la classe .open */
function nxToggleSidebar() {
    const sidebar = document.getElementById('nx-sidebar');
    const overlay = document.getElementById('nx-overlay');
    const isOpen  = sidebar.classList.toggle('open');
    overlay.style.display = isOpen ? 'block' : 'none';
}

/* Affiche une notification temporaire en haut à droite (succès ou erreur) */
function showNotification(message, isSuccess) {
    const $n = $('#nx-notification');
    $n.removeClass('success error')
      .addClass(isSuccess ? 'success' : 'error')
      .text(message)
      .fadeIn(200);
    setTimeout(function() { $n.fadeOut(400); }, 3500);
}
</script>

<!-- Scripts spécifiques à la page (DataTables, AJAX delete, etc.) -->
<?= $scripts ?? '' ?>

</body>
</html>
