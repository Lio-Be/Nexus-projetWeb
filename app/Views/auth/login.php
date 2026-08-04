<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-center py-5" style="min-height: calc(100vh - 62px - 90px); background:#f0f2f5;">
    <div class="w-100" style="max-width: 420px; padding: 0 16px;">

        <!-- Card -->
        <div class="rounded-3 shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">

            <!-- En-tête colorée -->
            <div class="text-center py-4" style="background: linear-gradient(135deg, #0A0F2E, #1a1040);">
                <img src="<?= base_url('img/Logo_Nexus.png') ?>" alt="NEXUS" style="height:42px; margin-bottom:10px;">
                <div style="font-size:11px; letter-spacing:3px; color:rgba(255,255,255,0.4);">CENTRE DE FORMATIONS</div>
            </div>

            <!-- Formulaire -->
            <div class="p-4" style="background:#fff;">
                <h5 class="fw-bold mb-1" style="color:#111827;">Connexion</h5>
                <p style="font-size:13px; color:#6b7280;" class="mb-4">Accède à ton espace personnel.</p>

                <?php if (session()->has('success')): ?>
                    <div class="alert alert-success d-flex align-items-center gap-2 py-2 px-3" style="font-size:13px;">
                        <i class="fa-solid fa-circle-check"></i>
                        <?= esc(session('success')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3" style="font-size:13px;">
                        <i class="fa-solid fa-circle-xmark"></i>
                        <?= esc(session('error')) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('login') ?>" method="post">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px; color:#374151;">
                            <i class="fa-solid fa-envelope fa-xs me-1" style="color:#00C8FF;"></i>Adresse email
                        </label>
                        <input type="email" name="email" class="form-control form-control-sm"
                               required style="border-color:#e5e7eb; font-size:13.5px;">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:13px; color:#374151;">
                            <i class="fa-solid fa-lock fa-xs me-1" style="color:#00C8FF;"></i>Mot de passe
                        </label>
                        <input type="password" name="password" class="form-control form-control-sm"
                               required style="border-color:#e5e7eb; font-size:13.5px;">
                    </div>

                    <button type="submit" class="btn btn-nexus w-100 py-2" style="font-size:14px;">
                        <i class="fa-solid fa-right-to-bracket me-2"></i>Se connecter
                    </button>
                </form>

                <p class="text-center mt-3 mb-0" style="font-size:13px; color:#6b7280;">
                    Pas encore de compte ?
                    <a href="<?= base_url('register') ?>" style="color:#00C8FF; text-decoration:none; font-weight:500;">
                        S'inscrire
                    </a>
                </p>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
