<?php
$pageActive = $pageActive ?? '';
$__clientApiBadge = new ClientApi();
$__nonLusReponse = $__clientApiBadge->get('/api/conversations/non-lus', ['role' => 'admin']);
$nbMessagesNonLus = ($__nonLusReponse['code'] === 200) ? (int)($__nonLusReponse['data']['non_lus'] ?? 0) : 0;
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
                <img src="<?php echo url('/assets/logo.png'); ?>" style="height:55px; object-fit:cover; border-radius: 11px; margin-right:10px;">
                <div>
                    <h4 class="text-white mb-0 notranslate">NO MORE WASTE</h4>
                    <small class="text-white-50">Interface admin</small>
                </div>
            </div>
            <nav class="nav flex-column">
                <a href="<?php echo url('/admin/dashboard'); ?>" class="nav-link <?php echo $pageActive === 'dashboard' ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2"></i> Tableau de bord
                </a>
                <a href="<?php echo url('/admin/commercants'); ?>" class="nav-link <?php echo $pageActive === 'commercants' ? 'active' : ''; ?>">
                    <i class="bi bi-shop"></i> Commerçants
                </a>
                <a href="<?php echo url('/admin/adherents'); ?>" class="nav-link <?php echo $pageActive === 'adherents' ? 'active' : ''; ?>">
                    <i class="bi bi-person-badge"></i> Adhérents
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
                <a href="<?php echo url('/admin/administrateurs'); ?>" class="nav-link <?php echo $pageActive === 'administrateurs' ? 'active' : ''; ?>">
                    <i class="bi bi-shield-lock"></i> Comptes admin
                </a>
                <a href="<?php echo url('/admin/messages'); ?>" class="nav-link d-flex align-items-center <?php echo $pageActive === 'messages' ? 'active' : ''; ?>">
                    <i class="bi bi-envelope"></i>&nbsp; Messages
                    <?php if ($nbMessagesNonLus > 0): ?>
                        <span class="badge bg-danger rounded-pill ms-2"><?php echo $nbMessagesNonLus; ?></span>
                    <?php endif; ?>
                </a>
                <hr class="border-light">
                <a href="<?php echo url('/deconnexion'); ?>" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </nav>
        </div>
        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-end mb-3">
                <?php require __DIR__ . '/../partials/traduction_selecteur.php'; ?>
            </div>
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
<?php require __DIR__ . '/../partials/traduction_widget.php'; ?>
</body>
</html>
