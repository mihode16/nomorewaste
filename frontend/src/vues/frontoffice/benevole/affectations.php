<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $collectes
 * @var array $tournees
 * @var array $services
 */
?>
<h2 class="mb-4">Mes affectations</h2>

<h5 class="mb-3"><i class="bi bi-truck text-success"></i> Collectes</h5>
<?php if (empty($collectes)): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center text-muted py-4">Aucune collecte qui vous soit affectée.</div>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Date</th><th>Adresse</th><th>Statut</th><th>Ma confirmation</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($collectes as $c): ?>
                        <?php
                        $maLigne = null;
                        foreach (($c['benevoles'] ?? []) as $cb) {
                            if ((int)($cb['benevole_id'] ?? 0) === (int)($_SESSION['user']['id'] ?? 0)) { $maLigne = $cb; break; }
                        }
                        ?>
                        <tr>
                            <td><?php echo format_datetime($c['date_heure_collecte']); ?></td>
                            <td><?php echo htmlspecialchars($c['adresse_collecte']); ?></td>
                            <td><?php echo badge_statut($c['statut'] === '' ? 'En attente' : $c['statut']); ?></td>
                            <td>
                                <?php if (!empty($maLigne['confirme'])): ?>
                                    <span class="badge bg-success">Confirmée</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">En attente</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?php echo url('/benevole/collectes/' . (int)$c['id']); ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<h5 class="mb-3"><i class="bi bi-signpost-2 text-success"></i> Tournées</h5>
<?php if (empty($tournees)): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center text-muted py-4">Aucune tournée qui vous soit affectée.</div>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Date</th><th>Départ</th><th>Rôle</th><th>Statut</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($tournees as $t): ?>
                        <?php $estChauffeur = (int)($t['benevole_id'] ?? 0) === (int)($_SESSION['user']['id'] ?? 0); ?>
                        <tr>
                            <td><?php echo format_datetime($t['date_heure_depart']); ?></td>
                            <td><?php echo htmlspecialchars($t['adresse_depart']); ?></td>
                            <td><?php echo $estChauffeur ? 'Chauffeur' : 'Bénévole'; ?></td>
                            <td><?php echo badge_statut($t['statut']); ?></td>
                            <td class="text-end">
                                <a href="<?php echo url('/benevole/tournees/' . (int)$t['id']); ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<h5 class="mb-3"><i class="bi bi-calendar-event text-success"></i> Services</h5>
<?php if (empty($services)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center text-muted py-4">Aucun service qui vous soit affecté.</div>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Service</th><th>Date</th><th>Inscrits</th><th>Statut</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['service']['nom'] ?? '-'); ?></td>
                            <td><?php echo format_datetime($p['date_heure_debut']); ?></td>
                            <td><?php echo (int)($p['nb_inscrits'] ?? 0); ?> / <?php echo (int)($p['capacite_max'] ?? 0); ?></td>
                            <td><?php echo badge_statut($p['statut']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
