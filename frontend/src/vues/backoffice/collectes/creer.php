<?php include __DIR__ . '/../../layouts/entete.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo htmlspecialchars($titre); ?></h2>
                <a href="<?php echo url('/admin/collectes'); ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>

            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?>">
                    <?php echo $_SESSION['flash']['message']; ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?php echo url('/admin/collectes'); ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date et heure *</label>
                                <input type="datetime-local" name="date_heure_collecte" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Commerçant *</label>
                                <select name="commercant_id" class="form-select" required>
                                    <option value="">Sélectionner un commerçant</option>
                                    <?php foreach ($commercants as $c): ?>
                                        <option value="<?php echo (int)$c['id']; ?>">
                                            <?php echo htmlspecialchars($c['raison_sociale']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Adresse de collecte *</label>
                                <input type="text" name="adresse_collecte" class="form-control" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Commentaire</label>
                                <textarea name="commentaire" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save"></i> Enregistrer
                                </button>
                                <a href="<?php echo url('/admin/collectes'); ?>" class="btn btn-secondary">Annuler</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/pied.php'; ?>