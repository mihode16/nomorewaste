<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $adherent
 */
$a = $adherent; ?>
<h2 class="mb-4">Mon profil</h2>

<div class="card border-0 shadow-sm" style="max-width: 700px;">
    <div class="card-body">
        <form method="POST" action="<?php echo url('/adherent/profil'); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($a['email']); ?>" disabled>
                    <small class="text-muted">Contactez l'association pour changer d'email.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="telephone" class="form-control" value="<?php echo htmlspecialchars($a['telephone'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($a['nom']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prénom *</label>
                    <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($a['prenom']); ?>" required>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Adresse</label>
                    <textarea name="adresse" class="form-control" rows="2"><?php echo htmlspecialchars($a['adresse'] ?? ''); ?></textarea>
                </div>
                <div class="col-12 mb-3 form-check">
                    <input type="checkbox" name="est_renouvele_automatiquement" class="form-check-input" id="renouvelAuto" value="1" <?php echo !empty($a['est_renouvele_automatiquement']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="renouvelAuto">Renouveler mon adhésion automatiquement</label>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
</div>
