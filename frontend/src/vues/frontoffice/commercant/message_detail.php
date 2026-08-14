<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $conversation
 */
$c = $conversation; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?php echo url('/commercant/messages'); ?>" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Retour à mes messages</a>
        <h2 class="mb-0"><?php echo htmlspecialchars($c['sujet']); ?></h2>
    </div>
    <div>
        <?php if (empty($c['cloturee'])): ?>
            <span class="badge bg-info">Ouverte</span>
        <?php else: ?>
            <span class="badge bg-secondary">Clôturée le <?php echo format_datetime($c['date_cloture']); ?></span>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?> alert-dismissible fade show">
        <?php echo $_SESSION['flash']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
        <?php foreach (($c['messages'] ?? []) as $m): ?>
            <?php $estAdmin = ($m['expediteur']['type_utilisateur'] ?? '') === 'responsable'; ?>
            <div class="d-flex <?php echo $estAdmin ? 'justify-content-start' : 'justify-content-end'; ?> mb-3">
                <div class="p-3 rounded <?php echo $estAdmin ? 'bg-light border' : 'bg-success bg-opacity-10 border border-success-subtle'; ?>" style="max-width: 70%;">
                    <p class="mb-1"><?php echo nl2br(htmlspecialchars($m['contenu'])); ?></p>
                    <p class="text-muted small mb-0 text-end">
                        <?php echo format_datetime($m['date_envoi']); ?><br>
                        <strong>— <?php echo htmlspecialchars(nom_affichage_participant($m['expediteur'] ?? null)); ?></strong>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if (empty($c['cloturee'])): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="<?php echo url('/commercant/messages/' . (int)$c['id'] . '/repondre'); ?>">
                <div class="mb-2">
                    <textarea name="contenu" class="form-control" rows="3" placeholder="Écrire une réponse..." required></textarea>
                </div>
                <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> Répondre</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-light border small">
        <i class="bi bi-info-circle"></i> Cette conversation a été clôturée par un administrateur. Pour continuer l'échange, envoyez un
        <a href="<?php echo url('/commercant/messages/creer'); ?>">nouveau message</a>.
    </div>
<?php endif; ?>
