<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $benevole
 */
$b = $benevole; ?>
<h2 class="mb-4">Mon profil</h2>

<div class="card border-0 shadow-sm" style="max-width: 700px;">
    <div class="card-body">
        <form method="POST" action="<?php echo url('/benevole/profil'); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($b['email']); ?>" disabled>
                    <small class="text-muted">Contactez l'association pour changer d'email.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Statut de candidature</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($b['statut_candidature'] ?? ''); ?>" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($b['nom']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prénom *</label>
                    <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($b['prenom']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="telephone" class="form-control" value="<?php echo htmlspecialchars($b['telephone'] ?? ''); ?>">
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Adresse</label>
                    <textarea name="adresse" class="form-control" rows="2"><?php echo htmlspecialchars($b['adresse'] ?? ''); ?></textarea>
                </div>
                <?php if (!empty($b['competences'])): ?>
                    <div class="col-12 mb-3">
                        <label class="form-label">Compétences</label>
                        <div>
                            <?php foreach ($b['competences'] as $c): ?>
                                <span class="badge bg-secondary me-1"><?php echo htmlspecialchars($c['nom']); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="col-12">
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
</div>
