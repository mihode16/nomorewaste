<?php include __DIR__ . '/../../layouts/entete.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-0">
            <div class="p-3">
                <h4 class="text-white">NO MORE WASTE</h4>
                <hr class="border-light">
            </div>
            <nav class="nav flex-column">
                <a href="/admin/dashboard" class="nav-link">
                    <i class="bi bi-speedometer2"></i> Tableau de bord
                </a>
                <a href="/admin/commercants" class="nav-link">
                    <i class="bi bi-shop"></i> Commerçants
                </a>
                <a href="/admin/collectes" class="nav-link active">
                    <i class="bi bi-truck"></i> Collectes
                </a>
                <a href="/admin/produits" class="nav-link">
                    <i class="bi bi-box"></i> Produits
                </a>
                <a href="/admin/benevoles" class="nav-link">
                    <i class="bi bi-people"></i> Bénévoles
                </a>
                <a href="/admin/tournees" class="nav-link">
                    <i class="bi bi-route"></i> Tournées
                </a>
                <hr class="border-light">
                <a href="/admin/logout" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </nav>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo $titre ?? 'Gestion des collectes'; ?></h2>
                <a href="/admin/collectes/creer" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Nouvelle collecte
                </a>
            </div>

            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?> alert-dismissible fade show">
                    <?php echo $_SESSION['flash']['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date / Heure</th>
                                    <th>Adresse</th>
                                    <th>Commerçant</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($collectes)): ?>
                                    <tr><td colspan="6" class="text-center">Aucune collecte trouvée</td></tr>
                                <?php else: ?>
                                    <?php foreach ($collectes as $c): ?>
                                        <tr>
                                            <td><?php echo $c['id']; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($c['date_heure_collecte'])); ?></td>
                                            <td><?php echo htmlspecialchars($c['adresse_collecte']); ?></td>
                                            <td>
                                                <?php 
                                                $nomCommercant = isset($c['commercant']['raison_sociale']) 
                                                    ? $c['commercant']['raison_sociale'] 
                                                    : 'N/A';
                                                echo htmlspecialchars($nomCommercant);
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $badge = 'secondary';
                                                if ($c['statut'] === 'Planifiée') $badge = 'primary';
                                                if ($c['statut'] === 'En cours') $badge = 'warning';
                                                if ($c['statut'] === 'Terminée') $badge = 'success';
                                                ?>
                                                <span class="badge bg-<?php echo $badge; ?>">
                                                    <?php echo $c['statut'] ?? 'Planifiée'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/admin/collectes/<?php echo $c['id']; ?>/modifier" class="btn btn-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <?php if ($c['statut'] !== 'Terminée'): ?>
                                                        <form method="POST" action="/admin/collectes/<?php echo $c['id']; ?>/terminer" style="display:inline;">
                                                            <button type="submit" class="btn btn-success" onclick="return confirm('Marquer comme terminée ?')">
                                                                <i class="bi bi-check2"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <form method="POST" action="/admin/collectes/<?php echo $c['id']; ?>/supprimer" style="display:inline;" onsubmit="return confirm('Confirmer la suppression ?')">
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/pied.php'; ?>