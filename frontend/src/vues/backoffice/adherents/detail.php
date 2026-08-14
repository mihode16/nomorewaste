<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $adherent
 * @var array $inscriptions
 * @var array $planningsDisponibles
 */
$a = $adherent; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/adherents'); ?>" class="btn btn-outline-secondary">
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
    <div class="col-md-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                <i class="bi bi-person-badge text-primary"></i>
                <strong>Informations</strong>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Nom</dt>
                    <dd class="col-sm-7"><?php echo htmlspecialchars($a['prenom'] . ' ' . $a['nom']); ?></dd>
                    <dt class="col-sm-5">Email</dt>
                    <dd class="col-sm-7"><?php echo htmlspecialchars($a['email']); ?></dd>
                    <dt class="col-sm-5">Téléphone</dt>
                    <dd class="col-sm-7"><?php echo htmlspecialchars($a['telephone'] ?? '') ?: '—'; ?></dd>
                    <dt class="col-sm-5">Adresse</dt>
                    <dd class="col-sm-7"><?php echo htmlspecialchars($a['adresse'] ?? '') ?: '—'; ?></dd>
                    <dt class="col-sm-5">Statut d'adhésion</dt>
                    <dd class="col-sm-7">
                        <?php if (($a['statut_adhesion'] ?? '') === 'valide'): ?>
                            <span class="badge bg-success">Validé</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">En attente</span>
                        <?php endif; ?>
                    </dd>
                    <?php if (($a['statut_adhesion'] ?? '') === 'valide'): $statutCalcule = statut_adhesion_calcule($a); ?>
                    <dt class="col-sm-5">Statut</dt>
                    <dd class="col-sm-7">
                        <span class="badge <?php echo $statutCalcule['classe']; ?>"><?php echo $statutCalcule['label']; ?></span>
                    </dd>
                    <?php endif; ?>
                    <dt class="col-sm-5">Période</dt>
                    <dd class="col-sm-7">
                        <?php echo format_date($a['date_debut_adhesion']); ?> au <?php echo format_date($a['date_fin_adhesion']); ?>
                    </dd>
                    <dt class="col-sm-5">Renouvellement auto</dt>
                    <dd class="col-sm-7"><?php echo !empty($a['est_renouvele_automatiquement']) ? 'Oui' : 'Non'; ?></dd>
                </dl>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body d-grid gap-2">
                <a href="<?php echo url('/admin/adherents/' . (int)$a['id'] . '/modifier'); ?>" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Modifier
                </a>
                <?php if (($a['statut_adhesion'] ?? '') !== 'valide'): ?>
                    <form method="POST" action="<?php echo url('/admin/adherents/' . (int)$a['id'] . '/valider'); ?>">
                        <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-circle"></i> Valider l'adhésion</button>
                    </form>
                <?php endif; ?>
                <form method="POST" action="<?php echo url('/admin/adherents/' . (int)$a['id'] . '/supprimer'); ?>" onsubmit="return confirm('Supprimer ce compte adhérent ?');">
                    <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-trash"></i> Supprimer le compte</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bookmark-check text-primary"></i>
                    <strong>Services inscrits</strong>
                </div>
                <?php if (!empty($planningsDisponibles)): ?>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#ajouterAbonnementModal">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($inscriptions)): ?>
                    <p class="text-muted mb-0">Aucun service souscrit pour le moment.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Créneau</th>
                                    <th>Places</th>
                                    <th>Statut</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inscriptions as $p): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($p['service']['nom'] ?? 'Service #' . $p['service_id']); ?></td>
                                        <td><?php echo format_datetime($p['date_heure_debut']); ?></td>
                                        <td><?php echo (int)($p['nb_inscrits'] ?? 0); ?> / <?php echo (int)$p['capacite_max']; ?></td>
                                        <td><?php echo badge_statut($p['statut']); ?></td>
                                        <td>
                                            <form method="POST" action="<?php echo url('/admin/adherents/' . (int)$a['id'] . '/abonnements/' . (int)$p['id'] . '/supprimer'); ?>" onsubmit="return confirm('Retirer cet abonnement ?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Retirer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
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

<!-- Modal ajout d'abonnement -->
<?php if (!empty($planningsDisponibles)): ?>
<div class="modal fade" id="ajouterAbonnementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo url('/admin/adherents/' . (int)$a['id'] . '/abonnements'); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un abonnement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Service / créneau</label>
                    <select name="planning_id" class="form-select" required>
                        <option value="">— Choisir —</option>
                        <?php foreach ($planningsDisponibles as $p): ?>
                            <option value="<?php echo (int)$p['id']; ?>">
                                <?php echo htmlspecialchars($p['service']['nom'] ?? 'Service #' . $p['service_id']); ?>
                                — <?php echo format_datetime($p['date_heure_debut']); ?>
                                (<?php echo (int)($p['nb_inscrits'] ?? 0); ?>/<?php echo (int)$p['capacite_max']; ?> places)
                            </option>
                        <?php endforeach; ?>
                    </select>
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
