<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/produits/creer'); ?>" class="btn btn-success"><i class="bi bi-plus-circle"></i> Ajouter</a>
</div>

<form method="GET" action="<?php echo url('/admin/produits'); ?>" class="mb-3">
    <div class="input-group" style="max-width:400px">
        <input type="text" name="code_barre" class="form-control" placeholder="Rechercher par code-barres..." value="<?php echo htmlspecialchars($code_barre ?? ''); ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
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
                            <td><code><?php echo htmlspecialchars($p['code_barre']); ?></code></td>
                            <td><?php echo htmlspecialchars($p['nom']); ?></td>
                            <td><?php echo htmlspecialchars($p['categorie'] ?? '-'); ?></td>
                            <td><?php echo (int)$p['quantite']; ?></td>
                            <td><?php echo format_date($p['date_peremption']); ?></td>
                            <td><?php echo badge_statut($p['statut']); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
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
