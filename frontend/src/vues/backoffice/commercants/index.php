<?php include __DIR__ . '/../../layouts/entete.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar p-0">
            <div class="p-3">
                <h4 class="text-white">NO MORE WASTE</h4>
                <hr class="border-light">
            </div>
            <nav class="nav flex-column">
                <a href="../dashboard.php" class="nav-link">
                    <i class="bi bi-speedometer2"></i> Tableau de bord
                </a>
                <a href="/admin/commercants" class="nav-link active">
                    <i class="bi bi-shop"></i> Commerçants
                </a>
                <a href="/admin/collectes" class="nav-link">
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

        <!-- Contenu principal -->
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo $titre ?? 'Gestion des commerçants'; ?></h2>
                <a href="creer.php" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Nouveau commerçant
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
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Raison sociale</th>
                                    <th>SIRET</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Adhésion</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($commercants)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Aucun commerçant trouvé</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($commercants as $c): ?>
                                        <tr>
                                            <td><?php echo $c['id']; ?></td>
                                            <td><?php echo htmlspecialchars($c['raison_sociale']); ?></td>
                                            <td><?php echo htmlspecialchars($c['siret']); ?></td>
                                            <td><?php echo htmlspecialchars($c['email']); ?></td>
                                            <td><?php echo htmlspecialchars($c['type_commerce'] ?? '-'); ?></td>
                                            <td>
                                                <?php echo date('d/m/Y', strtotime($c['date_debut_adhesion'])); ?>
                                                <br>
                                                <small class="text-muted">au <?php echo date('d/m/Y', strtotime($c['date_fin_adhesion'])); ?></small>
                                            </td>
                                            <td>
                                                <?php if (strtotime($c['date_fin_adhesion']) < time()): ?>
                                                    <span class="badge bg-danger">Expiré</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Actif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/admin/commercants/<?php echo $c['id']; ?>/modifier" class="btn btn-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#renouvelerModal<?php echo $c['id']; ?>">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </button>
                                                    <form method="POST" action="/admin/commercants/<?php echo $c['id']; ?>/supprimer" style="display:inline;" onsubmit="return confirm('Confirmer la suppression ?')">
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>

                                                <!-- Modal Renouvellement -->
                                                <div class="modal fade" id="renouvelerModal<?php echo $c['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="POST" action="/admin/commercants/<?php echo $c['id']; ?>/renouveler">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Renouveler l'adhésion</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="duree_mois" class="form-label">Durée en mois</label>
                                                                        <select name="duree_mois" id="duree_mois" class="form-select">
                                                                            <option value="6">6 mois</option>
                                                                            <option value="12" selected>12 mois (1 an)</option>
                                                                            <option value="24">24 mois (2 ans)</option>
                                                                            <option value="36">36 mois (3 ans)</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                    <button type="submit" class="btn btn-success">Renouveler</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/pied.php'; ?>