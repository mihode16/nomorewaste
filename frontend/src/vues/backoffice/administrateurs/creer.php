<?php
/**
 * @var string $titre
 * @var string $pageActive
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/administrateurs'); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="<?php echo url('/admin/administrateurs'); ?>">
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
                <div class="col-12">
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
</div>
