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
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Informations de la collecte</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">ID</dt>
                    <dd class="col-sm-8"><?php echo (int)$collecte['id']; ?></dd>
                    <dt class="col-sm-4">Date / Heure</dt>
                    <dd class="col-sm-8"><?php echo format_datetime($collecte['date_heure_collecte']); ?></dd>
                    <dt class="col-sm-4">Adresse</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($collecte['adresse_collecte']); ?></dd>
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
                    <dt class="col-sm-4">Bénévoles requis</dt>
                    <dd class="col-sm-8"><?php echo (int)($collecte['nb_benevoles'] ?? 0); ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Bénévoles associés</span>
                <?php if (($collecte['statut'] ?? '') === 'Planifiée'): ?>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#ajouterBenevoleModal">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </button>
                <?php endif; ?>
            </div>
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
                                    <?php
                                    $benevoleId = (int)$cb['benevole_id'];
                                    $nom = isset($benevolesMap[$benevoleId]) ? $benevolesMap[$benevoleId]['nom'] : '#'.$benevoleId;
                                    $competences = isset($benevolesMap[$benevoleId]) ? $benevolesMap[$benevoleId]['competences'] : '';
                                    ?>
                                    <tr>
                                        <td><?php echo $benevoleId; ?></td>
                                        <td><?php echo htmlspecialchars($nom); ?></td>
                                        <td><small><?php echo htmlspecialchars($competences); ?></small></td>
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
    </div>
</div>

<!-- Modal d'ajout de bénévole -->
<div class="modal fade" id="ajouterBenevoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo url('/admin/collectes/' . (int)$collecte['id'] . '/benevoles'); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un bénévole</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Bénévole</label>
                        <select name="benevole_id" class="form-select" required>
                            <option value="">Choisir un bénévole</option>
                            <?php foreach ($benevoles as $b): ?>
                                <?php
                                $competences = [];
                                if (!empty($b['competences'])) {
                                    foreach ($b['competences'] as $comp) {
                                        $competences[] = $comp['nom'];
                                    }
                                }
                                $compTexte = implode(', ', $competences);
                                ?>
                                <option value="<?php echo (int)$b['id']; ?>">
                                    <?php echo htmlspecialchars($b['prenom'] . ' ' . $b['nom']); ?>
                                    <?php if ($compTexte): ?>
                                        (<?php echo htmlspecialchars($compTexte); ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>