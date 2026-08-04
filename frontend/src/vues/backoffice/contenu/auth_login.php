<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titre ?? 'Connexion'); ?> — NO MORE WASTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #ecf0f1; min-height: 100vh; display: flex; align-items: center; }
        .login-card { max-width: 420px; margin: 0 auto; }
    </style>
</head>
<body>
<div class="container">
    <div class="login-card">
        <div class="text-center mb-4">
            <h2 class="text-success fw-bold">NO MORE WASTE</h2>
            <p class="text-muted">Administration</p>
        </div>
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash']['type']); ?>">
                <?php echo htmlspecialchars($_SESSION['flash']['message']); ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
        <div class="card shadow">
            <div class="card-body p-4">
                <form method="POST" action="<?php echo url('/admin/login'); ?>">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="admin@nomorewaste.org" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="mot_de_passe" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Se connecter</button>
                </form>
                <p class="text-muted small mt-3 mb-0 text-center">Compte démo : admin@nomorewaste.org / admin123</p>
            </div>
        </div>
        <p class="text-center mt-3">
            <a href="<?php echo url('/'); ?>" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Retour au site</a>
        </p>
    </div>
</div>
</body>
</html>
