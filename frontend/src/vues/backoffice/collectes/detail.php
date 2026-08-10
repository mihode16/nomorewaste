<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/collectes'); ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?> alert-dismissible fade show">
        <?php echo $_SESSION['flash']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="row">
    <!-- Colonne gauche : Informations collecte -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Informations de la collecte</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">ID</dt>
                    <dd class="col-sm-8"><?php echo (int)$collecte['id']; ?></dd>
                    <dt class="col-sm-4">Date / Heure</dt>
                    <dd class="col-sm-8"><?php echo format_datetime($collecte['date_heure_collecte']); ?></dd>
                    <dt class="col-sm-4">Adresse</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($collecte['adresse_collecte']); ?></dd>
                    <dt class="col-sm-4">Commerçant</dt>
                    <dd class="col-sm-8">
                        <?php
                        if (!empty($collecte['commercant'])) {
                            echo htmlspecialchars($collecte['commercant']['raison_sociale'] ?? $collecte['commercant']['nom'] ?? '');
                        } else {
                            echo '#'.(int)$collecte['commercant_id'];
                        }
                        ?>
                    </dd>
                    <dt class="col-sm-4">Statut</dt>
                    <dd class="col-sm-8">
                        <?php
                        $statut = $collecte['statut'] ?? '';
                        if ($statut === ''): ?>
                            <span class="badge bg-secondary">En attente</span>
                        <?php elseif ($statut === 'Planifiée'): ?>
                            <span class="badge bg-primary">Planifiée</span>
                        <?php elseif ($statut === 'Terminée'): ?>
                            <span class="badge bg-success">Terminée</span>
                        <?php else: ?>
                            <span class="badge bg-light text-dark"><?php echo htmlspecialchars($statut); ?></span>
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-4">Validation</dt>
                    <dd class="col-sm-8">
                        <?php if (!empty($collecte['validee'])): ?>
                            <span class="badge bg-success">Validée</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">En attente</span>
                        <?php endif; ?>
                    </dd>
                    <?php if (!empty($collecte['commentaire'])): ?>
                        <dt class="col-sm-4">Commentaire</dt>
                        <dd class="col-sm-8"><?php echo htmlspecialchars($collecte['commentaire']); ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>

    <!-- Colonne droite : Produits et bénévoles -->
    <div class="col-md-6">
        <!-- Bloc Produits -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Produits de la collecte</span>
                <?php
                $peutAjouter = (!empty($collecte['validee']) && ($collecte['statut'] ?? '') === 'Terminée');
                ?>
                <?php if ($peutAjouter): ?>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#ajouterProduitModal">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($produits)): ?>
                    <p class="text-muted">Aucun produit associé.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Code barre</th>
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Qté</th>
                                    <th>Péremption</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produits as $p): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($p['code_barre'])): ?>
<img src="<?php echo url('/barcode/' . $p['code_barre'] . '.svg'); ?>" alt="Code-barres" style="height:30px; vertical-align:middle;">                                                <br><small><?php echo htmlspecialchars($p['code_barre']); ?></small>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($p['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($p['categorie'] ?? '-'); ?></td>
                                        <td><?php echo (int)$p['quantite']; ?></td>
                                        <td><?php echo format_date($p['date_peremption']); ?></td>
                                        <td>
                                            <?php
                                            $pStatut = $p['statut'] ?? 'À venir';
                                            if ($pStatut === 'À venir') {
                                                echo '<span class="badge bg-warning text-dark">À venir</span>';
                                            } elseif ($pStatut === 'Stocké') {
                                                echo '<span class="badge bg-success">Stocké</span>';
                                            } else {
                                                echo badge_statut($pStatut);
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Bloc Bénévoles -->
        <div class="card">
            <div class="card-header">Bénévoles associés</div>
            <div class="card-body">
                <?php if (empty($collecte['benevoles'])): ?>
                    <p class="text-muted">Aucun bénévole associé.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
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
                                <?php foreach ($collecte['benevoles'] as $cb): ?>
                                    <tr>
                                        <td><?php echo (int)$cb['benevole_id']; ?></td>
                                        <td>
                                            <?php
                                            $benevoleId = (int)$cb['benevole_id'];
                                            if (isset($benevolesMap[$benevoleId])) {
                                                echo htmlspecialchars($benevolesMap[$benevoleId]['nom']);
                                            } else {
                                                echo '#'.$benevoleId;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (isset($benevolesMap[$benevoleId])) {
                                                $competences = $benevolesMap[$benevoleId]['competences'];
                                                if (!empty($competences)) {
                                                    foreach ($competences as $c) {
                                                        echo '<span class="badge bg-secondary me-1">' . htmlspecialchars($c) . '</span>';
                                                    }
                                                } else {
                                                    echo '<span class="text-muted">Aucune</span>';
                                                }
                                            } else {
                                                echo '<span class="text-muted">—</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($cb['confirme']): ?>
                                                <span class="badge bg-success">Confirmé</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">En attente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($cb['date_confirmation'])): ?>
                                                <?php echo format_datetime($cb['date_confirmation']); ?>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

<!-- Bouton Terminer la collecte (admin) -->
<?php if (($collecte['statut'] ?? '') === 'Planifiée'): ?>
<div class="card mt-3">
    <div class="card-header">Actions</div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('/admin/collectes/' . (int)$collecte['id'] . '/terminer'); ?>">
            <p class="text-muted small">Terminer la collecte manuellement (les produits passeront en stock).</p>
            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Terminer cette collecte ?');">
                <i class="bi bi-check-circle"></i> Terminer la collecte
            </button>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Modal d'ajout de produit (si autorisé) -->
<?php if ($peutAjouter): ?>
<div class="modal fade" id="ajouterProduitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo url('/admin/produits'); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un produit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="collecte_id" value="<?php echo (int)$collecte['id']; ?>">
                    <input type="hidden" name="code_barre" value="">
                    <div class="mb-3">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catégorie</label>
                        <input type="text" name="categorie" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantité</label>
                        <input type="number" name="quantite" class="form-control" value="1" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date de péremption *</label>
                        <input type="date" name="date_peremption" class="form-control" required>
                    </div>
                    <p class="text-muted small">Le code-barres sera généré automatiquement.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>