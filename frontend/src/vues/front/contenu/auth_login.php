<div class="row justify-content-center">
    <div class="col-md-6">
        <h1 class="mb-4">Connexion administration</h1>
        <form method="POST" action="<?php echo url('/admin/login'); ?>">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="admin@nomorewaste.org" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="mot_de_passe" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>
        <p class="text-muted small mt-3">Compte démo : admin@nomorewaste.org / admin123</p>
    </div>
</div>
