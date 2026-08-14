<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $jours
 * @var array $parJour
 * @var array $datesJours
 * @var array $semaine
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/benevoles'); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour aux bénévoles
    </a>
</div>

<div class="btn-group btn-group-sm mb-3">
    <a href="<?php echo url('/admin/benevoles/calendrier?semaine=courante'); ?>" class="btn btn-outline-success <?php echo $semaine === 'courante' ? 'active' : ''; ?>">
        Cette semaine
    </a>
    <a href="<?php echo url('/admin/benevoles/calendrier?semaine=prochaine'); ?>" class="btn btn-outline-success <?php echo $semaine === 'prochaine' ? 'active' : ''; ?>">
        Semaine prochaine
    </a>
</div>

<p class="text-muted">Disponibilités renseignées par chaque bénévole depuis son espace pour cette semaine.</p>

<div class="calendrier-semaine">
    <div class="row g-2">
        <?php foreach ($jours as $jour): ?>
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-success text-white text-center">
                        <div class="fw-semibold"><?php echo $jour; ?></div>
                        <div class="small text-white-50"><?php echo $datesJours[$jour]; ?></div>
                    </div>
                    <div class="card-body p-2" style="min-height: 420px;">
                        <?php if (empty($parJour[$jour])): ?>
                            <p class="text-muted small text-center mt-3 mb-0">—</p>
                        <?php else: ?>
                            <?php foreach ($parJour[$jour] as $d): ?>
                                <div class="creneau-benevole mb-2 p-2 rounded">
                                    <div class="fw-semibold small"><?php echo htmlspecialchars($d['benevole_prenom'] . ' ' . $d['benevole_nom']); ?></div>
                                    <div class="text-muted small">
                                        <i class="bi bi-clock"></i>
                                        <?php echo substr($d['heure_debut'], 0, 5); ?> – <?php echo substr($d['heure_fin'], 0, 5); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .creneau-benevole {
        background: #eaf6ec;
        border-left: 3px solid #198754;
    }
</style>
