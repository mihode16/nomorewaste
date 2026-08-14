<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $conversations
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Mes messages</h2>
    <a href="<?php echo url('/benevole/messages/creer'); ?>" class="btn btn-success">
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

<?php if (empty($conversations)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-chat-dots fs-1"></i>
            <p class="mb-0 mt-2">Aucun message pour le moment.</p>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($conversations as $c): ?>
        <a href="<?php echo url('/benevole/messages/' . (int)$c['id']); ?>" class="text-decoration-none text-reset">
            <div class="card border-0 shadow-sm mb-3 <?php echo ($c['nb_non_lus'] ?? 0) > 0 ? 'border-start border-4 border-success' : ''; ?>">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">
                            <?php echo htmlspecialchars($c['sujet']); ?>
                            <?php if (($c['nb_non_lus'] ?? 0) > 0): ?>
                                <span class="badge bg-success"><?php echo (int)$c['nb_non_lus']; ?> nouvelle(s) réponse(s)</span>
                            <?php endif; ?>
                            <?php echo !empty($c['cloturee']) ? '<span class="badge bg-secondary">Clôturée</span>' : '<span class="badge bg-info">Ouverte</span>'; ?>
                        </h6>
                        <p class="text-muted small mb-0">
                            <?php echo format_datetime($c['date_creation']); ?>
                        </p>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>
