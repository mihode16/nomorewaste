<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $tournees
 * @var string $filtre_statut
 * @var string $filtre_destination
 * @var string $filtre_date
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/tournees/creer'); ?>" class="btn btn-success"><i class="bi bi-plus-circle"></i> Planifier</a>
</div>

<!-- Barre de recherche et filtres -->
<form method="GET" action="<?php echo url('/admin/tournees'); ?>" class="row g-2 mb-4 align-items-end">
    <div class="col-md-3">
        <input type="text" name="destination" class="form-control form-control-sm"
               placeholder="Rechercher par destination..."
               value="<?php echo htmlspecialchars($filtre_destination ?? ''); ?>">
    </div>
    <div class="col-md-2">
        <select name="statut" class="form-select form-select-sm">
            <option value="">Tous les statuts</option>
            <option value="Prévue" <?php echo ($filtre_statut ?? '') === 'Prévue' ? 'selected' : ''; ?>>Prévue</option>
            <option value="Terminée" <?php echo ($filtre_statut ?? '') === 'Terminée' ? 'selected' : ''; ?>>Terminée</option>
        </select>
    </div>
    <div class="col-md-2">
        <input type="date" name="date" class="form-control form-control-sm"
               value="<?php echo htmlspecialchars($filtre_date ?? ''); ?>">
    </div>
    <div class="col-md-3 d-flex gap-1">
        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
            <i class="bi bi-search"></i> Filtrer
        </button>
        <a href="<?php echo url('/admin/tournees'); ?>" class="btn btn-secondary btn-sm flex-grow-1">
            <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
        </a>
    </div>
</form>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Départ</th>
                    <th>Adresse départ</th>
                    <th>Destination</th>
                    <th>Nb bénévoles</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tournees)): ?>
                    <tr><td colspan="7" class="text-center">Aucune tournée trouvée</td></tr>
                <?php else: ?>
                    <?php foreach ($tournees as $t): ?>
                        <tr>
                            <td><?php echo (int)$t['id']; ?></td>
                            <td><?php echo format_datetime($t['date_heure_depart']); ?></td>
                            <td><?php echo htmlspecialchars($t['adresse_depart']); ?></td>
                            <td><?php echo htmlspecialchars($t['lieu_distribution']['nom'] ?? '-'); ?></td>
                            <td>
                                <?php 
                                // Compter 1 chauffeur + bénévoles supplémentaires
                                $nbBenevoles = 1 + count($t['benevoles'] ?? []);
                                echo (int)$nbBenevoles;
                                ?>
                            </td>
                            <td><?php echo badge_statut($t['statut']); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo url('/admin/tournees/' . $t['id']); ?>" class="btn btn-info" title="Voir"><i class="bi bi-eye"></i></a>
                                    <a href="<?php echo url('/admin/tournees/' . $t['id'] . '/modifier'); ?>" class="btn btn-primary" title="Modifier"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="<?php echo url('/admin/tournees/' . $t['id'] . '/supprimer'); ?>" style="display:inline;" onsubmit="return confirm('Supprimer cette tournée ?');">
                                        <button type="submit" class="btn btn-danger" title="Supprimer"><i class="bi bi-trash"></i></button>
                                    </form>
                                    <?php if ($t['statut'] === 'Terminée'): ?>
                                        <a href="<?php echo url('/admin/tournees/' . $t['id'] . '/pdf'); ?>" class="btn btn-outline-secondary" title="PDF récapitulatif" target="_blank"><i class="bi bi-file-earmark-pdf"></i></a>
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