<?php $t = $tournee; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/tournees'); ?>" class="btn btn-secondary">Retour</a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">Informations tournée</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Départ</dt>
                    <dd class="col-sm-8"><?php echo format_datetime($t['date_heure_depart']); ?></dd>
                    <dt class="col-sm-4">Fin</dt>
                    <dd class="col-sm-8"><?php echo !empty($t['date_heure_fin']) ? format_datetime($t['date_heure_fin']) : '—'; ?></dd>
                    <dt class="col-sm-4">Adresse départ</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($t['adresse_depart']); ?></dd>
                    <dt class="col-sm-4">Statut</dt>
                    <dd class="col-sm-8"><?php echo badge_statut($t['statut']); ?></dd>
                    <dt class="col-sm-4">Bénévole</dt>
                    <dd class="col-sm-8">#<?php echo (int)($t['benevole_id'] ?? 0); ?></dd>
                </dl>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Produits livrés</div>
            <div class="card-body table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Code-barres</th><th>Nom</th><th>Qté</th><th>Statut</th></tr></thead>
                    <tbody>
                        <?php if (empty($t['produits'])): ?>
                            <tr><td colspan="4" class="text-center">Aucun produit</td></tr>
                        <?php else: ?>
                            <?php foreach ($t['produits'] as $p): ?>
                                <tr>
                                    <td><code><?php echo htmlspecialchars($p['code_barre']); ?></code></td>
                                    <td><?php echo htmlspecialchars($p['nom']); ?></td>
                                    <td><?php echo (int)$p['quantite']; ?></td>
                                    <td><?php echo badge_statut($p['statut']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <?php if ($t['statut'] !== 'Terminée'): ?>
        <div class="card">
            <div class="card-header">Actions</div>
            <div class="card-body">
                <form method="POST" action="<?php echo url('/admin/tournees/' . (int)$t['id'] . '/terminer'); ?>">
                    <p class="text-muted small">Marquer la tournée comme terminée et les produits comme distribués.</p>
                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Terminer cette tournée ?');">
                        <i class="bi bi-check-circle"></i> Terminer la tournée
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
