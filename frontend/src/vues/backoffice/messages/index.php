<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $conversations
 * @var array $filtre
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><?php echo htmlspecialchars($titre); ?></h2>
    <div class="btn-group btn-group-sm">
        <a href="<?php echo url('/admin/messages'); ?>" class="btn btn-outline-secondary <?php echo $filtre === '' ? 'active' : ''; ?>">Toutes</a>
        <a href="<?php echo url('/admin/messages?filtre=ouvertes'); ?>" class="btn btn-outline-secondary <?php echo $filtre === 'ouvertes' ? 'active' : ''; ?>">Ouvertes</a>
        <a href="<?php echo url('/admin/messages?filtre=cloturees'); ?>" class="btn btn-outline-secondary <?php echo $filtre === 'cloturees' ? 'active' : ''; ?>">Clôturées</a>
    </div>
</div>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?> alert-dismissible fade show">
        <?php echo $_SESSION['flash']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if (empty($conversations)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-inbox fs-1"></i>
            <p class="mb-0 mt-2">Aucune conversation.</p>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($conversations as $c): ?>
        <a href="<?php echo url('/admin/messages/' . (int)$c['id']); ?>" class="text-decoration-none text-reset">
            <div class="card border-0 shadow-sm mb-3 <?php echo ($c['nb_non_lus'] ?? 0) > 0 ? 'border-start border-4 border-warning' : ''; ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">
                                <?php echo htmlspecialchars($c['sujet']); ?>
                                <?php if (($c['nb_non_lus'] ?? 0) > 0): ?>
                                    <span class="badge bg-warning text-dark"><?php echo (int)$c['nb_non_lus']; ?> non lu(s)</span>
                                <?php endif; ?>
                                <?php echo !empty($c['cloturee']) ? '<span class="badge bg-secondary">Clôturée</span>' : '<span class="badge bg-success">Ouverte</span>'; ?>
                            </h6>
                            <?php $estCommercant = ($c['initiateur']['type_utilisateur'] ?? '') === 'commercant'; ?>
                            <p class="text-muted small mb-0">
                                <i class="bi <?php echo icone_type_participant($c['initiateur'] ?? null); ?>"></i>
                                <?php echo htmlspecialchars(nom_affichage_participant($c['initiateur'] ?? null)); ?>
                                <?php if ($estCommercant): ?>
                                    (<?php echo htmlspecialchars(($c['initiateur']['prenom'] ?? '') . ' ' . ($c['initiateur']['nom'] ?? '')); ?>)
                                <?php endif; ?>
                                — <?php echo format_datetime($c['date_creation']); ?>
                                <?php if (!empty($c['collecte_id'])): ?>
                                    — Collecte #<?php echo (int)$c['collecte_id']; ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </div>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>
