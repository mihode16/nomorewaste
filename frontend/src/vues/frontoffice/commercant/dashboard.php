<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $commercant
 * @var int $nbTotal
 * @var int $nbTerminees
 * @var array $prochaine
 * @var array $dernieresCollectes
 */
$c = $commercant; $statutCalcule = statut_adhesion_calcule($c); ?>
<div class="mb-4">
    <h2 class="mb-1">Bonjour, <?php echo htmlspecialchars($c['raison_sociale'] ?: ($c['prenom'] . ' ' . $c['nom'])); ?> 👋</h2>
    <p class="text-muted">Bienvenue sur votre espace commerçant NO MORE WASTE.</p>
</div>

<?php if (($c['statut_adhesion'] ?? '') === 'en_attente'): ?>
    <div class="alert alert-warning d-flex align-items-center gap-2">
        <i class="bi bi-hourglass-split fs-4"></i>
        <div>Votre adhésion est <strong>en attente de validation</strong> par l'association. Certaines fonctionnalités seront limitées jusqu'à validation.</div>
    </div>
<?php elseif (in_array($statutCalcule['label'], ['Expire bientôt', 'À renouveler', 'Expirée'], true)): ?>
    <div class="alert alert-<?php echo $statutCalcule['label'] === 'Expirée' ? 'danger' : 'warning'; ?> d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-bell fs-4"></i>
            <div>
                <?php if ($statutCalcule['label'] === 'Expirée'): ?>
                    Votre adhésion a expiré le <strong><?php echo format_date($c['date_fin_adhesion']); ?></strong>.
                <?php else: ?>
                    Votre adhésion arrive à échéance le <strong><?php echo format_date($c['date_fin_adhesion']); ?></strong>.
                <?php endif; ?>
                <?php if (!empty($c['demande_renouvellement'])): ?>
                    Votre demande de renouvellement est en cours de traitement.
                <?php else: ?>
                    Pensez à demander son renouvellement.
                <?php endif; ?>
            </div>
        </div>
        <?php if (empty($c['demande_renouvellement'])): ?>
            <a href="<?php echo url('/commercant/adhesion'); ?>" class="btn btn-sm btn-success">Demander le renouvellement</a>
        <?php endif; ?>
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
                    <div class="text-muted small">Collectes au total</div>
                    <div class="fs-3 fw-semibold"><?php echo (int)$nbTotal; ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="bi bi-check2-circle fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Collectes terminées</div>
                    <div class="fs-3 fw-semibold"><?php echo (int)$nbTerminees; ?></div>
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
                    <div class="text-muted small">Prochaine collecte</div>
                    <div class="fs-6 fw-semibold">
                        <?php echo $prochaine ? format_datetime($prochaine['date_heure_collecte']) : 'Aucune prévue'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-clock-history text-success"></i> Dernières collectes</strong>
        <a href="<?php echo url('/commercant/collectes'); ?>" class="btn btn-sm btn-outline-success">Voir tout</a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($dernieresCollectes)): ?>
            <p class="text-muted text-center py-4 mb-0">Aucune collecte pour le moment.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Adresse</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dernieresCollectes as $col): ?>
                            <tr>
                                <td><?php echo format_datetime($col['date_heure_collecte']); ?></td>
                                <td><?php echo htmlspecialchars($col['adresse_collecte']); ?></td>
                                <td><?php echo badge_statut($col['statut'] === '' ? 'En attente' : $col['statut']); ?></td>
                                <td class="text-end">
                                    <a href="<?php echo url('/commercant/collectes/' . $col['id']); ?>" class="btn btn-sm btn-outline-secondary">
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
