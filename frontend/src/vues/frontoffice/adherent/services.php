<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $services
 * @var array $plannings
 * @var array $inscritIds
 * @var bool $peutInscrire
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Services & ateliers</h2>
</div>

<?php if (!($peutInscrire ?? false)): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i> Votre adhésion doit être validée et à jour pour vous inscrire à un service.
        <a href="<?php echo url('/adherent/adhesion'); ?>" class="alert-link">Voir mon adhésion</a>
    </div>
<?php endif; ?>

<?php if (empty($plannings)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-calendar-x fs-1"></i>
            <p class="mb-0 mt-2">Aucune séance à venir pour le moment.</p>
        </div>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($plannings as $p): ?>
            <?php
            $inscrit = in_array($p['id'], $inscritIds, true);
            $complet = (int)($p['nb_inscrits'] ?? 0) >= (int)($p['capacite_max'] ?? 0);
            $ouvert = ($p['statut'] ?? '') === 'Ouvert';
            ?>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="mb-0"><?php echo htmlspecialchars($p['service']['nom'] ?? '-'); ?></h5>
                            <?php echo badge_statut($p['statut']); ?>
                        </div>
                        <p class="text-muted small mb-2"><?php echo htmlspecialchars($p['service']['description'] ?? ''); ?></p>
                        <p class="mb-1"><i class="bi bi-clock"></i> <?php echo format_datetime($p['date_heure_debut']); ?> — <?php echo format_datetime($p['date_heure_fin'], 'H:i'); ?></p>
                        <p class="mb-3">
                            <i class="bi bi-people"></i> <?php echo (int)($p['nb_inscrits'] ?? 0); ?> / <?php echo (int)($p['capacite_max'] ?? 0); ?> inscrits
                            <?php if ($complet): ?><span class="badge bg-danger ms-1">Complet</span><?php endif; ?>
                        </p>
                        <?php if ($inscrit): ?>
                            <form method="POST" action="<?php echo url('/adherent/services/' . (int)$p['id'] . '/desinscrire'); ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                    <i class="bi bi-x-circle"></i> Se désinscrire
                                </button>
                            </form>
                        <?php elseif ($ouvert && !$complet && ($peutInscrire ?? false)): ?>
                            <form method="POST" action="<?php echo url('/adherent/services/' . (int)$p['id'] . '/inscrire'); ?>">
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="bi bi-plus-circle"></i> S'inscrire
                                </button>
                            </form>
                        <?php else: ?>
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100" disabled>Indisponible</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
