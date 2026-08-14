<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $benevole
 * @var int $nbCollectes
 * @var int $nbTournees
 * @var int $nbServices
 * @var array $prochaines
 */
$b = $benevole; ?>
<div class="mb-4">
    <h2 class="mb-1">Bonjour, <?php echo htmlspecialchars($b['prenom'] . ' ' . $b['nom']); ?> 👋</h2>
    <p class="text-muted">Bienvenue sur votre espace bénévole NO MORE WASTE.</p>
</div>

<?php if (($b['statut_candidature'] ?? '') !== 'Validé'): ?>
    <div class="alert alert-warning d-flex align-items-center gap-2">
        <i class="bi bi-hourglass-split fs-4"></i>
        <div>Votre candidature est <strong><?php echo htmlspecialchars($b['statut_candidature'] ?? 'en attente'); ?></strong>. Certaines fonctionnalités seront limitées tant qu'elle n'est pas validée.</div>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="bi bi-truck fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Collectes</div>
                    <div class="fs-3 fw-semibold"><?php echo (int)$nbCollectes; ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="bi bi-signpost-2 fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Tournées</div>
                    <div class="fs-3 fw-semibold"><?php echo (int)$nbTournees; ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-warning-subtle text-warning rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="bi bi-calendar-event fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Services</div>
                    <div class="fs-3 fw-semibold"><?php echo (int)$nbServices; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-clock-history text-success"></i> Prochaines affectations</strong>
        <a href="<?php echo url('/benevole/affectations'); ?>" class="btn btn-sm btn-outline-success">Voir tout</a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($prochaines)): ?>
            <p class="text-muted text-center py-4 mb-0">Aucune affectation à venir.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Détail</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prochaines as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['type']); ?></td>
                                <td><?php echo format_datetime($p['date']); ?></td>
                                <td><?php echo htmlspecialchars($p['detail']); ?></td>
                                <td class="text-end">
                                    <a href="<?php echo url($p['lien']); ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
