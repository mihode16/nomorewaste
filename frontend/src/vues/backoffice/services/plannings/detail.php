<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $planning
 */
$p = $planning; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/services'); ?>" class="btn btn-outline-secondary">
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
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                <i class="bi bi-calendar-event text-primary"></i>
                <strong>Informations du planning</strong>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Service</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($p['service']['nom'] ?? 'Service #' . $p['service_id']); ?></dd>
                    <dt class="col-sm-4">Début</dt>
                    <dd class="col-sm-8"><?php echo format_datetime($p['date_heure_debut']); ?></dd>
                    <dt class="col-sm-4">Fin</dt>
                    <dd class="col-sm-8"><?php echo format_datetime($p['date_heure_fin']); ?></dd>
                    <dt class="col-sm-4">Statut</dt>
                    <dd class="col-sm-8"><?php echo badge_statut($p['statut']); ?></dd>
                    <dt class="col-sm-4">Bénévole en charge</dt>
                    <dd class="col-sm-8">
                        <?php if (!empty($p['benevole']) && !empty($p['benevole']['nom'])): ?>
                            <?php echo htmlspecialchars($p['benevole']['prenom'] . ' ' . $p['benevole']['nom']); ?>
                        <?php else: ?>
                            <span class="text-muted">Non assigné</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                <i class="bi bi-people text-primary"></i>
                <strong>Capacité</strong>
            </div>
            <div class="card-body text-center">
                <?php
                $nbInscrits = (int)($p['nb_inscrits'] ?? 0);
                $capacite = (int)$p['capacite_max'];
                $plein = $capacite > 0 && $nbInscrits >= $capacite;
                $pourcentage = $capacite > 0 ? min(100, round($nbInscrits / $capacite * 100)) : 0;
                ?>
                <div class="display-6 mb-1"><?php echo $nbInscrits; ?> / <?php echo $capacite; ?></div>
                <div class="progress mb-2" style="height: 8px;">
                    <div class="progress-bar <?php echo $plein ? 'bg-danger' : 'bg-primary'; ?>" style="width: <?php echo $pourcentage; ?>%"></div>
                </div>
                <span class="text-muted small"><?php echo $plein ? 'Planning complet' : ($capacite - $nbInscrits) . ' place(s) restante(s)'; ?></span>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-grid gap-2">
                <a href="<?php echo url('/admin/services/plannings/' . (int)$p['id'] . '/modifier'); ?>" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Modifier
                </a>
                <form method="POST" action="<?php echo url('/admin/services/plannings/' . (int)$p['id'] . '/supprimer'); ?>" onsubmit="return confirm('Supprimer ce planning ?');">
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
