<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $tournee
 * @var bool $estChauffeur
 * @var bool $confirme
 * @var bool $peutTerminer
 * @var bool $heurePasse
 */
$t = $tournee; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Tournée #<?php echo (int)$t['id']; ?></h2>
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
                    <dt class="col-sm-5">Départ</dt>
                    <dd class="col-sm-7"><?php echo format_datetime($t['date_heure_depart']); ?></dd>
                    <?php if (!empty($t['date_heure_fin'])): ?>
                        <dt class="col-sm-5">Fin prévue</dt>
                        <dd class="col-sm-7"><?php echo format_datetime($t['date_heure_fin']); ?></dd>
                    <?php endif; ?>
                    <dt class="col-sm-5">Adresse de départ</dt>
                    <dd class="col-sm-7"><?php echo htmlspecialchars($t['adresse_depart']); ?></dd>
                    <?php if (!empty($t['lieu_distribution'])): ?>
                        <dt class="col-sm-5">Lieu de distribution</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($t['lieu_distribution']['nom'] . ' — ' . $t['lieu_distribution']['adresse']); ?></dd>
                    <?php endif; ?>
                    <dt class="col-sm-5">Mon rôle</dt>
                    <dd class="col-sm-7"><?php echo $estChauffeur ? 'Chauffeur' : 'Bénévole'; ?></dd>
                    <dt class="col-sm-5">Statut</dt>
                    <dd class="col-sm-7"><?php echo badge_statut($t['statut']); ?></dd>
                </dl>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Ma participation</strong></div>
            <div class="card-body">
                <?php if ($confirme): ?>
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle"></i> Vous avez marqué cette tournée comme terminée.
                    </div>
                <?php elseif ($peutTerminer): ?>
                    <form method="POST" action="<?php echo url('/benevole/tournees/' . (int)$t['id'] . '/terminer'); ?>" onsubmit="return confirm('Confirmer que la tournée est terminée ?');">
                        <p class="text-muted small">L'heure de la tournée est passée : vous pouvez la marquer comme terminée.</p>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check2-circle"></i> Marquer comme terminée
                        </button>
                    </form>
                <?php elseif (!$heurePasse): ?>
                    <p class="text-muted small mb-0"><i class="bi bi-clock"></i> Vous pourrez marquer cette tournée comme terminée une fois l'heure prévue passée.</p>
                <?php else: ?>
                    <p class="text-muted small mb-0">Cette tournée n'est plus modifiable.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Produits (<?php echo count($t['produits'] ?? []); ?>)</strong></div>
            <div class="card-body">
                <?php if (empty($t['produits'])): ?>
                    <p class="text-muted mb-0">Aucun produit associé à cette tournée.</p>
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
                                <?php foreach ($t['produits'] as $p): ?>
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
