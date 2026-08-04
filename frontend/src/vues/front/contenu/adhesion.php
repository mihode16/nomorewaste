<h1><?php echo __('adhesion_title'); ?></h1>
<p class="lead"><?php echo __('adhesion_intro'); ?></p>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="<?php echo url('/adherer'); ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mot de passe *</label>
                            <input type="password" name="mot_de_passe" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom *</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="telephone" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date début adhésion</label>
                            <input type="date" name="date_debut_adhesion" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Adresse</label>
                            <textarea name="adresse" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success btn-lg"><?php echo __('btn_register'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <h5>Avantages adhérent</h5>
                <ul>
                    <li>Accès aux cours de cuisine</li>
                    <li>Conseils anti-gaspillage</li>
                    <li>Partage de véhicules</li>
                    <li>Échange de services</li>
                    <li>Services de réparation</li>
                </ul>
                <p class="text-muted small">Cotisation annuelle : faible tarif</p>
            </div>
        </div>
    </div>
</div>
