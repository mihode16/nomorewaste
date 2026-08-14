<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $adherent
 */
$a = $adherent; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/adherents'); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?> alert-dismissible fade show">
        <?php echo $_SESSION['flash']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="<?php echo url('/admin/adherents/' . (int)$a['id']); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($a['email']); ?>" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Statut d'adhésion</label>
                    <input type="text" class="form-control" value="<?php echo ($a['statut_adhesion'] ?? '') === 'valide' ? 'Validé' : 'En attente'; ?>" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($a['nom']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prénom *</label>
                    <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($a['prenom']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="telephone" class="form-control" value="<?php echo htmlspecialchars($a['telephone'] ?? ''); ?>">
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Adresse</label>
                    <textarea name="adresse" class="form-control" rows="2"><?php echo htmlspecialchars($a['adresse'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date début adhésion</label>
                    <input type="date" name="date_debut_adhesion" class="form-control" value="<?php echo date('Y-m-d', strtotime($a['date_debut_adhesion'])); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date fin adhésion</label>
                    <input type="date" name="date_fin_adhesion" class="form-control" value="<?php echo date('Y-m-d', strtotime($a['date_fin_adhesion'])); ?>">
                </div>
                <div class="col-12 mb-3 form-check">
                    <input type="checkbox" name="est_renouvele_automatiquement" class="form-check-input" id="renouvelAuto" value="1" <?php echo !empty($a['est_renouvele_automatiquement']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="renouvelAuto">Renouvellement automatique</label>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </div>
        </form>
    </div>
</div>
