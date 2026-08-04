<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/tournees/creer'); ?>" class="btn btn-success"><i class="bi bi-plus-circle"></i> Planifier</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Départ</th>
                    <th>Adresse départ</th>
                    <th>Destination</th>
                    <th>Bénévole</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tournees)): ?>
                    <tr><td colspan="7" class="text-center">Aucune tournée</td></tr>
                <?php else: ?>
                    <?php foreach ($tournees as $t): ?>
                        <tr>
                            <td><?php echo (int)$t['id']; ?></td>
                            <td><?php echo format_datetime($t['date_heure_depart']); ?></td>
                            <td><?php echo htmlspecialchars($t['adresse_depart']); ?></td>
                            <td><?php echo htmlspecialchars($t['lieu_distribution']['nom'] ?? '-'); ?></td>
                            <td>#<?php echo (int)($t['benevole_id'] ?? 0); ?></td>
                            <td><?php echo badge_statut($t['statut']); ?></td>
                            <td>
                                <a href="<?php echo url('/admin/tournees/' . $t['id']); ?>" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
