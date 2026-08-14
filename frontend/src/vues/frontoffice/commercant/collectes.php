<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $collectes
 * @var string $filtre_statut
 * @var bool $peutDemander
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Mes collectes</h2>
    <?php if ($peutDemander): ?>
        <a href="<?php echo url('/commercant/collectes/creer'); ?>" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Demander une collecte
        </a>
    <?php else: ?>
        <span class="text-muted small" title="Votre adhésion doit être validée et à jour">
            <i class="bi bi-lock"></i> Demande de collecte indisponible
        </span>
    <?php endif; ?>
</div>

<form method="GET" action="<?php echo url('/commercant/collectes'); ?>" class="row g-2 mb-4 align-items-end">
    <div class="col-md-3">
        <select name="statut" class="form-select form-select-sm">
            <option value="">Tous les statuts</option>
            <option value="Planifiée" <?php echo ($filtre_statut ?? '') === 'Planifiée' ? 'selected' : ''; ?>>Planifiée</option>
            <option value="Terminée" <?php echo ($filtre_statut ?? '') === 'Terminée' ? 'selected' : ''; ?>>Terminée</option>
        </select>
    </div>
    <div class="col-md-3 d-flex gap-1">
        <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search"></i> Filtrer</button>
        <a href="<?php echo url('/commercant/collectes'); ?>" class="btn btn-outline-secondary btn-sm">Réinitialiser</a>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Adresse</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($collectes)): ?>
                    <tr><td colspan="5" class="text-center">Aucune collecte trouvée</td></tr>
                <?php else: ?>
                    <?php foreach ($collectes as $c): ?>
                        <tr>
                            <td>#<?php echo (int)$c['id']; ?></td>
                            <td><?php echo format_datetime($c['date_heure_collecte']); ?></td>
                            <td><?php echo htmlspecialchars($c['adresse_collecte']); ?></td>
                            <td><?php echo badge_statut($c['statut'] === '' ? 'En attente' : $c['statut']); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo url('/commercant/collectes/' . $c['id']); ?>" class="btn btn-outline-secondary" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if (empty($c['validee'])): ?>
                                        <a href="<?php echo url('/commercant/collectes/' . $c['id'] . '/modifier'); ?>" class="btn btn-outline-primary" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($c['statut'] === 'Terminée'): ?>
                                        <a href="<?php echo url('/commercant/collectes/' . $c['id'] . '/pdf'); ?>" class="btn btn-outline-success" title="PDF récapitulatif" target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
