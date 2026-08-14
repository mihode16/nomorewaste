<?php
/**
 * @var string $titre
 */
?>
<section class="hero-section text-white rounded-4 mb-5 overflow-hidden position-relative">
    <div class="hero-bg" style="background-image: url('https://images.unsplash.com/photo-1542838132-92c53300491e?w=1600&q=80');"></div>
    <div class="hero-overlay"></div>
    <div class="position-relative px-4 px-md-5 py-5 text-center text-md-start">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3"><?php echo __('home_hero'); ?></h1>
                <p class="lead mb-4"><?php echo __('home_intro'); ?></p>
                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                    <a href="<?php echo url('/adherer'); ?>" class="btn btn-light btn-lg fw-semibold"><?php echo __('nav_adhesion'); ?></a>
                    <a href="<?php echo url('/services'); ?>" class="btn btn-outline-light btn-lg"><?php echo __('nav_services'); ?></a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="text-center mb-4">
    <h2 class="fw-bold">Rejoignez le mouvement</h2>
    <p class="text-muted">Trois façons de participer, selon votre profil.</p>
</div>

<div class="row g-4 mb-5">
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm text-center hover-card">
            <div class="card-body d-flex flex-column p-4">
                <div class="mx-auto mb-3 rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center action-icon">
                    <i class="bi bi-calendar-heart"></i>
                </div>
                <h5>Demander un service</h5>
                <p class="text-muted small flex-grow-1">
                    Cours de cuisine, conseils anti-gaspillage, partage de véhicules... Devenez adhérent pour
                    accéder à nos services solidaires.
                </p>
                <a href="<?php echo url('/adherer'); ?>" class="btn btn-outline-success mt-2">Devenir adhérent</a>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm text-center hover-card">
            <div class="card-body d-flex flex-column p-4">
                <div class="mx-auto mb-3 rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center action-icon">
                    <i class="bi bi-person-heart"></i>
                </div>
                <h5>Devenir bénévole</h5>
                <p class="text-muted small flex-grow-1">
                    Participez aux collectes, aux tournées de distribution ou animez un atelier. Chaque geste
                    compte.
                </p>
                <a href="<?php echo url('/devenir-benevole'); ?>" class="btn btn-outline-success mt-2">Devenir bénévole</a>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-lg-4">
        <div class="card h-100 border-0 shadow-sm text-center hover-card">
            <div class="card-body d-flex flex-column p-4">
                <div class="mx-auto mb-3 rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center action-icon">
                    <i class="bi bi-shop"></i>
                </div>
                <h5>Donner mes invendus</h5>
                <p class="text-muted small flex-grow-1">
                    Vous êtes commerçant ? Donnez vos invendus plutôt que de les jeter et planifiez vos
                    collectes en quelques clics.
                </p>
                <a href="<?php echo url('/devenir-commercant'); ?>" class="btn btn-outline-success mt-2">Je suis commerçant</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 align-items-center mb-5">
    <div class="col-12 col-lg-6 order-2 order-lg-1">
        <h3 class="fw-bold"><?php echo __('home_mission'); ?></h3>
        <p class="text-muted"><?php echo __('home_mission_text'); ?></p>
        <ul class="list-unstyled">
            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Collecte quotidienne des invendus</li>
            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Stockage et traçabilité par code-barres</li>
            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Tournées de distribution vers les associations</li>
            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Services aux adhérents (cuisine, réparation, covoiturage…)</li>
        </ul>
    </div>
    <div class="col-12 col-lg-6 order-1 order-lg-2">
        <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=900&q=80"
             alt="Cagette de légumes frais" class="img-fluid rounded-4 shadow-sm w-100 ratio-img" loading="lazy">
    </div>
</div>

<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h3 class="fw-bold mb-3"><?php echo __('home_agencies'); ?></h3>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light h-100">
                            <i class="bi bi-geo-alt-fill text-success fs-4"></i>
                            <div class="fw-semibold">Paris</div>
                            <div class="text-muted small">Siège social</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light h-100">
                            <i class="bi bi-geo-alt text-success fs-4"></i>
                            <div class="fw-semibold">Nantes</div>
                            <div class="text-muted small">Agence locale</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light h-100">
                            <i class="bi bi-geo-alt text-success fs-4"></i>
                            <div class="fw-semibold">Marseille</div>
                            <div class="text-muted small">Agence locale</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light h-100">
                            <i class="bi bi-geo-alt text-success fs-4"></i>
                            <div class="fw-semibold">Limoges</div>
                            <div class="text-muted small">Agence locale</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row text-center g-3 mb-5">
    <div class="col-12 col-sm-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <i class="bi bi-people-fill text-success fs-2"></i>
                <h4 class="mt-2 mb-0">200+</h4>
                <p class="text-muted mb-0">Bénévoles actifs</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <i class="bi bi-briefcase-fill text-success fs-2"></i>
                <h4 class="mt-2 mb-0">14</h4>
                <p class="text-muted mb-0">Salariés en CDI</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <i class="bi bi-heart-fill text-success fs-2"></i>
                <h4 class="mt-2 mb-0">6</h4>
                <p class="text-muted mb-0">Services proposés</p>
            </div>
        </div>
    </div>
</div>

<section class="rounded-4 overflow-hidden position-relative text-white text-center closing-band">
    <div class="hero-bg" style="background-image: url('https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=1600&q=80');"></div>
    <div class="hero-overlay"></div>
    <div class="position-relative p-5">
        <h3 class="fw-bold mb-2">Ensemble, réduisons le gaspillage</h3>
        <p class="mb-4">Chaque don, chaque heure de bénévolat, chaque adhésion fait la différence.</p>
        <a href="<?php echo url('/devenir-benevole'); ?>" class="btn btn-light btn-lg fw-semibold">Rejoindre l'aventure</a>
    </div>
</section>

<style>
    .hero-section, .closing-band { min-height: 320px; display: flex; align-items: center; }
    .hero-bg {
        position: absolute; inset: 0;
        background-size: cover; background-position: center;
    }
    .hero-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(120deg, rgba(25,52,33,.92) 0%, rgba(25,135,84,.75) 60%, rgba(25,135,84,.55) 100%);
    }
    .closing-band .hero-overlay { background: linear-gradient(0deg, rgba(15,30,20,.85), rgba(15,30,20,.65)); }
    .action-icon { width: 72px; height: 72px; font-size: 1.8rem; }
    @media (max-width: 767px) {
        .hero-section, .closing-band { min-height: 260px; }
    }
</style>
