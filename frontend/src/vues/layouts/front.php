<!DOCTYPE html>
<html lang="<?php echo lang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titre ?? 'NO MORE WASTE'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold text-success" href="<?php echo url('/'); ?>">NO MORE WASTE</a>
        <div class="d-flex align-items-center">
            <a class="nav-link d-inline" href="<?php echo url('/'); ?>"><?php echo __('nav_home'); ?></a>
            <a class="nav-link d-inline" href="<?php echo url('/services'); ?>"><?php echo __('nav_services'); ?></a>
            <a class="nav-link d-inline" href="<?php echo url('/adherer'); ?>"><?php echo __('nav_adhesion'); ?></a>
            <div class="dropdown ms-2">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                    <?php echo strtoupper(lang()); ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo lang_url('fr'); ?>">Français</a></li>
                    <li><a class="dropdown-item" href="<?php echo lang_url('en'); ?>">English</a></li>
                    <li><a class="dropdown-item" href="<?php echo lang_url('es'); ?>">Español</a></li>
                    <li><a class="dropdown-item" href="<?php echo lang_url('it'); ?>">Italiano</a></li>
                </ul>
            </div>
            <a class="btn btn-outline-primary btn-sm ms-2" href="<?php echo url('/admin'); ?>"><?php echo __('nav_admin'); ?></a>
        </div>
    </div>
</nav>
<div class="container pb-5">
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash']['type']); ?> alert-dismissible fade show">
            <?php echo htmlspecialchars($_SESSION['flash']['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <?php echo $contenu ?? ''; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
