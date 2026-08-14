<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $services
 * @var array $plannings
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <div class="d-flex gap-2">
        <a href="<?php echo url('/admin/services/creer'); ?>" class="btn btn-outline-primary"><i class="bi bi-plus-circle"></i> Nouveau service</a>
        <a href="<?php echo url('/admin/services/plannings/creer'); ?>" class="btn btn-success"><i class="bi bi-calendar-plus"></i> Nouveau planning</a>
    </div>
</div>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?> alert-dismissible fade show">
        <?php echo $_SESSION['flash']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<h4 class="mb-3">Catalogue des services</h4>
<div class="row mb-5">
    <?php if (empty($services)): ?>
        <p class="text-muted">Aucun service dans le catalogue.</p>
    <?php else: ?>
        <?php foreach ($services as $s): ?>
            <div class="col-md-4 mb-3">
                <a href="<?php echo url('/admin/services/' . (int)$s['id'] . '/modifier'); ?>" class="text-decoration-none text-reset">
                    <div class="card h-100 shadow-sm border-0" style="transition: box-shadow .15s;" onmouseover="this.classList.add('shadow')" onmouseout="this.classList.remove('shadow')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="badge bg-info"><?php echo htmlspecialchars($s['type'] ?? ''); ?></span>
                                <i class="bi bi-pencil text-muted"></i>
                            </div>
                            <h5 class="mt-2"><?php echo htmlspecialchars($s['nom']); ?></h5>
                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($s['description'] ?? ''); ?></p>
                            <?php if (!empty($s['competences'])): ?>
                                <p class="mb-0">
                                    <?php foreach ($s['competences'] as $c): ?>
                                        <span class="badge bg-secondary"><i class="bi bi-mortarboard"></i> <?php echo htmlspecialchars($c['nom']); ?></span>
                                    <?php endforeach; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<h4 class="mb-3">Plannings</h4>
<div class="card border-0 shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Inscrits / Capacité</th>
                    <th>Bénévole en charge</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($plannings)): ?>
                    <tr><td colspan="6" class="text-center">Aucun planning</td></tr>
                <?php else: ?>
                    <?php foreach ($plannings as $p): ?>
                        <tr class="cursor-pointer" style="cursor:pointer;" onclick="window.location='<?php echo url('/admin/services/plannings/' . (int)$p['id']); ?>'">
                            <td><?php echo htmlspecialchars($p['service']['nom'] ?? 'Service #' . $p['service_id']); ?></td>
                            <td><?php echo format_datetime($p['date_heure_debut']); ?></td>
                            <td><?php echo format_datetime($p['date_heure_fin']); ?></td>
                            <td>
                                <?php
                                $nbInscrits = (int)($p['nb_inscrits'] ?? 0);
                                $capacite = (int)$p['capacite_max'];
                                $plein = $capacite > 0 && $nbInscrits >= $capacite;
                                ?>
                                <span class="badge <?php echo $plein ? 'bg-danger' : 'bg-secondary'; ?>">
                                    <?php echo $nbInscrits; ?> / <?php echo $capacite; ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($p['benevole']) && !empty($p['benevole']['nom'])): ?>
                                    <?php echo htmlspecialchars($p['benevole']['prenom'] . ' ' . $p['benevole']['nom']); ?>
                                <?php else: ?>
                                    <span class="text-muted">Non assigné</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo badge_statut($p['statut']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
