<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $service
 */
$s = $service; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/services'); ?>" class="btn btn-outline-secondary">
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
        <form method="POST" action="<?php echo url('/admin/services/' . (int)$s['id']); ?>">
            <div class="mb-3">
                <label class="form-label">Nom *</label>
                <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($s['nom']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Type</label>
                <input type="text" name="type" class="form-control" value="<?php echo htmlspecialchars($s['type'] ?? ''); ?>" placeholder="Ex : Atelier, Entraide, Conseil...">
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($s['description'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Enregistrer
            </button>
            <a href="<?php echo url('/admin/services'); ?>" class="btn btn-outline-secondary">Annuler</a>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        <h6 class="text-danger">Zone dangereuse</h6>
        <p class="text-muted small mb-2">La suppression d'un service supprime aussi tous ses plannings associés.</p>
        <form method="POST" action="<?php echo url('/admin/services/' . (int)$s['id'] . '/supprimer'); ?>" onsubmit="return confirm('Supprimer ce service et tous ses plannings ?');">
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash"></i> Supprimer ce service
            </button>
        </form>
    </div>
</div>
