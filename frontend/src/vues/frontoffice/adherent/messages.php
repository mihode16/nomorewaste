<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $avecAssociation
 * @var array $entreAdherents
 * @var int $monId
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Mes messages</h2>
    <a href="<?php echo url('/adherent/messages/creer'); ?>" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Nouveau message
    </a>
</div>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?> alert-dismissible fade show">
        <?php echo $_SESSION['flash']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<h5 class="mb-3"><i class="bi bi-building text-success"></i> Avec l'association</h5>
<?php if (empty($avecAssociation)): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center text-muted py-4">Aucun message avec l'association.</div>
    </div>
<?php else: ?>
    <div class="mb-4">
        <?php foreach ($avecAssociation as $c): ?>
            <?php $nonLus = (int)($c['nb_non_lus'] ?? 0); ?>
            <a href="<?php echo url('/adherent/messages/' . (int)$c['id']); ?>" class="text-decoration-none text-reset">
                <div class="card border-0 shadow-sm mb-3 <?php echo $nonLus > 0 ? 'border-start border-4 border-success' : ''; ?>">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">
                                <?php echo htmlspecialchars($c['sujet']); ?>
                                <?php if ($nonLus > 0): ?>
                                    <span class="badge bg-success"><?php echo $nonLus; ?> nouveau(x)</span>
                                <?php endif; ?>
                                <?php echo !empty($c['cloturee']) ? '<span class="badge bg-secondary">Clôturée</span>' : ''; ?>
                            </h6>
                            <p class="text-muted small mb-0">Discussion avec l'association — <?php echo format_datetime($c['date_creation']); ?></p>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h5 class="mb-3"><i class="bi bi-people text-success"></i> Entre adhérents</h5>
<?php if (empty($entreAdherents)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center text-muted py-4">Aucun message avec d'autres adhérents.</div>
    </div>
<?php else: ?>
    <?php foreach ($entreAdherents as $c): ?>
        <?php
        $autre = ((int)($c['initiateur_id'] ?? 0) === $monId) ? ($c['destinataire'] ?? null) : ($c['initiateur'] ?? null);
        $nonLus = (int)($c['nb_non_lus'] ?? 0);
        ?>
        <a href="<?php echo url('/adherent/messages/' . (int)$c['id']); ?>" class="text-decoration-none text-reset">
            <div class="card border-0 shadow-sm mb-3 <?php echo $nonLus > 0 ? 'border-start border-4 border-success' : ''; ?>">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">
                            <?php echo htmlspecialchars($c['sujet']); ?>
                            <?php if ($nonLus > 0): ?>
                                <span class="badge bg-success"><?php echo $nonLus; ?> nouveau(x)</span>
                            <?php endif; ?>
                        </h6>
                        <p class="text-muted small mb-0">Avec <?php echo htmlspecialchars(nom_affichage_participant($autre)); ?> — <?php echo format_datetime($c['date_creation']); ?></p>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>
