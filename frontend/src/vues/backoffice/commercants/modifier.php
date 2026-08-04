<?php
$c = $commercant;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/commercants'); ?>" class="btn btn-secondary">Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo url('/admin/commercants/' . (int)$c['id']); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($c['email']); ?>" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($c['nom']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prénom *</label>
                    <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($c['prenom']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="telephone" class="form-control" value="<?php echo htmlspecialchars($c['telephone'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">SIRET *</label>
                    <input type="text" name="siret" class="form-control" value="<?php echo htmlspecialchars($c['siret']); ?>" required>
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
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date début adhésion</label>
                    <input type="date" name="date_debut_adhesion" class="form-control" value="<?php echo date('Y-m-d', strtotime($c['date_debut_adhesion'])); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date fin adhésion</label>
                    <input type="date" name="date_fin_adhesion" class="form-control" value="<?php echo date('Y-m-d', strtotime($c['date_fin_adhesion'])); ?>">
                </div>
                <div class="col-12 mb-3 form-check">
                    <input type="checkbox" name="est_renouvele_automatiquement" class="form-check-input" id="renouvelAuto" value="1" <?php echo !empty($c['est_renouvele_automatiquement']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="renouvelAuto">Renouvellement automatique</label>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </div>
        </form>
    </div>
</div>
