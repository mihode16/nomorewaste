<?php
/**
 * @var string $titre
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titre ?? 'Connexion'); ?> — NO MORE WASTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            background: linear-gradient(160deg, rgba(20,83,45,.94) 0%, rgba(25,135,84,.88) 100%),
                        url('https://images.unsplash.com/photo-1542838132-92c53300491e?w=1600&q=60') center/cover no-repeat fixed;
        }
        .login-wrap { max-width: 430px; margin: 2rem auto; width: 100%; }
        .login-card { border: none; border-radius: 18px; overflow: hidden; box-shadow: 0 1.5rem 3rem rgba(0,0,0,.3); }
        .login-card-accent { height: 6px; background: linear-gradient(90deg, #146c43, #198754, #75d69c); }
        .login-brand {
            background: #fff;
            padding: 2rem 2rem 1.25rem;
            text-align: center;
        }
        .login-brand img { height: 56px; border-radius: 12px; margin-bottom: .75rem; }
        .login-body { padding: 1.5rem 2rem 2rem; }
        .login-body .input-group-text { border-right: none; }
        .login-body .form-control { border-left: none; }
        .login-body .form-control:focus { box-shadow: none; border-color: #86d9ab; }
        .login-body .input-group:focus-within .input-group-text { border-color: #86d9ab; }
        .login-back {
            color: rgba(255,255,255,.9);
            text-decoration: none;
            display: inline-flex; align-items: center; gap: .35rem;
            background: rgba(255,255,255,.12);
            padding: .35rem .9rem;
            border-radius: 50px;
            transition: background .15s ease;
        }
        .login-back:hover { color: #fff; background: rgba(255,255,255,.22); }
    </style>
</head>
<body>
<div class="container">
    <div class="login-wrap">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="<?php echo url('/'); ?>" class="login-back"><i class="bi bi-arrow-left"></i> Retour au site</a>
            <?php $traductionSelecteurClasse = 'btn-outline-light'; require __DIR__ . '/../partials/traduction_selecteur.php'; ?>
        </div>
        <div class="card login-card">
            <div class="login-card-accent"></div>
            <div class="login-brand">
                <img src="<?php echo url('/assets/logo.png'); ?>" alt="NO MORE WASTE">
                <h4 class="text-success fw-bold mb-0 notranslate">NO MORE WASTE</h4>
                <p class="text-muted small mb-0">Connexion à votre espace</p>
            </div>
            <div class="login-body">
                <?php if (isset($_SESSION['flash'])): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash']['type']); ?> py-2">
                        <?php echo htmlspecialchars($_SESSION['flash']['message']); ?>
                    </div>
                    <?php unset($_SESSION['flash']); ?>
                <?php endif; ?>
                <form method="POST" action="<?php echo url('/connexion'); ?>">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-envelope text-success"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="vous@exemple.fr" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-lock text-success"></i></span>
                            <input type="password" name="mot_de_passe" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
                        <i class="bi bi-box-arrow-in-right"></i> Se connecter
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../partials/traduction_widget.php'; ?>
</body>
</html>
