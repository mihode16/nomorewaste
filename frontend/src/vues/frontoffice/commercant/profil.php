<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $commercant
 */
$c = $commercant; ?>
<h2 class="mb-4">Mon profil</h2>

<div class="card border-0 shadow-sm" style="max-width: 700px;">
    <div class="card-body">
        <form method="POST" action="<?php echo url('/commercant/profil'); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($c['email']); ?>" disabled>
                    <small class="text-muted">Contactez l'association pour changer d'email.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">SIRET</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($c['siret']); ?>" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom du contact *</label>
                    <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($c['nom']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prénom du contact *</label>
                    <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($c['prenom']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="telephone" class="form-control" value="<?php echo htmlspecialchars($c['telephone'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Raison sociale *</label>
                    <input type="text" name="raison_sociale" class="form-control" value="<?php echo htmlspecialchars($c['raison_sociale']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Type de commerce</label>
                    <input type="text" name="type_commerce" class="form-control" value="<?php echo htmlspecialchars($c['type_commerce'] ?? ''); ?>">
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Adresse</label>
                    <textarea name="adresse" class="form-control" rows="2"><?php echo htmlspecialchars($c['adresse'] ?? ''); ?></textarea>
                </div>
                <div class="col-12 mb-3 form-check">
                    <input type="checkbox" name="est_renouvele_automatiquement" class="form-check-input" id="renouvelAuto" value="1" <?php echo !empty($c['est_renouvele_automatiquement']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="renouvelAuto">Renouveler mon adhésion automatiquement</label>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
</div>
