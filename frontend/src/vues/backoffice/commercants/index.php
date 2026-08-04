<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre ?? 'Gestion des commerçants'); ?></h2>
    <a href="<?php echo url('/admin/commercants/creer'); ?>" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Nouveau commerçant
    </a>
</div>

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
                        <tr><td colspan="8" class="text-center">Aucun commerçant trouvé</td></tr>
                    <?php else: ?>
                        <?php foreach ($commercants as $c): ?>
                            <tr>
                                <td><?php echo (int)$c['id']; ?></td>
                                <td><?php echo htmlspecialchars($c['raison_sociale']); ?></td>
                                <td><?php echo htmlspecialchars($c['siret']); ?></td>
                                <td><?php echo htmlspecialchars($c['email']); ?></td>
                                <td><?php echo htmlspecialchars($c['type_commerce'] ?? '-'); ?></td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($c['date_debut_adhesion'])); ?>
                                    <br><small class="text-muted">au <?php echo date('d/m/Y', strtotime($c['date_fin_adhesion'])); ?></small>
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
                                        <a href="<?php echo url('/admin/commercants/' . $c['id'] . '/modifier'); ?>" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#renouvelerModal<?php echo $c['id']; ?>"><i class="bi bi-arrow-repeat"></i></button>
                                        <form method="POST" action="<?php echo url('/admin/commercants/' . $c['id'] . '/supprimer'); ?>" class="d-inline" onsubmit="return confirm('Confirmer la suppression ?');">
                                            <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                    <div class="modal fade" id="renouvelerModal<?php echo $c['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="<?php echo url('/admin/commercants/' . $c['id'] . '/renouveler'); ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Renouveler l'adhésion</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <select name="duree_mois" class="form-select">
                                                            <option value="6">6 mois</option>
                                                            <option value="12" selected>12 mois</option>
                                                            <option value="24">24 mois</option>
                                                        </select>
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
