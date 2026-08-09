<?php
$pageActive = $pageActive ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titre ?? 'NO MORE WASTE'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .sidebar { min-height: 100vh; background: #2c3e50; }
        .sidebar .nav-link { color: #ecf0f1; }
        .sidebar .nav-link:hover { background: #34495e; }
        .sidebar .nav-link.active { background: #1abc9c; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-0">
            <div class="p-3 d-flex align-items-center">
                <img src="/nomorewaste/assets/logo.png" style="height:55px; object-fit:cover; border-radius: 11px; margin-right:10px;">
                <h4 class="text-white mb-0">NO MORE WASTE</h4>
            </div>
            <nav class="nav flex-column">
                <a href="<?php echo url('/admin/dashboard'); ?>" class="nav-link <?php echo $pageActive === 'dashboard' ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2"></i> Tableau de bord
                </a>
                <a href="<?php echo url('/admin/commercants'); ?>" class="nav-link <?php echo $pageActive === 'commercants' ? 'active' : ''; ?>">
                    <i class="bi bi-shop"></i> Commerçants
                </a>
                <a href="<?php echo url('/admin/collectes'); ?>" class="nav-link <?php echo $pageActive === 'collectes' ? 'active' : ''; ?>">
                    <i class="bi bi-truck"></i> Collectes
                </a>
                <a href="<?php echo url('/admin/produits'); ?>" class="nav-link <?php echo $pageActive === 'produits' ? 'active' : ''; ?>">
                    <i class="bi bi-box"></i> Produits
                </a>
                <a href="<?php echo url('/admin/benevoles'); ?>" class="nav-link <?php echo $pageActive === 'benevoles' ? 'active' : ''; ?>">
                    <i class="bi bi-people"></i> Bénévoles
                </a>
                <a href="<?php echo url('/admin/tournees'); ?>" class="nav-link <?php echo $pageActive === 'tournees' ? 'active' : ''; ?>">
                    <i class="bi bi-route"></i> Tournées
                </a>
                <a href="<?php echo url('/admin/services'); ?>" class="nav-link <?php echo $pageActive === 'services' ? 'active' : ''; ?>">
                    <i class="bi bi-calendar-event"></i> Services
                </a>
                <hr class="border-light">
                <a href="<?php echo url('/admin/logout'); ?>" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </nav>
        </div>
        <div class="col-md-10 p-4">
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash']['type']); ?> alert-dismissible fade show">
                    <?php echo htmlspecialchars($_SESSION['flash']['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>
            <?php echo $contenu ?? ''; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
