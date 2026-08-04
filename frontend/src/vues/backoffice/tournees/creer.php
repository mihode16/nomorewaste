<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/tournees'); ?>" class="btn btn-secondary">Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo url('/admin/tournees'); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date et heure de départ *</label>
                    <input type="datetime-local" name="date_heure_depart" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Bénévole chauffeur *</label>
                    <select name="benevole_id" class="form-select" required>
                        <option value="">— Choisir —</option>
                        <?php foreach ($benevoles as $b): ?>
                            <option value="<?php echo (int)$b['id']; ?>"><?php echo htmlspecialchars($b['prenom'] . ' ' . $b['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Lieu de distribution *</label>
                    <select name="lieu_distribution_id" class="form-select" required>
                        <option value="">— Choisir —</option>
                        <?php foreach ($lieux as $l): ?>
                            <option value="<?php echo (int)$l['id']; ?>"><?php echo htmlspecialchars($l['nom'] . ' — ' . $l['adresse']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Adresse de départ *</label>
                    <input type="text" name="adresse_depart" class="form-control" value="Siège NO MORE WASTE, 75001 Paris" required>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Produits à livrer (stock disponible)</label>
                    <?php if (empty($produits)): ?>
                        <p class="text-muted">Aucun produit en stock</p>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($produits as $p): ?>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="produits_ids[]" value="<?php echo (int)$p['id']; ?>" id="prod<?php echo (int)$p['id']; ?>">
                                        <label class="form-check-label" for="prod<?php echo (int)$p['id']; ?>">
                                            <?php echo htmlspecialchars($p['nom']); ?> (<?php echo (int)$p['quantite']; ?>)
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success">Créer la tournée</button>
                </div>
            </div>
        </form>
    </div>
</div>
