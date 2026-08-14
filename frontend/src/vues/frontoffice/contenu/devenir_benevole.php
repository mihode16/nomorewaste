<?php
/**
 * @var string $titre
 * @var array $competences
 */
?>
<a href="<?php echo url('/'); ?>" class="text-decoration-none small text-muted d-inline-block mb-3">
    <i class="bi bi-arrow-left"></i> Retour à l'accueil
</a>
<div class="page-hero">
    <i class="bi bi-person-heart d-block mb-2"></i>
    <h1 class="mb-2">Devenir bénévole</h1>
    <p class="lead mb-0">Donnez de votre temps pour lutter contre le gaspillage alimentaire : collectes, tournées de distribution, animation d'ateliers... Rejoignez l'équipe !</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="<?php echo url('/devenir-benevole'); ?>">
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
                        <div class="col-12 mb-3">
                            <label class="form-label">Adresse</label>
                            <textarea name="adresse" class="form-control" rows="2"></textarea>
                        </div>
                        <?php if (!empty($competences)): ?>
                            <div class="col-12 mb-4">
                                <label class="form-label">Compétences (facultatif)</label>
                                <div class="row">
                                    <?php foreach ($competences as $c): ?>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="competences[]" value="<?php echo (int)$c['id']; ?>" id="comp<?php echo (int)$c['id']; ?>">
                                                <label class="form-check-label" for="comp<?php echo (int)$c['id']; ?>"><?php echo htmlspecialchars($c['nom']); ?></label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success btn-lg">Envoyer ma candidature</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm bg-success bg-opacity-10 mb-4">
            <div class="card-body p-4">
                <h5 class="mb-3"><i class="bi bi-stars text-success"></i> Pourquoi devenir bénévole ?</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Participer aux collectes chez nos commerçants partenaires</li>
                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Conduire ou accompagner une tournée de distribution</li>
                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Animer un atelier (cuisine, réparation, compostage...)</li>
                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Renseigner vos disponibilités depuis votre espace</li>
                </ul>
            </div>
        </div>
        <img src="https://images.unsplash.com/photo-1593113630400-ea4288922497?w=700&q=80"
             alt="Bénévoles préparant des paniers solidaires" class="img-fluid rounded-4 shadow-sm w-100 ratio-img mb-3" loading="lazy">
        <p class="text-muted small">Votre candidature sera examinée par l'association avant validation de votre compte.</p>
    </div>
</div>
