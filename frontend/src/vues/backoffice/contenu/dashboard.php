<h2><?php echo htmlspecialchars($titre ?? 'Tableau de bord'); ?></h2>
<?php if (!empty($user)): ?>
    <p class="text-muted">Bienvenue, <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></p>
<?php endif; ?>

<div class="row mt-4">
    <div class="col-md-2">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body text-center">
                <h5>Commerçants</h5>
                <h2><?php echo (int)($nbCommercants ?? 0); ?></h2>
                <a href="<?php echo url('/admin/commercants'); ?>" class="text-white small">Voir →</a>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-success mb-3">
            <div class="card-body text-center">
                <h5>Collectes</h5>
                <h2><?php echo (int)($nbCollectes ?? 0); ?></h2>
                <a href="<?php echo url('/admin/collectes'); ?>" class="text-white small">Voir →</a>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body text-center">
                <h5>Produits</h5>
                <h2><?php echo (int)($nbProduits ?? 0); ?></h2>
                <a href="<?php echo url('/admin/produits'); ?>" class="text-white small">Voir →</a>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body text-center">
                <h5>Bénévoles</h5>
                <h2><?php echo (int)($nbBenevoles ?? 0); ?></h2>
                <a href="<?php echo url('/admin/benevoles'); ?>" class="text-white small">Voir →</a>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-info mb-3">
            <div class="card-body text-center">
                <h5>Tournées</h5>
                <h2><?php echo (int)($nbTournees ?? 0); ?></h2>
                <a href="<?php echo url('/admin/tournees'); ?>" class="text-white small">Voir →</a>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($adhesionsExpirantes)): ?>
<div class="alert alert-warning mt-4">
    <h5><i class="bi bi-exclamation-triangle"></i> Adhésions commerçants expirant bientôt</h5>
    <ul class="mb-0">
        <?php foreach ($adhesionsExpirantes as $c): ?>
            <li>
                <strong><?php echo htmlspecialchars($c['raison_sociale']); ?></strong>
                — expire le <?php echo format_date($c['date_fin_adhesion']); ?>
                <a href="<?php echo url('/admin/commercants/' . $c['id'] . '/modifier'); ?>" class="ms-2">Renouveler</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Actions rapides</div>
            <div class="card-body">
                <a href="<?php echo url('/admin/collectes/creer'); ?>" class="btn btn-outline-success me-2 mb-2"><i class="bi bi-truck"></i> Planifier collecte</a>
                <a href="<?php echo url('/admin/produits/creer'); ?>" class="btn btn-outline-warning me-2 mb-2"><i class="bi bi-box"></i> Ajouter produit</a>
                <a href="<?php echo url('/admin/tournees/creer'); ?>" class="btn btn-outline-info me-2 mb-2"><i class="bi bi-route"></i> Créer tournée</a>
                <a href="<?php echo url('/admin/benevoles/creer'); ?>" class="btn btn-outline-danger mb-2"><i class="bi bi-person-plus"></i> Inscrire bénévole</a>
            </div>
        </div>
    </div>
</div>
