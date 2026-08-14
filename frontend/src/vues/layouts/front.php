<!DOCTYPE html>
<html lang="<?php echo lang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titre ?? 'NO MORE WASTE'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; }
        .site-header { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .site-header .navbar-brand img { height: 38px; border-radius: 8px; margin-right: .5rem; }
        .site-header .nav-link { color: #333; font-weight: 500; }
        .site-header .nav-link.active, .site-header .nav-link:hover { color: #198754; }
        .site-footer { background: #1f2b23; color: rgba(255,255,255,.65); }
        .site-footer a { color: rgba(255,255,255,.8); text-decoration: none; }
        .site-footer a:hover { color: #fff; text-decoration: underline; }
        .site-footer h6 { color: #fff; font-size: .85rem; text-transform: uppercase; letter-spacing: .04em; }

        /* Bannière de titre réutilisable sur les pages de contenu */
        .page-hero {
            background: linear-gradient(120deg, #146c43 0%, #198754 100%);
            color: #fff; border-radius: 1rem; padding: 2.5rem 2rem; margin-bottom: 2.5rem;
        }
        .page-hero .bi { font-size: 2rem; opacity: .9; }

        /* Carte avec effet de survol, réutilisée sur plusieurs pages */
        .hover-card { transition: transform .15s ease, box-shadow .15s ease; }
        .hover-card:hover { transform: translateY(-4px); box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.08) !important; }

        .ratio-img { aspect-ratio: 4 / 3; object-fit: cover; }
        .ratio-img-wide { aspect-ratio: 16 / 9; object-fit: cover; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg site-header sticky-top py-2">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold text-success notranslate" href="<?php echo url('/'); ?>">
            <img src="/nomorewaste/assets/logo.png" alt="NO MORE WASTE">
            NO MORE WASTE
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navPublic">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navPublic">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="<?php echo url('/'); ?>"><?php echo __('nav_home'); ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo url('/services'); ?>"><?php echo __('nav_services'); ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo url('/adherer'); ?>"><?php echo __('nav_adhesion'); ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo url('/equipe'); ?>">Équipe</a></li>
                <li class="nav-item"><?php require __DIR__ . '/../partials/traduction_selecteur.php'; ?></li>
                <li class="nav-item"><a class="btn btn-success btn-sm ms-lg-2" href="<?php echo url('/connexion'); ?>"><?php echo __('nav_admin'); ?></a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container pb-5" style="flex: 1 0 auto;">
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash']['type']); ?> alert-dismissible fade show mt-4">
            <?php echo htmlspecialchars($_SESSION['flash']['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <?php echo $contenu ?? ''; ?>
</div>

<footer class="site-footer pt-4 pb-3">
    <div class="container">
        <div class="row gy-3 align-items-start">
            <div class="col-md-5">
                <a class="d-flex align-items-center fw-bold text-white text-decoration-none mb-2 notranslate" href="<?php echo url('/'); ?>">
                    <img src="/nomorewaste/assets/logo.png" alt="NO MORE WASTE" style="height:28px;border-radius:6px;margin-right:.5rem;">
                    NO MORE WASTE
                </a>
                <p class="small mb-0">Association de lutte contre le gaspillage alimentaire.</p>
            </div>
            <div class="col-6 col-md-3">
                <h6 class="mb-2">Rejoindre</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-1"><a href="<?php echo url('/adherer'); ?>">Devenir adhérent</a></li>
                    <li class="mb-1"><a href="<?php echo url('/devenir-benevole'); ?>">Devenir bénévole</a></li>
                    <li class="mb-1"><a href="<?php echo url('/devenir-commercant'); ?>">Donner mes invendus</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-4">
                <h6 class="mb-2">Informations</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-1"><a href="<?php echo url('/equipe'); ?>">Notre équipe</a></li>
                    <li class="mb-1"><a href="<?php echo url('/mentions-legales'); ?>">Mentions légales</a></li>
                    <li class="mb-1"><a href="<?php echo url('/connexion'); ?>">Connexion</a></li>
                </ul>
            </div>
        </div>
        <hr class="my-3" style="border-color: rgba(255,255,255,.12);">
        <div class="d-flex flex-wrap justify-content-between align-items-center small">
            <span>&copy; <?php echo date('Y'); ?> NO MORE WASTE</span>
            <span class="text-white-50">Hébergé par Bullionet</span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php require __DIR__ . '/../partials/traduction_widget.php'; ?>
</body>
</html>
