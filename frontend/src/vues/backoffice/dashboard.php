<?php include __DIR__ . '/../layouts/entete.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar p-0">
            <div class="p-3">
                <h4 class="text-white">NO MORE WASTE</h4>
                <hr class="border-light">
            </div>
            <nav class="nav flex-column">
                <a href="/admin/dashboard" class="nav-link active">
                    <i class="bi bi-speedometer2"></i> Tableau de bord
                </a>
                <a href="/admin/commercants" class="nav-link">
                    <i class="bi bi-shop"></i> Commerçants
                </a>
                <a href="/admin/collectes" class="nav-link">
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

        <!-- Contenu principal -->
        <div class="col-md-10 p-4">
            <h2><?php echo $titre ?? 'Tableau de bord'; ?></h2>
            
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Commerçants</h5>
                            <h2><?php echo $nbCommercants ?? 0; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Collectes</h5>
                            <h2><?php echo $nbCollectes ?? 0; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">Produits</h5>
                            <h2>0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <h5 class="card-title">Bénévoles</h5>
                            <h2>0</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../vues/layouts/pied.php'; ?>