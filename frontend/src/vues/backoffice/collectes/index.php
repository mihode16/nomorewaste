<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $collectes
 * @var string $filtre_commercant
 * @var string $filtre_statut
 * @var string $filtre_date_debut
 * @var string $filtre_date_fin
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre ?? 'Gestion des collectes'); ?></h2>
    <a href="<?php echo url('/admin/collectes/creer'); ?>" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Nouvelle collecte
    </a>
</div>

<!-- Barre de recherche et filtres -->
<form method="GET" action="<?php echo url('/admin/collectes'); ?>" class="row g-2 mb-4 align-items-end">
    <div class="col-md-3">
        <input type="text" name="commercant" class="form-control form-control-sm" 
               placeholder="Rechercher par commerçant..." 
               value="<?php echo htmlspecialchars($filtre_commercant ?? ''); ?>">
    </div>
    <div class="col-md-2">
        <select name="statut" class="form-select form-select-sm">
            <option value="">Tous les statuts</option>
            <option value="Planifiée" <?php echo ($filtre_statut ?? '') === 'Planifiée' ? 'selected' : ''; ?>>Planifiée</option>
            <option value="Terminée" <?php echo ($filtre_statut ?? '') === 'Terminée' ? 'selected' : ''; ?>>Terminée</option>
        </select>
    </div>
    <div class="col-md-2">
        <input type="date" name="date_debut" class="form-control form-control-sm" 
               value="<?php echo htmlspecialchars($filtre_date_debut ?? ''); ?>">
    </div>
    <div class="col-md-2">
        <input type="date" name="date_fin" class="form-control form-control-sm" 
               value="<?php echo htmlspecialchars($filtre_date_fin ?? ''); ?>">
    </div>
    <div class="col-md-3 d-flex gap-1">
        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
            <i class="bi bi-search"></i> Filtrer
        </button>
        <a href="<?php echo url('/admin/collectes'); ?>" class="btn btn-secondary btn-sm flex-grow-1">
            <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
        </a>
    </div>
</form>

<!-- Messages flash -->
<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?> alert-dismissible fade show">
        <?php echo $_SESSION['flash']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- Tableau des collectes (inchangé) -->
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
                        <th>Validation</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($collectes)): ?>
                        <tr><td colspan="7" class="text-center">Aucune collecte trouvée</td></tr>
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
                                <td>
                                    <?php if (!empty($c['validee'])): ?>
                                        <span class="badge bg-success">Validée</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">En attente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statut = $c['statut'] ?? '';
                                    if ($statut === ''): ?>
                                        <span class="badge bg-secondary">En attente</span>
                                    <?php elseif ($statut === 'Planifiée'): ?>
                                        <span class="badge bg-primary">Planifiée</span>
                                    <?php elseif ($statut === 'Terminée'): ?>
                                        <span class="badge bg-success">Terminée</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark"><?php echo htmlspecialchars($statut); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo url('/admin/collectes/' . $c['id']); ?>" class="btn btn-info" title="Voir"><i class="bi bi-eye"></i></a>
                                        <?php if ($c['statut'] === '' && !$c['validee']): ?>
                                            <form method="POST" action="<?php echo url('/admin/collectes/' . $c['id'] . '/valider'); ?>" style="display:inline;">
                                                <button type="submit" class="btn btn-success" onclick="return confirm('Valider cette collecte ?')" title="Valider"><i class="bi bi-check-circle"></i></button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($c['statut'] === 'Planifiée'): ?>
                                            <a href="<?php echo url('/admin/collectes/' . $c['id'] . '/benevoles'); ?>" class="btn btn-warning" title="Gérer bénévoles"><i class="bi bi-people"></i></a>
                                        <?php endif; ?>
                                        <a href="<?php echo url('/admin/collectes/' . $c['id'] . '/modifier'); ?>" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                                        <form method="POST" action="<?php echo url('/admin/collectes/' . $c['id'] . '/supprimer'); ?>" style="display:inline;" onsubmit="return confirm('Supprimer ?')">
                                            <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                        <?php if ($c['statut'] === 'Terminée'): ?>
                                            <a href="<?php echo url('/admin/collectes/' . $c['id'] . '/pdf'); ?>" class="btn btn-outline-secondary" title="PDF récapitulatif" target="_blank"><i class="bi bi-file-earmark-pdf"></i></a>
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
</div>