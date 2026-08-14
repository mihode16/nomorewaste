<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $produits
 * @var string $filtre_recherche
 * @var string $filtre_categorie
 * @var string $filtre_statut
 * @var string $filtre_tri
 * @var array $categories
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/produits/creer'); ?>" class="btn btn-success"><i class="bi bi-plus-circle"></i> Ajouter</a>
</div>

<!-- Barre de recherche et filtres -->
<form method="GET" action="<?php echo url('/admin/produits'); ?>" class="row g-2 mb-4 align-items-end">
    <div class="col-md-3">
        <input type="text" name="recherche" class="form-control form-control-sm" 
               placeholder="Rechercher par code-barres ou nom..." 
               value="<?php echo htmlspecialchars($filtre_recherche ?? ''); ?>">
    </div>
    <div class="col-md-2">
        <select name="categorie" class="form-select form-select-sm">
            <option value="">Toutes les catégories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($filtre_categorie ?? '') === $cat ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="statut" class="form-select form-select-sm">
            <option value="">Tous les statuts</option>
            <option value="À venir" <?php echo ($filtre_statut ?? '') === 'À venir' ? 'selected' : ''; ?>>À venir</option>
            <option value="Stocké" <?php echo ($filtre_statut ?? '') === 'Stocké' ? 'selected' : ''; ?>>Stocké</option>
            <option value="À distribuer" <?php echo ($filtre_statut ?? '') === 'À distribuer' ? 'selected' : ''; ?>>À distribuer</option>
            <option value="Distribué" <?php echo ($filtre_statut ?? '') === 'Distribué' ? 'selected' : ''; ?>>Distribué</option>
        </select>
    </div>
    <div class="col-md-2">
        <select name="tri" class="form-select form-select-sm">
            <option value="date_peremption_asc" <?php echo ($filtre_tri ?? '') === 'date_peremption_asc' ? 'selected' : ''; ?>>Péremption (plus proche)</option>
            <option value="date_peremption_desc" <?php echo ($filtre_tri ?? '') === 'date_peremption_desc' ? 'selected' : ''; ?>>Péremption (plus lointaine)</option>
            <option value="nom_asc" <?php echo ($filtre_tri ?? '') === 'nom_asc' ? 'selected' : ''; ?>>Nom (A→Z)</option>
        </select>
    </div>
    <div class="col-md-3 d-flex gap-1">
        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
            <i class="bi bi-search"></i> Filtrer
        </button>
        <a href="<?php echo url('/admin/produits'); ?>" class="btn btn-secondary btn-sm flex-grow-1">
            <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
        </a>
    </div>
</form>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Code-barres</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Qté</th>
                    <th>Péremption</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($produits)): ?>
                    <tr><td colspan="7" class="text-center">Aucun produit</td></tr>
                <?php else: ?>
                    <?php foreach ($produits as $p): ?>
                        <tr>
                            <td>
                                <?php if (!empty($p['code_barre'])): ?>
                                    <img src="<?php echo url('/barcode/' . rawurlencode($p['code_barre']) . '.svg'); ?>" alt="Code-barres" style="height:30px; vertical-align:middle;">
                                    <br><small><?php echo htmlspecialchars($p['code_barre']); ?></small>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($p['nom']); ?></td>
                            <td><?php echo htmlspecialchars($p['categorie'] ?? '-'); ?></td>
                            <td><?php echo (int)$p['quantite']; ?></td>
                            <td><?php echo format_date($p['date_peremption']); ?></td>
                            <td><?php echo badge_statut($p['statut']); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if (!empty($p['collecte_id'])): ?>
                                        <a href="<?php echo url('/admin/collectes/' . $p['collecte_id']); ?>" class="btn btn-info" title="Voir la collecte"><i class="bi bi-eye"></i></a>
                                    <?php else: ?>
                                        <span class="btn btn-secondary btn-sm disabled" title="Aucune collecte"><i class="bi bi-eye"></i></span>
                                    <?php endif; ?>
                                    <a href="<?php echo url('/admin/produits/' . $p['id'] . '/modifier'); ?>" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="<?php echo url('/admin/produits/' . $p['id'] . '/supprimer'); ?>" class="d-inline" onsubmit="return confirm('Supprimer ?');">
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