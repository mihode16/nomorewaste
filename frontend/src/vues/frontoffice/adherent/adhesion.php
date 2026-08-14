<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $adherent
 * @var float $prixMensuel
 */
$a = $adherent; $statutCalcule = statut_adhesion_calcule($a); ?>
<h2 class="mb-4">Mon adhésion</h2>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white"><strong>Détails de l'adhésion</strong></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Statut de la demande</dt>
                    <dd class="col-sm-7">
                        <?php if (($a['statut_adhesion'] ?? '') === 'valide'): ?>
                            <span class="badge bg-success">Validée</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">En attente de validation</span>
                        <?php endif; ?>
                    </dd>
                    <?php if (($a['statut_adhesion'] ?? '') === 'valide'): ?>
                        <dt class="col-sm-5">Statut</dt>
                        <dd class="col-sm-7">
                            <span class="badge <?php echo $statutCalcule['classe']; ?>"><?php echo $statutCalcule['label']; ?></span>
                        </dd>
                        <dt class="col-sm-5">Période</dt>
                        <dd class="col-sm-7"><?php echo format_date($a['date_debut_adhesion']); ?> au <?php echo format_date($a['date_fin_adhesion']); ?></dd>
                    <?php endif; ?>
                    <dt class="col-sm-5">Renouvellement auto</dt>
                    <dd class="col-sm-7"><?php echo !empty($a['est_renouvele_automatiquement']) ? 'Oui' : 'Non'; ?> — <a href="<?php echo url('/adherent/profil'); ?>">modifier</a></dd>
                    <dt class="col-sm-5">Cotisation</dt>
                    <dd class="col-sm-7"><?php echo number_format($prixMensuel, 2); ?> € / mois</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong><i class="bi bi-credit-card text-success"></i> Renouvellement</strong></div>
            <div class="card-body">
                <?php if (($a['statut_adhesion'] ?? '') !== 'valide'): ?>
                    <p class="text-muted small mb-0">Votre demande d'adhésion est en cours de validation par l'association ; vous pourrez demander un renouvellement une fois celle-ci validée.</p>
                <?php elseif (!empty($a['demande_renouvellement'])): ?>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-hourglass-split"></i> Votre demande de renouvellement a bien été transmise à l'association et est en cours de traitement.
                    </div>
                <?php else: ?>
                    <p class="text-muted small">
                        Votre adhésion actuelle se termine le <?php echo format_date($a['date_fin_adhesion']); ?>.
                        Choisissez une durée pour simuler le paiement de votre renouvellement.
                    </p>
                    <form method="POST" action="<?php echo url('/adherent/adhesion/demander-renouvellement'); ?>">
                        <div class="mb-3">
                            <label class="form-label">Durée</label>
                            <select name="duree_mois" id="dureeMois" class="form-select" onchange="majTotal()">
                                <option value="6">6 mois</option>
                                <option value="12" selected>12 mois</option>
                                <option value="24">24 mois</option>
                            </select>
                        </div>
                        <div class="alert alert-light border d-flex justify-content-between align-items-center">
                            <span>Total à payer</span>
                            <strong id="totalPrix" class="fs-5 text-success">0,00 €</strong>
                        </div>
                        <fieldset class="border rounded p-3 mb-3">
                            <legend class="fs-6 text-muted px-1 w-auto mb-2"><i class="bi bi-lock"></i> Paiement (simulation)</legend>
                            <div class="mb-2">
                                <label class="form-label small">Numéro de carte</label>
                                <input type="text" class="form-control" placeholder="4242 4242 4242 4242" maxlength="19" required>
                            </div>
                            <div class="row g-2">
                                <div class="col-7">
                                    <label class="form-label small">Expiration</label>
                                    <input type="text" class="form-control" placeholder="MM/AA" maxlength="5" required>
                                </div>
                                <div class="col-5">
                                    <label class="form-label small">CVV</label>
                                    <input type="text" class="form-control" placeholder="123" maxlength="3" required>
                                </div>
                            </div>
                            <p class="text-muted small mb-0 mt-2">
                                <i class="bi bi-info-circle"></i> Aucun paiement réel n'est effectué : cet écran simule le principe d'un
                                abonnement. Votre demande sera confirmée par un administrateur de l'association.
                            </p>
                        </fieldset>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check2-circle"></i> Payer et renouveler (simulation)
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
const prixMensuel = <?php echo (float)$prixMensuel; ?>;
function majTotal() {
    const mois = parseInt(document.getElementById('dureeMois').value, 10);
    document.getElementById('totalPrix').textContent = (mois * prixMensuel).toFixed(2).replace('.', ',') + ' €';
}
majTotal();
</script>
