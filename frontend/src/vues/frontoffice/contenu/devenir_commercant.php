<?php
/**
 * @var string $titre
 */
?>
<a href="<?php echo url('/'); ?>" class="text-decoration-none small text-muted d-inline-block mb-3">
    <i class="bi bi-arrow-left"></i> Retour à l'accueil
</a>
<div class="page-hero">
    <i class="bi bi-shop d-block mb-2"></i>
    <h1 class="mb-2">Donner mes invendus</h1>
    <p class="lead mb-0">Vous êtes commerçant et souhaitez donner vos produits invendus plutôt que de les jeter ? Inscrivez votre établissement pour planifier vos premières collectes.</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="<?php echo url('/devenir-commercant'); ?>">
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
                            <label class="form-label">Nom du contact *</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom du contact *</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Raison sociale *</label>
                            <input type="text" name="raison_sociale" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SIRET *</label>
                            <input type="text" name="siret" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type de commerce</label>
                            <input type="text" name="type_commerce" class="form-control" placeholder="Ex : Boulangerie, Supermarché...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="telephone" class="form-control">
                        </div>
                        <div class="col-12 mb-4">
                            <label class="form-label">Adresse</label>
                            <textarea name="adresse" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success btn-lg">Envoyer ma demande</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm bg-success bg-opacity-10 mb-4">
            <div class="card-body p-4">
                <h5 class="mb-3"><i class="bi bi-stars text-success"></i> Comment ça marche ?</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Votre demande est validée par l'association</li>
                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Vous planifiez vos collectes depuis votre espace</li>
                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Nos bénévoles viennent récupérer vos invendus</li>
                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Vous suivez l'historique de vos dons</li>
                </ul>
            </div>
        </div>
        <img src="https://images.unsplash.com/photo-1506617564039-2f3b650b7010?w=700&q=80"
             alt="Étal de produits frais chez un commerçant" class="img-fluid rounded-4 shadow-sm w-100 ratio-img mb-3" loading="lazy">
        <p class="text-muted small">Un justificatif d'entreprise (SIRET) est nécessaire pour la validation.</p>
    </div>
</div>
