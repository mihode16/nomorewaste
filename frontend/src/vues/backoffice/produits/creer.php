<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/produits'); ?>" class="btn btn-secondary">Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo url('/admin/produits'); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Code-barres *</label>
                    <input type="text" name="code_barre" class="form-control" required placeholder="Ex: 3760123456789">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Catégorie</label>
                    <input type="text" name="categorie" class="form-control" placeholder="Boulangerie, Frais...">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Quantité</label>
                    <input type="number" name="quantite" class="form-control" value="1" min="1">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date péremption *</label>
                    <input type="date" name="date_peremption" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Collecte associée</label>
                    <select name="collecte_id" class="form-select">
                        <option value="0">— Aucune —</option>
                        <?php foreach ($collectes as $c): ?>
                            <option value="<?php echo (int)$c['id']; ?>">#<?php echo (int)$c['id']; ?> — <?php echo format_datetime($c['date_heure_collecte']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success">Enregistrer en stock</button>
                </div>
            </div>
        </form>
    </div>
</div>
