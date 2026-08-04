<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/collectes/creer'); ?>" class="btn btn-success"><i class="bi bi-plus-circle"></i> Planifier</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Adresse</th>
                    <th>Commerçant</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($collectes)): ?>
                    <tr><td colspan="6" class="text-center">Aucune collecte</td></tr>
                <?php else: ?>
                    <?php foreach ($collectes as $c): ?>
                        <tr>
                            <td><?php echo (int)$c['id']; ?></td>
                            <td><?php echo format_datetime($c['date_heure_collecte']); ?></td>
                            <td><?php echo htmlspecialchars($c['adresse_collecte']); ?></td>
                            <td>#<?php echo (int)($c['commercant_id'] ?? 0); ?></td>
                            <td><?php echo badge_statut($c['statut']); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo url('/admin/collectes/' . $c['id'] . '/modifier'); ?>" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                                    <?php if ($c['statut'] !== 'Terminée'): ?>
                                        <form method="POST" action="<?php echo url('/admin/collectes/' . $c['id'] . '/terminer'); ?>" class="d-inline">
                                            <button type="submit" class="btn btn-success" title="Terminer"><i class="bi bi-check"></i></button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="<?php echo url('/admin/collectes/' . $c['id'] . '/supprimer'); ?>" class="d-inline" onsubmit="return confirm('Supprimer ?');">
                                        <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i></button>
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
