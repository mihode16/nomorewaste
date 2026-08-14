<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $administrateur
 */
$ad = $administrateur; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/administrateurs'); ?>" class="btn btn-outline-secondary">
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
        <form method="POST" action="<?php echo url('/admin/administrateurs/' . (int)$ad['id']); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($ad['email']); ?>" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" name="mot_de_passe" class="form-control" placeholder="Laisser vide pour ne pas changer">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($ad['nom']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prénom *</label>
                    <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($ad['prenom']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="telephone" class="form-control" value="<?php echo htmlspecialchars($ad['telephone'] ?? ''); ?>">
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Adresse</label>
                    <textarea name="adresse" class="form-control" rows="2"><?php echo htmlspecialchars($ad['adresse'] ?? ''); ?></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </div>
        </form>
    </div>
</div>
