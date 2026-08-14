<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $collecte
 * @var array $produits
 */
$c = $collecte; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Collecte #<?php echo (int)$c['id']; ?></h2>
    <a href="<?php echo url('/commercant/collectes'); ?>" class="btn btn-outline-secondary">
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
                    <dt class="col-sm-5">Statut</dt>
                    <dd class="col-sm-7"><?php echo badge_statut($c['statut'] === '' ? 'En attente' : $c['statut']); ?></dd>
                    <dt class="col-sm-5">Validation</dt>
                    <dd class="col-sm-7">
                        <?php if (!empty($c['validee'])): ?>
                            <span class="badge bg-success">Validée par l'association</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">En attente de validation</span>
                        <?php endif; ?>
                    </dd>
                    <?php if (!empty($c['commentaire'])): ?>
                        <dt class="col-sm-5">Commentaire</dt>
                        <dd class="col-sm-7"><?php echo htmlspecialchars($c['commentaire']); ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <div class="d-grid gap-2">
            <?php if (empty($c['validee'])): ?>
                <a href="<?php echo url('/commercant/collectes/' . $c['id'] . '/modifier'); ?>" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Modifier ma demande
                </a>
            <?php else: ?>
                <div class="alert alert-light border small mb-0">
                    <i class="bi bi-info-circle"></i> Cette demande a été validée par l'association et ne peut plus être modifiée directement.
                    <a href="<?php echo url('/commercant/messages/creer?collecte_id=' . (int)$c['id']); ?>" class="d-block mt-1 fw-semibold">
                        <i class="bi bi-envelope"></i> Contacter un admin à ce sujet
                    </a>
                </div>
            <?php endif; ?>
            <?php if ($c['statut'] === 'Terminée'): ?>
                <a href="<?php echo url('/commercant/collectes/' . $c['id'] . '/pdf'); ?>" class="btn btn-success" target="_blank">
                    <i class="bi bi-file-earmark-pdf"></i> Télécharger le PDF récapitulatif
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Produits collectés (<?php echo count($produits); ?>)</strong></div>
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
