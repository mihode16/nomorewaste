<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $tournee
 * @var array $benevolesMap
 */
$t = $tournee; ?>
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
                    <dt class="col-sm-4">Bénévole chauffeur</dt>
                    <dd class="col-sm-8">
                        <?php if (!empty($t['benevole']) && !empty($t['benevole']['nom'])): ?>
                            <?php echo htmlspecialchars($t['benevole']['prenom'] . ' ' . $t['benevole']['nom']); ?>
                            (ID #<?php echo (int)$t['benevole_id']; ?>)
                        <?php else: ?>
                            #<?php echo (int)($t['benevole_id'] ?? 0); ?>
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-4">Lieu de distribution</dt>
                    <dd class="col-sm-8">
                        <?php if (!empty($t['lieu_distribution'])): ?>
                            <?php echo htmlspecialchars($t['lieu_distribution']['nom'] . ' — ' . $t['lieu_distribution']['adresse']); ?>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Produits livrés</div>
            <div class="card-body table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Code-barres</th>
                            <th>Nom</th>
                            <th>Qté</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($t['produits'])): ?>
                            <tr><td colspan="4" class="text-center">Aucun produit</td></tr>
                        <?php else: ?>
                            <?php foreach ($t['produits'] as $p): ?>
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
                                    <td><?php echo (int)$p['quantite']; ?></td>
                                    <td><?php echo badge_statut($p['statut']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header">Bénévoles associés</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Compétences</th>
                                <th>Confirmation</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo (int)($t['benevole_id'] ?? 0); ?></td>
                                <td>
                                    <?php echo !empty($t['benevole']['nom']) ? htmlspecialchars($t['benevole']['prenom'] . ' ' . $t['benevole']['nom']) : '#' . (int)($t['benevole_id'] ?? 0); ?>
                                    <span class="badge bg-info text-dark">Chauffeur</span>
                                </td>
                                <td>
                                    <?php
                                    $benevoleId = (int)($t['benevole_id'] ?? 0);
                                    $competences = $benevolesMap[$benevoleId]['competences'] ?? [];
                                    if (!empty($competences)):
                                        foreach ($competences as $c): ?>
                                            <span class="badge bg-secondary me-1"><?php echo htmlspecialchars($c); ?></span>
                                        <?php endforeach;
                                    else: ?>
                                        <span class="text-muted">Aucune</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($t['chauffeur_confirme'])): ?>
                                        <span class="badge bg-success">Confirmé</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">En attente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo !empty($t['date_confirmation_chauffeur']) ? format_datetime($t['date_confirmation_chauffeur']) : '—'; ?>
                                </td>
                            </tr>
                            <?php if (empty($t['benevoles_confirmation'])): ?>
                                <tr><td colspan="5" class="text-center text-muted">Aucun bénévole supplémentaire</td></tr>
                            <?php else: ?>
                                <?php foreach ($t['benevoles_confirmation'] as $tb): ?>
                                    <?php $benevoleId = (int)$tb['benevole_id']; ?>
                                    <tr>
                                        <td><?php echo $benevoleId; ?></td>
                                        <td>
                                            <?php
                                            echo isset($benevolesMap[$benevoleId])
                                                ? htmlspecialchars($benevolesMap[$benevoleId]['nom'])
                                                : htmlspecialchars(($tb['benevole_prenom'] ?? '') . ' ' . ($tb['benevole_nom'] ?? ''));
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $competences = $benevolesMap[$benevoleId]['competences'] ?? [];
                                            if (!empty($competences)):
                                                foreach ($competences as $c): ?>
                                                    <span class="badge bg-secondary me-1"><?php echo htmlspecialchars($c); ?></span>
                                                <?php endforeach;
                                            else: ?>
                                                <span class="text-muted">Aucune</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($tb['confirme'])): ?>
                                                <span class="badge bg-success">Confirmé</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">En attente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo !empty($tb['date_confirmation']) ? format_datetime($tb['date_confirmation']) : '—'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <p class="text-muted small mt-2 mb-0">La confirmation sera faite par chaque bénévole depuis son espace personnel (à venir). Une fois tous confirmés, la tournée passe automatiquement en "Terminée".</p>
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