<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $adherents
 * @var string $filtre_recherche
 * @var string $filtre_statut
 * @var float $prixAdhesion
 */
?>
<div class="d-flex justify-content-between align-items-center mb-1">
    <h2><?php echo htmlspecialchars($titre ?? 'Gestion des adhérents'); ?></h2>
    <a href="<?php echo url('/admin/adherents/creer'); ?>" class="btn btn-success btn-sm">
        <i class="bi bi-plus-circle"></i> Nouvel adhérent
    </a>
</div>
<p class="text-muted mb-4 d-flex align-items-center gap-2">
    Adhésion : <strong><?php echo number_format($prixAdhesion, 2, ',', ' '); ?> € / mois</strong>, donnant accès aux services de l'association.
    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" data-bs-toggle="modal" data-bs-target="#prixAdhesionModal">
        <i class="bi bi-pencil"></i> Modifier
    </button>
</p>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?> alert-dismissible fade show">
        <?php echo $_SESSION['flash']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- Barre de recherche et filtres -->
<form method="GET" action="<?php echo url('/admin/adherents'); ?>" class="row g-2 mb-4 align-items-end">
    <div class="col-md-4">
        <input type="text" name="recherche" class="form-control form-control-sm"
               placeholder="Rechercher par nom, prénom ou email..."
               value="<?php echo htmlspecialchars($filtre_recherche ?? ''); ?>">
    </div>
    <div class="col-md-3">
        <select name="statut" class="form-select form-select-sm">
            <option value="">Tous les statuts</option>
            <option value="en_attente" <?php echo ($filtre_statut ?? '') === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
            <option value="valide" <?php echo ($filtre_statut ?? '') === 'valide' ? 'selected' : ''; ?>>Validé</option>
        </select>
    </div>
    <div class="col-md-3 d-flex gap-1">
        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
            <i class="bi bi-search"></i> Filtrer
        </button>
        <a href="<?php echo url('/admin/adherents'); ?>" class="btn btn-secondary btn-sm flex-grow-1">
            <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
        </a>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Statut d'adhésion</th>
                    <th>Dates de l'adhésion</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($adherents)): ?>
                    <tr><td colspan="7" class="text-center">Aucun adhérent trouvé</td></tr>
                <?php else: ?>
                    <?php foreach ($adherents as $a): ?>
                        <?php
                        $estValide = (($a['statut_adhesion'] ?? '') === 'valide');
                        $fin = strtotime($a['date_fin_adhesion']);
                        $statutCalcule = statut_adhesion_calcule($a);
                        ?>
                        <tr>
                            <td><?php echo (int)$a['id']; ?></td>
                            <td><?php echo htmlspecialchars($a['prenom'] . ' ' . $a['nom']); ?></td>
                            <td><?php echo htmlspecialchars($a['email']); ?></td>
                            <td>
                                <?php if ($estValide): ?>
                                    <span class="badge bg-success">Validé</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">En attente</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($a['date_debut_adhesion'])); ?>
                                <br><small class="text-muted">au <?php echo date('d/m/Y', $fin); ?></small>
                            </td>
                            <td>
                                <?php if (!$estValide): ?>
                                    <span class="text-muted">—</span>
                                <?php else: ?>
                                    <span class="badge <?php echo $statutCalcule['classe']; ?>"><?php echo $statutCalcule['label']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?php echo url('/admin/adherents/' . $a['id']); ?>" class="btn btn-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo url('/admin/adherents/' . $a['id'] . '/modifier'); ?>" class="btn btn-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if (!$estValide): ?>
                                        <form method="POST" action="<?php echo url('/admin/adherents/' . $a['id'] . '/valider'); ?>" class="d-inline" onsubmit="return confirm('Valider cette demande d\'adhésion ?');">
                                            <button type="submit" class="btn btn-success" title="Valider">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#renouvelerModal<?php echo $a['id']; ?>" title="Renouveler">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    <?php endif; ?>
                                    <form method="POST" action="<?php echo url('/admin/adherents/' . $a['id'] . '/supprimer'); ?>" class="d-inline" onsubmit="return confirm('Confirmer la suppression de ce compte adhérent ?');">
                                        <button type="submit" class="btn btn-danger" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                <?php if ($estValide): ?>
                                    <div class="modal fade" id="renouvelerModal<?php echo $a['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="<?php echo url('/admin/adherents/' . $a['id'] . '/renouveler'); ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Renouveler l'adhésion</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label class="form-label">Durée</label>
                                                        <select name="duree_mois" class="form-select">
                                                            <option value="1">1 mois — <?php echo number_format($prixAdhesion * 1, 2, ',', ' '); ?> €</option>
                                                            <option value="6">6 mois — <?php echo number_format($prixAdhesion * 6, 2, ',', ' '); ?> €</option>
                                                            <option value="12" selected>12 mois — <?php echo number_format($prixAdhesion * 12, 2, ',', ' '); ?> €</option>
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

<!-- Modal modification du prix de l'adhésion -->
<div class="modal fade" id="prixAdhesionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo url('/admin/adherents/prix'); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier le prix de l'adhésion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Prix mensuel (€)</label>
                    <input type="number" name="prix" class="form-control" step="0.01" min="0.01"
                           value="<?php echo number_format($prixAdhesion, 2, '.', ''); ?>" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
