<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $commercants
 * @var string $filtre_raison_sociale
 * @var string $filtre_statut
 * @var string $filtre_type
 * @var array $types
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre ?? 'Gestion des commerçants'); ?></h2>
    <a href="<?php echo url('/admin/commercants/creer'); ?>" class="btn btn-success btn-sm">
        <i class="bi bi-plus-circle"></i> Nouveau commerçant
    </a>
</div>

<!-- Barre de recherche et filtres -->
<form method="GET" action="<?php echo url('/admin/commercants'); ?>" class="row g-2 mb-4 align-items-end">
    <div class="col-md-3">
        <input type="text" name="raison_sociale" class="form-control form-control-sm" 
               placeholder="Rechercher par raison sociale..." 
               value="<?php echo htmlspecialchars($filtre_raison_sociale ?? ''); ?>">
    </div>
    <div class="col-md-3">
        <select name="statut" class="form-select form-select-sm">
            <option value="">Tous les statuts</option>
            <option value="en_attente" <?php echo ($filtre_statut ?? '') === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
            <option value="valide" <?php echo ($filtre_statut ?? '') === 'valide' ? 'selected' : ''; ?>>Validé</option>
            <option value="refuse" <?php echo ($filtre_statut ?? '') === 'refuse' ? 'selected' : ''; ?>>Refusé</option>
        </select>
    </div>
    <div class="col-md-3">
        <select name="type" class="form-select form-select-sm">
            <option value="">Tous les types</option>
            <?php foreach ($types as $t): ?>
                <option value="<?php echo htmlspecialchars($t); ?>" <?php echo ($filtre_type ?? '') === $t ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($t); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 d-flex gap-1">
        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
            <i class="bi bi-search"></i> Filtrer
        </button>
        <a href="<?php echo url('/admin/commercants'); ?>" class="btn btn-secondary btn-sm flex-grow-1">
            <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
        </a>
    </div>
</form>

<!-- Le tableau reste inchangé -->
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
                        <th>Statut d'adhésion</th>
                        <th>Dates de l'adhésion</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($commercants)): ?>
                        <tr><td colspan="9" class="text-center">Aucun commerçant trouvé</td></tr>
                    <?php else: ?>
                        <?php foreach ($commercants as $c): ?>
                            <?php
                            $statutAdhesion = $c['statut_adhesion'] ?? 'en_attente';
                            $estValide = ($statutAdhesion === 'valide');
                            ?>
                            <tr>
                                <td><?php echo (int)$c['id']; ?></td>
                                <td><?php echo htmlspecialchars($c['raison_sociale']); ?></td>
                                <td><?php echo htmlspecialchars($c['siret']); ?></td>
                                <td><?php echo htmlspecialchars($c['email']); ?></td>
                                <td><?php echo htmlspecialchars($c['type_commerce'] ?? '-'); ?></td>
                                <td>
                                    <?php if ($statutAdhesion === 'valide'): ?>
                                        <span class="badge bg-success">Validé</span>
                                    <?php elseif ($statutAdhesion === 'refuse'): ?>
                                        <span class="badge bg-danger">Refusé</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">En attente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($estValide): ?>
                                        <?php echo date('d/m/Y', strtotime($c['date_debut_adhesion'])); ?>
                                        <br><small class="text-muted">au <?php echo date('d/m/Y', strtotime($c['date_fin_adhesion'])); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($estValide): ?>
                                        <?php $statutCalcule = statut_adhesion_calcule($c); ?>
                                        <span class="badge <?php echo $statutCalcule['classe']; ?>"><?php echo $statutCalcule['label']; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?php echo url('/admin/commercants/' . $c['id'] . '/modifier'); ?>" class="btn btn-primary btn-sm" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if (!$estValide): ?>
                                            <form method="POST" action="<?php echo url('/admin/commercants/' . $c['id'] . '/valider'); ?>" class="d-inline" onsubmit="return confirm('Valider ce commerçant ?');">
                                                <button type="submit" class="btn btn-success btn-sm" title="Valider">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($estValide): ?>
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#renouvelerModal<?php echo $c['id']; ?>" title="Renouveler">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        <?php endif; ?>
                                        <form method="POST" action="<?php echo url('/admin/commercants/' . $c['id'] . '/supprimer'); ?>" class="d-inline" onsubmit="return confirm('Confirmer la suppression ?');">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <?php if ($estValide): ?>
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
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>