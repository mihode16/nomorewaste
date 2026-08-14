<?php
/**
 * @var string $titre
 */
?>
<a href="<?php echo url('/'); ?>" class="text-decoration-none small text-muted d-inline-block mb-3">
    <i class="bi bi-arrow-left"></i> Retour à l'accueil
</a>
<div class="page-hero">
    <i class="bi bi-person-badge d-block mb-2"></i>
    <h1 class="mb-2"><?php echo __('adhesion_title'); ?></h1>
    <p class="lead mb-0"><?php echo __('adhesion_intro'); ?></p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="<?php echo url('/adherer'); ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mot de passe *</label>
                            <input type="password" name="mot_de_passe" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom *</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="telephone" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date début adhésion</label>
                            <input type="date" name="date_debut_adhesion" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-12 mb-4">
                            <label class="form-label">Adresse</label>
                            <textarea name="adresse" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success btn-lg"><?php echo __('btn_register'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm bg-success bg-opacity-10 h-100">
            <div class="card-body p-4">
                <h5 class="mb-3"><i class="bi bi-stars text-success"></i> Avantages adhérent</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Accès aux cours de cuisine</li>
                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Conseils anti-gaspillage</li>
                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Partage de véhicules</li>
                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Échange de services</li>
                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Services de réparation</li>
                </ul>
                <hr>
                <p class="text-muted small mb-0"><i class="bi bi-tag"></i> Cotisation annuelle : faible tarif</p>
            </div>
        </div>
    </div>
</div>
