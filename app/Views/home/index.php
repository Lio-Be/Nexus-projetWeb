<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<!-- HERO -->
<section style="background: linear-gradient(135deg, #0A0F2E 0%, #1a1040 60%, #0d1a3a 100%); min-height: 520px; display:flex; align-items:center;">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge mb-3 px-3 py-2" style="background:rgba(0,200,255,0.15); color:#00C8FF; font-size:12px; letter-spacing:1px; border: 1px solid rgba(0,200,255,0.3);">
                    <i class="fa-solid fa-graduation-cap me-1"></i> Centre de Formations Professionnelles
                </span>
                <h1 class="fw-bold mb-3" style="font-size:2.6rem; line-height:1.2; color:#fff;">
                    L'école des métiers du<br>
                    <span style="background: linear-gradient(90deg, #00C8FF, #7B2FFF); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">
                        numérique et du jeu vidéo
                    </span>
                </h1>
                <p class="mb-4" style="color:rgba(255,255,255,0.65); font-size:15px; line-height:1.7;">
                    Développeur web, designer UX, data scientist, expert en marketing digital ou chef de projet :
                    chez Nexus, tu construis des compétences concrètes pour les métiers tech d'aujourd'hui et de demain.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?= base_url('formations') ?>" class="btn btn-nexus px-4 py-2">
                        <i class="fa-solid fa-book-open me-2"></i>Découvrir les formations
                    </a>
                    <a href="<?= base_url('register') ?>" class="btn px-4 py-2"
                       style="border:1px solid rgba(255,255,255,0.25); color:rgba(255,255,255,0.8); border-radius:8px;">
                        <i class="fa-solid fa-user-plus me-2"></i>S'inscrire gratuitement
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="<?= base_url('img/02.jpg') ?>" alt="Campus Nexus"
                     class="img-fluid rounded-3 shadow-lg"
                     style="max-height:360px; object-fit:cover; width:100%; border: 2px solid rgba(0,200,255,0.2);">
            </div>
        </div>
    </div>
</section>

<!-- ATOUTS -->
<section class="py-5" style="background:#f8f9fa;">
    <div class="container">
        <h2 class="text-center fw-bold mb-2" style="color:#111827;">Pourquoi choisir Nexus ?</h2>
        <p class="text-center mb-5" style="color:#6b7280; font-size:14px;">Un apprentissage orienté pratique avec des formateurs issus du terrain.</p>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-4 rounded-3 h-100" style="background:#fff; border:1px solid #e5e7eb; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    <div class="mb-3" style="width:44px; height:44px; border-radius:10px; background:rgba(0,200,255,0.1); display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-laptop-code" style="color:#00C8FF; font-size:18px;"></i>
                    </div>
                    <h5 class="fw-semibold mb-2" style="color:#111827; font-size:15px;">Projets concrets</h5>
                    <p style="color:#6b7280; font-size:13.5px; line-height:1.7; margin:0;">
                        Chaque formation est construite autour de projets réels. Tu sors avec un portfolio, pas juste un diplôme.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-3 h-100" style="background:#fff; border:1px solid #e5e7eb; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    <div class="mb-3" style="width:44px; height:44px; border-radius:10px; background:rgba(123,47,255,0.1); display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-users" style="color:#7B2FFF; font-size:18px;"></i>
                    </div>
                    <h5 class="fw-semibold mb-2" style="color:#111827; font-size:15px;">Formateurs du terrain</h5>
                    <p style="color:#6b7280; font-size:13.5px; line-height:1.7; margin:0;">
                        Tous nos formateurs sont des professionnels en activité. Ils t'apportent des retours d'expérience réels.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-3 h-100" style="background:#fff; border:1px solid #e5e7eb; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                    <div class="mb-3" style="width:44px; height:44px; border-radius:10px; background:rgba(0,200,255,0.1); display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-wifi" style="color:#00C8FF; font-size:18px;"></i>
                    </div>
                    <h5 class="fw-semibold mb-2" style="color:#111827; font-size:15px;">En ligne & présentiel</h5>
                    <p style="color:#6b7280; font-size:13.5px; line-height:1.7; margin:0;">
                        Choisis le format qui te convient. Nos sessions sont disponibles à distance ou dans nos locaux.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section style="background: linear-gradient(135deg, #0A0F2E, #1a1040);">
    <div class="container py-5 text-center">
        <h2 class="fw-bold mb-2" style="color:#fff;">Prêt à démarrer ton parcours ?</h2>
        <p class="mb-4" style="color:rgba(255,255,255,0.6); font-size:14px;">
            Envie de voir les programmes, durées et prérequis ?<br>
            Découvre toutes nos formations et trouve le parcours qui te correspond.
        </p>
        <a href="<?= base_url('formations') ?>" class="btn btn-nexus px-5 py-2">
            <i class="fa-solid fa-arrow-right me-2"></i>Voir toutes les formations
        </a>
    </div>
</section>

<?= $this->endSection() ?>
