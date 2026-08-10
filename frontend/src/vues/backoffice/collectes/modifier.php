<?php include __DIR__ . '/../../layouts/entete.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/collectes'); ?>" class="btn btn-secondary">
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

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo url('/admin/collectes/' . (int)$collecte['id']); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date et heure *</label>
                    <input type="datetime-local" name="date_heure_collecte" class="form-control" 
                           value="<?php echo date('Y-m-d\TH:i', strtotime($collecte['date_heure_collecte'])); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Commerçant *</label>
                    <select name="commercant_id" class="form-select" required>
                        <option value="">Sélectionner un commerçant</option>
                        <?php foreach ($commercants as $c): ?>
                            <option value="<?php echo (int)$c['id']; ?>" 
                                <?php echo ((int)$c['id'] === (int)$collecte['commercant_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['raison_sociale']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Adresse de collecte *</label>
                    <input type="text" name="adresse_collecte" class="form-control" 
                           value="<?php echo htmlspecialchars($collecte['adresse_collecte']); ?>" required>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Commentaire</label>
                    <textarea name="commentaire" class="form-control" rows="3"><?php echo htmlspecialchars($collecte['commentaire'] ?? ''); ?></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Mettre à jour
                    </button>
                    <a href="<?php echo url('/admin/collectes'); ?>" class="btn btn-secondary">Annuler</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/pied.php'; ?>c