<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'NEXUS') ?> — Centre de Formations</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --nx-bg:     #0A0F2E;
            --nx-accent: #00C8FF;
            --nx-purple: #7B2FFF;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Navbar ── */
        .nx-navbar {
            background: var(--nx-bg);
            padding: 0 24px;
            height: 62px;
        }
        .nx-navbar .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .nx-navbar .navbar-brand img {
            height: 36px;
            object-fit: contain;
        }
        .nx-brand-name {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 3px;
            background: linear-gradient(90deg, #00C8FF, #7B2FFF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .nx-navbar .nav-link {
            color: rgba(255,255,255,0.65) !important;
            font-size: 13.5px;
            padding: 6px 14px !important;
            border-radius: 6px;
            transition: all 0.15s;
        }
        .nx-navbar .nav-link:hover,
        .nx-navbar .nav-link.active {
            color: #fff !important;
            background: rgba(255,255,255,0.07);
        }
        .btn-nx-outline {
            border: 1px solid rgba(0,200,255,0.5);
            color: var(--nx-accent) !important;
            font-size: 13px;
            padding: 5px 16px !important;
            border-radius: 20px;
            transition: all 0.15s;
        }
        .btn-nx-outline:hover {
            background: var(--nx-accent);
            color: var(--nx-bg) !important;
        }
        .btn-nx-fill {
            background: linear-gradient(90deg, #00C8FF, #7B2FFF);
            color: #fff !important;
            font-size: 13px;
            padding: 5px 16px !important;
            border-radius: 20px;
            border: none;
            transition: opacity 0.15s;
        }
        .btn-nx-fill:hover { opacity: 0.85; }

        /* ── Menu mobile : fond sombre pour que les liens restent lisibles ── */
        @media (max-width: 991px) {
            .collapse.navbar-collapse {
                background: var(--nx-bg);
                padding: 8px 0 12px;
                border-top: 1px solid rgba(255,255,255,0.07);
                margin: 0 -24px;
                padding-left: 24px;
                padding-right: 24px;
            }
        }

        /* ── Footer ── */
        .nx-footer {
            background: var(--nx-bg);
            color: rgba(255,255,255,0.45);
            font-size: 13px;
            padding: 28px 24px;
            margin-top: auto;
        }
        .nx-footer a { color: rgba(255,255,255,0.45); text-decoration: none; transition: color 0.15s; }
        .nx-footer a:hover { color: #fff; }
        .nx-footer .social-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            object-fit: contain;
            opacity: 0.6;
            transition: opacity 0.15s;
        }
        .nx-footer .social-icon:hover { opacity: 1; }

        /* ── Contenu ── */
        main { flex: 1; }

        /* ── Bouton principal générique ── */
        .btn-nexus {
            background: linear-gradient(90deg, #00C8FF, #7B2FFF);
            color: #fff;
            border: none;
            font-weight: 500;
            border-radius: 8px;
            transition: opacity 0.15s;
        }
        .btn-nexus:hover { opacity: 0.88; color: #fff; }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body>

<!-- NAVBAR -->
<nav class="nx-navbar navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= base_url('/') ?>">
            <img src="<?= base_url('img/Logo_Nexus.png') ?>" alt="NEXUS">
            <span class="nx-brand-name">NEXUS</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nx-nav"
                style="color:rgba(255,255,255,0.6);">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="nx-nav">
            <ul class="navbar-nav me-auto ms-3 gap-1">
                <li class="nav-item">
                    <a class="nav-link <?= (uri_string() === '') ? 'active' : '' ?>"
                       href="<?= base_url('/') ?>">
                        <i class="fa-solid fa-house fa-xs me-1"></i>Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (uri_string() === 'formations') ? 'active' : '' ?>"
                       href="<?= base_url('formations') ?>">
                        <i class="fa-solid fa-book-open fa-xs me-1"></i>Formations
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav gap-2 align-items-center">
                <?php if (session()->get('isLoggedIn')): ?>
                    <li class="nav-item">
                        <span style="color:rgba(255,255,255,0.55); font-size:13px;">
                            Bonjour, <?= esc(session()->get('prenom')) ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <?php
                            $role = session()->get('role');
                            $dashboardUrl = match($role) {
                                'Admin'     => site_url('admin/dashboard'),
                                'Formateur' => site_url('formateur/dashboard'),
                                default     => site_url('etudiant/dashboard'),
                            };
                        ?>
                        <a class="nav-link btn-nx-outline" href="<?= $dashboardUrl ?>">
                            <i class="fa-solid fa-gauge fa-xs me-1"></i>Mon espace
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-nx-fill" href="<?= site_url('logout') ?>">
                            <i class="fa-solid fa-right-from-bracket fa-xs me-1"></i>Déconnexion
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn-nx-fill" href="<?= base_url('login') ?>">
                            <i class="fa-solid fa-right-to-bracket fa-xs me-1"></i>Connexion
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- CONTENU -->
<main>
    <?= $this->renderSection('content') ?>
</main>

<!-- FOOTER -->
<footer class="nx-footer">
    <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <img src="<?= base_url('img/Logo_Nexus.png') ?>" alt="NEXUS" style="height:28px; opacity:0.7;">
                <span style="font-size:12px; letter-spacing:2px; color:rgba(255,255,255,0.3);">NEXUS</span>
            </div>
            <div class="d-flex gap-2">
                <a target="_blank" href="https://x.com/" title="X">
                    <img src="<?= base_url('img/twitter.png') ?>" alt="X" class="social-icon">
                </a>
                <a target="_blank" href="https://facebook.com/" title="Facebook">
                    <img src="<?= base_url('img/facebook.png') ?>" alt="Facebook" class="social-icon">
                </a>
                <a target="_blank" href="https://instagram.com/" title="Instagram">
                    <img src="<?= base_url('img/instagram.png') ?>" alt="Instagram" class="social-icon">
                </a>
            </div>
            <p class="mb-0">&copy; 2026 NEXUS — Centre de Formations. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>

</body>
</html>
