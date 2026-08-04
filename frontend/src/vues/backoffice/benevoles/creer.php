<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/benevoles'); ?>" class="btn btn-secondary">Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo url('/admin/benevoles'); ?>">
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
                <div class="col-12 mb-3">
                    <label class="form-label">Compétences</label>
                    <div class="row">
                        <?php foreach ($competences as $comp): ?>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="competences[]" value="<?php echo (int)$comp['id']; ?>" id="comp<?php echo (int)$comp['id']; ?>">
                                    <label class="form-check-label" for="comp<?php echo (int)$comp['id']; ?>"><?php echo htmlspecialchars($comp['nom']); ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success">Inscrire le bénévole</button>
                </div>
            </div>
        </form>
    </div>
</div>
