<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $collecte
 * @var array $produits
 * @var array $maLigne
 * @var bool $peutTerminer
 * @var bool $heurePasse
 */
$c = $collecte; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Collecte #<?php echo (int)$c['id']; ?></h2>
    <a href="<?php echo url('/benevole/affectations'); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white"><strong>Informations</strong></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Date / heure</dt>
                    <dd class="col-sm-7"><?php echo format_datetime($c['date_heure_collecte']); ?></dd>
                    <dt class="col-sm-5">Adresse</dt>
                    <dd class="col-sm-7"><?php echo htmlspecialchars($c['adresse_collecte']); ?></dd>
                    <dt class="col-sm-5">Commerçant</dt>
                    <dd class="col-sm-7"><?php echo htmlspecialchars($c['commercant']['raison_sociale'] ?? ''); ?></dd>
                    <dt class="col-sm-5">Statut</dt>
                    <dd class="col-sm-7"><?php echo badge_statut($c['statut'] === '' ? 'En attente' : $c['statut']); ?></dd>
                    <?php if (!empty($c['commentaire'])): ?>
                        <dt class="col-sm-5">Commentaire</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($c['commentaire']); ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Ma participation</strong></div>
            <div class="card-body">
                <?php if (!empty($maLigne['confirme'])): ?>
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle"></i> Vous avez marqué cette collecte comme terminée le <?php echo format_datetime($maLigne['date_confirmation']); ?>.
                    </div>
                <?php elseif ($peutTerminer): ?>
                    <form method="POST" action="<?php echo url('/benevole/collectes/' . (int)$c['id'] . '/terminer'); ?>" onsubmit="return confirm('Confirmer que la collecte est terminée ?');">
                        <p class="text-muted small">L'heure de la collecte est passée : vous pouvez la marquer comme terminée.</p>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check2-circle"></i> Marquer comme terminée
                        </button>
                    </form>
                <?php elseif (!$heurePasse): ?>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-clock"></i> Vous pourrez marquer cette collecte comme terminée une fois l'heure prévue (<?php echo format_datetime($c['date_heure_collecte']); ?>) passée.
                    </p>
                <?php else: ?>
                    <p class="text-muted small mb-0">Cette collecte n'est plus modifiable.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Produits (<?php echo count($produits); ?>)</strong></div>
            <div class="card-body">
                <?php if (empty($produits)): ?>
                    <p class="text-muted mb-0">Aucun produit enregistré pour cette collecte.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Qté</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produits as $p): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($p['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($p['categorie'] ?? '-'); ?></td>
                                        <td><?php echo (int)$p['quantite']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
