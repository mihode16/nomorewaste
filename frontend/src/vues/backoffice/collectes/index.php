<?php include __DIR__ . '/../../layouts/entete.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo htmlspecialchars($titre ?? 'Gestion des collectes'); ?></h2>
                <a href="<?php echo url('/admin/collectes/creer'); ?>" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Nouvelle collecte
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
                                    <th>Date / Heure</th>
                                    <th>Adresse</th>
                                    <th>Commerçant</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($collectes)): ?>
                                    <tr><td colspan="6" class="text-center">Aucune collecte trouvée</td></tr>
                                <?php else: ?>
                                    <?php foreach ($collectes as $c): ?>
                                        <tr>
                                            <td><?php echo (int)$c['id']; ?></td>
                                            <td><?php echo format_datetime($c['date_heure_collecte']); ?></td>
                                            <td><?php echo htmlspecialchars($c['adresse_collecte']); ?></td>
                                            <td>
                                                <?php 
                                                if (!empty($c['commercant'])) {
                                                    echo htmlspecialchars($c['commercant']['raison_sociale'] ?? $c['commercant']['nom'] ?? '');
                                                } else {
                                                    echo '#'.(int)$c['commercant_id'];
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo badge_statut($c['statut'] ?? 'Planifiée'); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo url('/admin/collectes/' . $c['id'] . '/modifier'); ?>" class="btn btn-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <?php if (($c['statut'] ?? '') !== 'Terminée'): ?>
                                                        <form method="POST" action="<?php echo url('/admin/collectes/' . $c['id'] . '/terminer'); ?>" style="display:inline;">
                                                            <button type="submit" class="btn btn-success" onclick="return confirm('Marquer comme terminée ?')">
                                                                <i class="bi bi-check2"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <form method="POST" action="<?php echo url('/admin/collectes/' . $c['id'] . '/supprimer'); ?>" style="display:inline;" onsubmit="return confirm('Confirmer la suppression ?')">
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
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