<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $commercant
 */
$c = $commercant; $statutCalcule = statut_adhesion_calcule($c); ?>
<h2 class="mb-4">Mon adhésion</h2>

<div class="row">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white"><strong>Détails de l'adhésion</strong></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Statut de la demande</dt>
                    <dd class="col-sm-7">
                        <?php if (($c['statut_adhesion'] ?? '') === 'valide'): ?>
                            <span class="badge bg-success">Validée</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">En attente de validation</span>
                        <?php endif; ?>
                    </dd>
                    <?php if (($c['statut_adhesion'] ?? '') === 'valide'): ?>
                        <dt class="col-sm-5">Statut</dt>
                        <dd class="col-sm-7">
                            <span class="badge <?php echo $statutCalcule['classe']; ?>"><?php echo $statutCalcule['label']; ?></span>
                        </dd>
                        <dt class="col-sm-5">Période</dt>
                        <dd class="col-sm-7"><?php echo format_date($c['date_debut_adhesion']); ?> au <?php echo format_date($c['date_fin_adhesion']); ?></dd>
                    <?php endif; ?>
                    <dt class="col-sm-5">Renouvellement auto</dt>
                    <dd class="col-sm-7"><?php echo !empty($c['est_renouvele_automatiquement']) ? 'Oui' : 'Non'; ?> — <a href="<?php echo url('/commercant/profil'); ?>">modifier</a></dd>
                    <dt class="col-sm-5">Raison sociale</dt>
                    <dd class="col-sm-7"><?php echo htmlspecialchars($c['raison_sociale']); ?></dd>
                    <dt class="col-sm-5">SIRET</dt>
                    <dd class="col-sm-7"><?php echo htmlspecialchars($c['siret']); ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong><i class="bi bi-bell text-success"></i> Renouvellement</strong></div>
            <div class="card-body">
                <?php if (($c['statut_adhesion'] ?? '') !== 'valide'): ?>
                    <p class="text-muted small mb-0">Votre demande d'adhésion est en cours de validation par l'association ; vous pourrez demander un renouvellement une fois celle-ci validée.</p>
                <?php elseif (!empty($c['demande_renouvellement'])): ?>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-hourglass-split"></i> Votre demande de renouvellement a bien été transmise à l'association et est en cours de traitement.
                    </div>
                <?php else: ?>
                    <p class="text-muted small">
                        NO MORE WASTE vous notifie automatiquement lorsque votre adhésion approche de son échéance
                        (<?php echo format_date($c['date_fin_adhesion']); ?>). Vous pouvez à tout moment demander son renouvellement.
                    </p>
                    <form method="POST" action="<?php echo url('/commercant/adhesion/demander-renouvellement'); ?>">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-arrow-repeat"></i> Demander le renouvellement
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
