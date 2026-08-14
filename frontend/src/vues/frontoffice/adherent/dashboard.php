<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $adherent
 * @var int $nbInscriptions
 * @var array $prochainesSeances
 * @var int $nbMessagesNonLus
 */
$a = $adherent; $statutCalcule = statut_adhesion_calcule($a); ?>
<div class="mb-4">
    <h2 class="mb-1">Bonjour, <?php echo htmlspecialchars($a['prenom'] . ' ' . $a['nom']); ?> 👋</h2>
    <p class="text-muted">Bienvenue sur votre espace adhérent NO MORE WASTE.</p>
</div>

<?php if (($a['statut_adhesion'] ?? '') === 'en_attente'): ?>
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
                    Votre adhésion a expiré le <strong><?php echo format_date($a['date_fin_adhesion']); ?></strong>.
                <?php else: ?>
                    Votre adhésion arrive à échéance le <strong><?php echo format_date($a['date_fin_adhesion']); ?></strong>.
                <?php endif; ?>
                <?php if (!empty($a['demande_renouvellement'])): ?>
                    Votre demande de renouvellement est en cours de traitement.
                <?php else: ?>
                    Pensez à demander son renouvellement.
                <?php endif; ?>
            </div>
        </div>
        <?php if (empty($a['demande_renouvellement'])): ?>
            <a href="<?php echo url('/adherent/adhesion'); ?>" class="btn btn-sm btn-success">Renouveler</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="bi bi-calendar-check fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Inscriptions aux services</div>
                    <div class="fs-3 fw-semibold"><?php echo (int)$nbInscriptions; ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="bi bi-calendar-event fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Prochaine séance</div>
                    <div class="fs-6 fw-semibold">
                        <?php echo !empty($prochainesSeances) ? format_datetime($prochainesSeances[0]['date_heure_debut']) : 'Aucune prévue'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-warning-subtle text-warning rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="bi bi-envelope fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Messages non lus</div>
                    <div class="fs-3 fw-semibold"><?php echo (int)$nbMessagesNonLus; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-calendar-event text-success"></i> Mes prochaines séances</strong>
        <a href="<?php echo url('/adherent/services'); ?>" class="btn btn-sm btn-outline-success">Voir les services</a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($prochainesSeances)): ?>
            <p class="text-muted text-center py-4 mb-0">Vous n'êtes inscrit à aucune séance à venir.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prochainesSeances as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['service']['nom'] ?? '-'); ?></td>
                                <td><?php echo format_datetime($p['date_heure_debut']); ?></td>
                                <td><?php echo badge_statut($p['statut']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
