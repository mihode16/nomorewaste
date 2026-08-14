<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $benevoles
 * @var string $filtre_nom
 * @var string $filtre_statut
 * @var string $filtre_competence_id
 * @var array $competences
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre ?? 'Gestion des bénévoles'); ?></h2>
    <div class="btn-group">
        <a href="<?php echo url('/admin/benevoles/calendrier'); ?>" class="btn btn-outline-success">
            <i class="bi bi-calendar3"></i> Calendrier des disponibilités
        </a>
        <a href="<?php echo url('/admin/benevoles/creer'); ?>" class="btn btn-success">
            <i class="bi bi-person-plus"></i> Inscrire
        </a>
    </div>
</div>

<!-- Barre de recherche et filtres -->
<form method="GET" action="<?php echo url('/admin/benevoles'); ?>" class="row g-2 mb-4 align-items-end">
    <div class="col-md-3">
        <input type="text" name="nom" class="form-control form-control-sm" 
               placeholder="Rechercher par nom ou prénom..." 
               value="<?php echo htmlspecialchars($filtre_nom ?? ''); ?>">
    </div>
    <div class="col-md-2">
        <select name="statut" class="form-select form-select-sm">
            <option value="">Tous les statuts</option>
            <option value="En attente" <?php echo ($filtre_statut ?? '') === 'En attente' ? 'selected' : ''; ?>>En attente</option>
            <option value="Validé" <?php echo ($filtre_statut ?? '') === 'Validé' ? 'selected' : ''; ?>>Validé</option>
            <option value="Refusé" <?php echo ($filtre_statut ?? '') === 'Refusé' ? 'selected' : ''; ?>>Refusé</option>
        </select>
    </div>
    <div class="col-md-2">
        <select name="competence_id" class="form-select form-select-sm">
            <option value="0">Toutes les compétences</option>
            <?php foreach ($competences as $comp): ?>
                <option value="<?php echo (int)$comp['id']; ?>" <?php echo ($filtre_competence_id ?? 0) == $comp['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($comp['nom']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 d-flex gap-1">
        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
            <i class="bi bi-search"></i> Filtrer
        </button>
        <a href="<?php echo url('/admin/benevoles'); ?>" class="btn btn-secondary btn-sm flex-grow-1">
            <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
        </a>
    </div>
</form>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?> alert-dismissible fade show">
        <?php echo $_SESSION['flash']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Compétences</th>
                    <th>Statut</th>
                    <th>Candidature</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($benevoles)): ?>
                    <tr><td colspan="7" class="text-center">Aucun bénévole</td></tr>
                <?php else: ?>
                    <?php foreach ($benevoles as $b): ?>
                        <tr>
                            <td><?php echo (int)$b['id']; ?></td>
                            <td><?php echo htmlspecialchars($b['prenom'] . ' ' . $b['nom']); ?></td>
                            <td><?php echo htmlspecialchars($b['email']); ?></td>
                            <td>
                                <?php if (!empty($b['competences'])): ?>
                                    <?php foreach ($b['competences'] as $comp): ?>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($comp['nom']); ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo badge_statut($b['statut_candidature']); ?></td>
                            <td><?php echo format_date($b['date_candidature']); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo url('/admin/benevoles/' . $b['id']); ?>" class="btn btn-primary" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if (($b['statut_candidature'] ?? '') === 'En attente'): ?>
                                        <form method="POST" action="<?php echo url('/admin/benevoles/' . $b['id'] . '/statut'); ?>" style="display:inline;">
                                            <input type="hidden" name="statut" value="Validé">
                                            <button type="submit" class="btn btn-success" title="Valider" onclick="return confirm('Valider ce bénévole ?');">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="<?php echo url('/admin/benevoles/' . $b['id'] . '/supprimer'); ?>" style="display:inline;" onsubmit="return confirm('Supprimer ce bénévole ?');">
                                        <button type="submit" class="btn btn-danger" title="Supprimer">
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