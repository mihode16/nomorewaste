<?php
$pageActive = $pageActive ?? '';
$__clientApiBadge = new ClientApi();
$__nonLusReponse = $__clientApiBadge->get('/api/conversations/non-lus', ['role' => 'utilisateur', 'utilisateur_id' => (int)($_SESSION['user']['id'] ?? 0)]);
$nbMessagesNonLus = ($__nonLusReponse['code'] === 200) ? (int)($__nonLusReponse['data']['non_lus'] ?? 0) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titre ?? 'Espace bénévole'); ?> — NO MORE WASTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f5f8f6; }
        .navbar-brand img { height: 32px; border-radius: 7px; margin-right: .5rem; }
        .espace-nav .nav-link { color: rgba(255,255,255,.85); }
        .espace-nav .nav-link.active, .espace-nav .nav-link:hover { color: #fff; font-weight: 600; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold notranslate" href="<?php echo url('/benevole/dashboard'); ?>">
            <img src="/nomorewaste/assets/logo.png" alt="NO MORE WASTE">
            NO MORE WASTE
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navEspace">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navEspace">
            <ul class="navbar-nav espace-nav me-auto ms-3">
                <li class="nav-item">
                    <a class="nav-link <?php echo $pageActive === 'dashboard' ? 'active' : ''; ?>" href="<?php echo url('/benevole/dashboard'); ?>">
                        <i class="bi bi-speedometer2"></i> Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $pageActive === 'disponibilites' ? 'active' : ''; ?>" href="<?php echo url('/benevole/disponibilites'); ?>">
                        <i class="bi bi-calendar-week"></i> Mes disponibilités
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $pageActive === 'affectations' ? 'active' : ''; ?>" href="<?php echo url('/benevole/affectations'); ?>">
                        <i class="bi bi-list-check"></i> Mes affectations
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $pageActive === 'planning' ? 'active' : ''; ?>" href="<?php echo url('/benevole/planning'); ?>">
                        <i class="bi bi-file-earmark-excel"></i> Mon planning
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $pageActive === 'messages' ? 'active' : ''; ?>" href="<?php echo url('/benevole/messages'); ?>">
                        <i class="bi bi-envelope"></i> Messages
                        <?php if ($nbMessagesNonLus > 0): ?>
                            <span class="badge bg-danger rounded-pill"><?php echo $nbMessagesNonLus; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $pageActive === 'profil' ? 'active' : ''; ?>" href="<?php echo url('/benevole/profil'); ?>">
                        <i class="bi bi-person-circle"></i> Mon profil
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 small d-none d-md-inline">
                    <?php echo htmlspecialchars(($_SESSION['user']['prenom'] ?? '') . ' ' . ($_SESSION['user']['nom'] ?? '')); ?>
                </span>
                <?php $traductionSelecteurClasse = 'btn-outline-light'; require __DIR__ . '/../partials/traduction_selecteur.php'; ?>
                <a href="<?php echo url('/deconnexion'); ?>" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </div>
        </div>
    </div>
</nav>
<div class="container py-4">
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash']['type']); ?> alert-dismissible fade show">
            <?php echo $_SESSION['flash']['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <?php echo $contenu ?? ''; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php require __DIR__ . '/../partials/traduction_widget.php'; ?>
</body>
</html>
