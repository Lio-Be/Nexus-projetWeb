<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-center py-5" style="background:#f0f2f5;">
    <div class="w-100" style="max-width: 560px; padding: 0 16px;">

        <!-- Card -->
        <div class="rounded-3 shadow-sm overflow-hidden" style="border: 1px solid #e5e7eb;">

            <!-- En-tête -->
            <div class="text-center py-4" style="background: linear-gradient(135deg, #0A0F2E, #1a1040);">
                <img src="<?= base_url('img/Logo_Nexus.png') ?>" alt="NEXUS" style="height:42px; margin-bottom:10px;">
                <div style="font-size:11px; letter-spacing:3px; color:rgba(255,255,255,0.4);">CENTRE DE FORMATIONS</div>
            </div>

            <!-- Formulaire -->
            <div class="p-4" style="background:#fff;">
                <h5 class="fw-bold mb-1" style="color:#111827;">Créer un compte</h5>
                <p style="font-size:13px; color:#6b7280;" class="mb-4">Rejoins la communauté Nexus et accède à nos formations.</p>

                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger py-2 px-3" style="font-size:13px;">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        <ul class="mb-0 mt-1 ps-3">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= base_url('register') ?>">
                    <?= csrf_field() ?>

                    <!-- Nom / Prénom -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px; color:#374151;">
                                <i class="fa-solid fa-user fa-xs me-1" style="color:#00C8FF;"></i>Nom
                            </label>
                            <input type="text" name="nom" class="form-control form-control-sm"
                                   value="<?= old('nom') ?>" required
                                   style="border-color:#e5e7eb; font-size:13.5px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px; color:#374151;">
                                Prénom
                            </label>
                            <input type="text" name="prenom" class="form-control form-control-sm"
                                   value="<?= old('prenom') ?>" required
                                   style="border-color:#e5e7eb; font-size:13.5px;">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px; color:#374151;">
                            <i class="fa-solid fa-envelope fa-xs me-1" style="color:#00C8FF;"></i>Adresse email
                        </label>
                        <input type="email" name="email" class="form-control form-control-sm"
                               value="<?= old('email') ?>" required
                               style="border-color:#e5e7eb; font-size:13.5px;">
                    </div>

                    <!-- Mot de passe -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px; color:#374151;">
                                <i class="fa-solid fa-lock fa-xs me-1" style="color:#00C8FF;"></i>Mot de passe
                            </label>
                            <input type="password" name="password" class="form-control form-control-sm"
                                   required style="border-color:#e5e7eb; font-size:13.5px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px; color:#374151;">
                                Confirmation
                            </label>
                            <input type="password" name="password_confirm" class="form-control form-control-sm"
                                   required style="border-color:#e5e7eb; font-size:13.5px;">
                        </div>
                    </div>

                    <!-- Séparateur optionnel -->
                    <div class="mb-3 mt-4" style="border-top:1px solid #f0f0f0; padding-top:16px;">
                        <p style="font-size:12px; color:#9ca3af; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.8px;">
                            Informations optionnelles
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px; color:#374151;">
                                <i class="fa-solid fa-location-dot fa-xs me-1" style="color:#7B2FFF;"></i>Adresse
                            </label>
                            <input type="text" name="adresse" class="form-control form-control-sm"
                                   value="<?= old('adresse') ?>"
                                   style="border-color:#e5e7eb; font-size:13.5px;">
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size:13px; color:#374151;">
                                    Code postal
                                </label>
                                <input type="text" name="code_postal" class="form-control form-control-sm"
                                       value="<?= old('code_postal') ?>"
                                       style="border-color:#e5e7eb; font-size:13.5px;">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size:13px; color:#374151;">
                                    <i class="fa-solid fa-cake-candles fa-xs me-1" style="color:#7B2FFF;"></i>Date de naissance
                                </label>
                                <input type="date" name="date_naissance" class="form-control form-control-sm"
                                       value="<?= old('date_naissance') ?>"
                                       style="border-color:#e5e7eb; font-size:13.5px;">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-nexus w-100 py-2 mt-2" style="font-size:14px;">
                        <i class="fa-solid fa-user-plus me-2"></i>Créer mon compte
                    </button>
                </form>

                <p class="text-center mt-3 mb-0" style="font-size:13px; color:#6b7280;">
                    Déjà un compte ?
                    <a href="<?= base_url('login') ?>" style="color:#00C8FF; text-decoration:none; font-weight:500;">
                        Se connecter
                    </a>
                </p>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
