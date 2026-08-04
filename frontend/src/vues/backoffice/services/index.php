<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/services/plannings/creer'); ?>" class="btn btn-success"><i class="bi bi-calendar-plus"></i> Nouveau planning</a>
</div>

<h4 class="mb-3">Catalogue des services</h4>
<div class="row mb-5">
    <?php foreach ($services as $s): ?>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <span class="badge bg-info"><?php echo htmlspecialchars($s['type'] ?? ''); ?></span>
                    <h5 class="mt-2"><?php echo htmlspecialchars($s['nom']); ?></h5>
                    <p class="text-muted small"><?php echo htmlspecialchars($s['description'] ?? ''); ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<h4 class="mb-3">Plannings</h4>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Capacité</th>
                    <th>Bénévole</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($plannings)): ?>
                    <tr><td colspan="6" class="text-center">Aucun planning</td></tr>
                <?php else: ?>
                    <?php foreach ($plannings as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['service']['nom'] ?? 'Service #' . $p['service_id']); ?></td>
                            <td><?php echo format_datetime($p['date_heure_debut']); ?></td>
                            <td><?php echo format_datetime($p['date_heure_fin']); ?></td>
                            <td><?php echo (int)$p['capacite_max']; ?></td>
                            <td>#<?php echo (int)($p['benevole_id'] ?? 0); ?></td>
                            <td><?php echo badge_statut($p['statut']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
