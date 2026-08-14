<?php
/**
 * @var string $titre
 * @var string $pageActive
 */
?>
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
        <form method="POST" action="<?php echo url('/admin/services'); ?>">
            <div class="mb-3">
                <label class="form-label">Nom *</label>
                <input type="text" name="nom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Type</label>
                <input type="text" name="type" class="form-control" placeholder="Ex : Atelier, Entraide, Conseil...">
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="creerCompetence" onchange="document.getElementById('blocCompetence').classList.toggle('d-none', !this.checked)">
                <label class="form-check-label" for="creerCompetence">Créer une compétence associée à ce service</label>
            </div>
            <div id="blocCompetence" class="d-none border rounded p-3 mb-3 bg-light">
                <div class="mb-2">
                    <label class="form-label">Nom de la compétence *</label>
                    <input type="text" name="nouvelle_competence" class="form-control" placeholder="Ex : Compostage">
                </div>
                <div class="mb-0">
                    <label class="form-label">Description de la compétence</label>
                    <textarea name="description_competence" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle"></i> Créer le service
            </button>
            <a href="<?php echo url('/admin/services'); ?>" class="btn btn-outline-secondary">Annuler</a>
        </form>
    </div>
</div>
