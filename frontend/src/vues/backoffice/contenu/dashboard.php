<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var int $nbCommercants
 * @var int $nbCollectes
 * @var int $nbProduits
 * @var int $nbBenevoles
 * @var int $nbTournees
 * @var int $nbAdherents
 * @var array $adhesionsExpirantes
 * @var array $notifications
 * @var array $produitsParStatut
 * @var array $benevolesParCandidature
 * @var array $tourneesParStatut
 * @var array $user
 */
?>
<div class="d-flex justify-content-between align-items-center mb-1">
    <div>
        <h2 class="mb-1"><?php echo htmlspecialchars($titre ?? 'Tableau de bord'); ?></h2>
        <?php if (!empty($user)): ?>
            <p class="text-muted mb-0">Bienvenue, <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></p>
        <?php endif; ?>
    </div>
    <form method="POST" action="<?php echo url('/admin/dashboard/executer-taches'); ?>" onsubmit="return confirm('Exécuter maintenant les rappels de renouvellement et l\'envoi des plannings bénévoles du jour ?');">
        <button type="submit" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-envelope-arrow-up"></i> Exécuter les tâches quotidiennes maintenant
        </button>
    </form>
</div>

<!-- Statistiques -->
<div class="row mt-4 g-3">
    <?php
    $tuiles = [
        ['label' => 'Commerçants', 'valeur' => $nbCommercants ?? 0, 'icone' => 'bi-shop', 'couleur' => 'primary', 'lien' => '/admin/commercants'],
        ['label' => 'Collectes', 'valeur' => $nbCollectes ?? 0, 'icone' => 'bi-truck', 'couleur' => 'success', 'lien' => '/admin/collectes'],
        ['label' => 'Produits', 'valeur' => $nbProduits ?? 0, 'icone' => 'bi-box-seam', 'couleur' => 'warning', 'lien' => '/admin/produits'],
        ['label' => 'Bénévoles', 'valeur' => $nbBenevoles ?? 0, 'icone' => 'bi-people', 'couleur' => 'danger', 'lien' => '/admin/benevoles'],
        ['label' => 'Tournées', 'valeur' => $nbTournees ?? 0, 'icone' => 'bi-route', 'couleur' => 'info', 'lien' => '/admin/tournees'],
        ['label' => 'Adhérents', 'valeur' => $nbAdherents ?? 0, 'icone' => 'bi-person-badge', 'couleur' => 'secondary', 'lien' => '/admin/adherents'],
    ];
    ?>
    <?php foreach ($tuiles as $tuile): ?>
        <div class="col-6 col-md-4 col-lg">
            <a href="<?php echo url($tuile['lien']); ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 stat-tile">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-<?php echo $tuile['couleur']; ?>-subtle text-<?php echo $tuile['couleur']; ?>">
                            <i class="bi <?php echo $tuile['icone']; ?>"></i>
                        </div>
                        <div>
                            <div class="text-muted small"><?php echo $tuile['label']; ?></div>
                            <div class="fs-3 fw-semibold text-dark"><?php echo (int)$tuile['valeur']; ?></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="row mt-4 g-3">
    <!-- Notifications -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                <i class="bi bi-bell text-primary"></i>
                <strong>Notifications</strong>
                <?php if (!empty($notifications)): ?>
                    <span class="badge bg-primary rounded-pill ms-auto"><?php echo count($notifications); ?></span>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($notifications)): ?>
                    <p class="text-muted text-center py-4 mb-0"><i class="bi bi-check2-circle"></i> Rien à signaler pour le moment</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($notifications as $n): ?>
                            <a href="<?php echo htmlspecialchars($n['lien']); ?>" class="list-group-item list-group-item-action d-flex align-items-start gap-2">
                                <i class="bi <?php echo htmlspecialchars($n['icone']); ?> text-<?php echo htmlspecialchars($n['type']); ?> mt-1"></i>
                                <span class="small"><?php echo htmlspecialchars($n['message']); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Camembert produits -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                <i class="bi bi-pie-chart text-primary"></i>
                <strong>Produits par statut</strong>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <?php if (empty($produitsParStatut)): ?>
                    <p class="text-muted mb-0">Aucune donnée</p>
                <?php else: ?>
                    <canvas id="chartProduits" style="max-height: 240px;"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tournées par statut -->
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                <i class="bi bi-route text-primary"></i>
                <strong>Tournées</strong>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <?php if (empty($tourneesParStatut)): ?>
                    <p class="text-muted mb-0">Aucune donnée</p>
                <?php else: ?>
                    <canvas id="chartTournees" style="max-height: 220px;"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3 g-3">
    <!-- Bénévoles par candidature -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                <i class="bi bi-bar-chart text-primary"></i>
                <strong>Bénévoles par statut de candidature</strong>
            </div>
            <div class="card-body">
                <?php if (empty($benevolesParCandidature)): ?>
                    <p class="text-muted mb-0">Aucune donnée</p>
                <?php else: ?>
                    <canvas id="chartBenevoles" style="max-height: 220px;"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!empty($adhesionsExpirantes)): ?>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle text-warning"></i>
                <strong>Adhésions commerçants expirant bientôt</strong>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <?php foreach ($adhesionsExpirantes as $c): ?>
                        <li class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <span><strong><?php echo htmlspecialchars($c['raison_sociale']); ?></strong> — expire le <?php echo format_date($c['date_fin_adhesion']); ?></span>
                            <a href="<?php echo url('/admin/commercants/' . $c['id'] . '/modifier'); ?>" class="btn btn-sm btn-outline-warning">Renouveler</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="row mt-3 g-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge text-primary"></i>
                <strong>Actions rapides</strong>
            </div>
            <div class="card-body">
                <a href="<?php echo url('/admin/collectes/creer'); ?>" class="btn btn-outline-success me-2 mb-2"><i class="bi bi-truck"></i> Planifier collecte</a>
                <a href="<?php echo url('/admin/produits/creer'); ?>" class="btn btn-outline-warning me-2 mb-2"><i class="bi bi-box"></i> Ajouter produit</a>
                <a href="<?php echo url('/admin/tournees/creer'); ?>" class="btn btn-outline-info me-2 mb-2"><i class="bi bi-route"></i> Créer tournée</a>
                <a href="<?php echo url('/admin/benevoles/creer'); ?>" class="btn btn-outline-danger mb-2"><i class="bi bi-person-plus"></i> Inscrire bénévole</a>
            </div>
        </div>
    </div>
</div>

<style>
.stat-icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; flex-shrink: 0;
}
.stat-tile:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important; transition: box-shadow .15s; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const couleurs = {
    'Stocké': '#198754', 'À distribuer': '#0dcaf0', 'Distribué': '#6c757d', 'À venir': '#ffc107',
    'Validé': '#198754', 'En attente': '#ffc107', 'Refusé': '#dc3545',
    'Prévue': '#0d6efd', 'Terminée': '#198754',
    'Non défini': '#adb5bd',
};
function couleurPour(label) {
    return couleurs[label] || '#adb5bd';
}

<?php if (!empty($produitsParStatut)): ?>
new Chart(document.getElementById('chartProduits'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_keys($produitsParStatut)); ?>,
        datasets: [{
            data: <?php echo json_encode(array_values($produitsParStatut)); ?>,
            backgroundColor: <?php echo json_encode(array_keys($produitsParStatut)); ?>.map(couleurPour),
        }]
    },
    options: { plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } } }
});
<?php endif; ?>

<?php if (!empty($tourneesParStatut)): ?>
new Chart(document.getElementById('chartTournees'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_keys($tourneesParStatut)); ?>,
        datasets: [{
            data: <?php echo json_encode(array_values($tourneesParStatut)); ?>,
            backgroundColor: <?php echo json_encode(array_keys($tourneesParStatut)); ?>.map(couleurPour),
        }]
    },
    options: { plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } } }
});
<?php endif; ?>

<?php if (!empty($benevolesParCandidature)): ?>
new Chart(document.getElementById('chartBenevoles'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_keys($benevolesParCandidature)); ?>,
        datasets: [{
            label: 'Bénévoles',
            data: <?php echo json_encode(array_values($benevolesParCandidature)); ?>,
            backgroundColor: <?php echo json_encode(array_keys($benevolesParCandidature)); ?>.map(couleurPour),
            borderRadius: 6,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});
<?php endif; ?>
</script>
