<?php include __DIR__ . '/../../layouts/entete.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-0">
            <div class="p-3">
                <h4 class="text-white">NO MORE WASTE</h4>
                <hr class="border-light">
            </div>
            <nav class="nav flex-column">
                <a href="/admin/dashboard" class="nav-link">
                    <i class="bi bi-speedometer2"></i> Tableau de bord
                </a>
                <a href="/admin/commercants" class="nav-link">
                    <i class="bi bi-shop"></i> Commerçants
                </a>
                <a href="/admin/collectes" class="nav-link active">
                    <i class="bi bi-truck"></i> Collectes
                </a>
                <a href="/admin/produits" class="nav-link">
                    <i class="bi bi-box"></i> Produits
                </a>
                <a href="/admin/benevoles" class="nav-link">
                    <i class="bi bi-people"></i> Bénévoles
                </a>
                <a href="/admin/tournees" class="nav-link">
                    <i class="bi bi-route"></i> Tournées
                </a>
                <hr class="border-light">
                <a href="/admin/logout" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </nav>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo $titre; ?></h2>
                <a href="/admin/collectes" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="/admin/collectes/<?php echo $collecte['id']; ?>">
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
                                        <option value="<?php echo $c['id']; ?>" 
                                            <?php echo ($c['id'] == $collecte['commercant_id']) ? 'selected' : ''; ?>>
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
                                <a href="/admin/collectes" class="btn btn-secondary">Annuler</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/pied.php'; ?>