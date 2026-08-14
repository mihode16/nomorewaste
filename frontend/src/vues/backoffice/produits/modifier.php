<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $produit
 * @var array $collectes
 */
$p = $produit; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/produits'); ?>" class="btn btn-secondary">Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo url('/admin/produits/' . (int)$p['id']); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Code-barres</label>
                    <input type="text" name="code_barre" class="form-control" value="<?php echo htmlspecialchars($p['code_barre']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($p['nom']); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Catégorie</label>
                    <input type="text" name="categorie" class="form-control" value="<?php echo htmlspecialchars($p['categorie'] ?? ''); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Quantité</label>
                    <input type="number" name="quantite" class="form-control" value="<?php echo (int)$p['quantite']; ?>" min="1">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="À venir" <?php echo ($p['statut'] ?? '') === 'À venir' ? 'selected' : ''; ?>>À venir</option>
                        <?php foreach (['Stocké', 'En tournée', 'Distribué'] as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo ($p['statut'] ?? '') === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date péremption</label>
                    <input type="date" name="date_peremption" class="form-control" value="<?php echo date('Y-m-d', strtotime($p['date_peremption'])); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Collecte</label>
                    <select name="collecte_id" class="form-select">
                        <option value="0">— Aucune —</option>
                        <?php foreach ($collectes as $c): ?>
                            <option value="<?php echo (int)$c['id']; ?>" <?php echo (int)$c['id'] === (int)($p['collecte_id'] ?? 0) ? 'selected' : ''; ?>>
                                #<?php echo (int)$c['id']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </div>
        </form>
    </div>
</div>