<?php include __DIR__ . '/../../layouts/entete.php'; ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Connexion - NO MORE WASTE</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['flash'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?>">
                            <?php echo $_SESSION['flash']['message']; ?>
                        </div>
                        <?php unset($_SESSION['flash']); ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="/admin/login">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="admin@nomorewaste.org" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="mot_de_passe" class="form-control" value="admin123" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/pied.php'; ?>