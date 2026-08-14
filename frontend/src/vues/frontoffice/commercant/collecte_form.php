<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var string $mode
 * @var array $collecte
 * @var array $lignes
 * @var array $categories
 */
$c = $collecte; $estCreation = ($mode === 'creer'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url($estCreation ? '/commercant/collectes' : '/commercant/collectes/' . (int)$c['id']); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

<form method="POST" action="<?php echo $estCreation ? url('/commercant/collectes') : url('/commercant/collectes/' . (int)$c['id']); ?>">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white"><strong>Informations générales</strong></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date et heure souhaitées *</label>
                    <input type="datetime-local" name="date_heure_collecte" class="form-control"
                           value="<?php echo isset($c['date_heure_collecte']) ? date('Y-m-d\TH:i', strtotime($c['date_heure_collecte'])) : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Adresse de collecte *</label>
                    <input type="text" name="adresse_collecte" class="form-control" value="<?php echo htmlspecialchars($c['adresse_collecte'] ?? ''); ?>" required>
                </div>
                <div class="col-12 mb-0">
                    <label class="form-label">Commentaire</label>
                    <textarea name="commentaire" class="form-control" rows="2" placeholder="Précisions utiles pour l'équipe de collecte..."><?php echo htmlspecialchars($c['commentaire'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Produits à collecter</strong>
            <button type="button" class="btn btn-sm btn-success" id="btnAjouterLigne">
                <i class="bi bi-plus-circle"></i> Ajouter une ligne
            </button>
        </div>
        <div class="card-body">
            <p class="text-muted small">Indiquez la nature (catégorie), la quantité approximative et la date de péremption de chaque type de produit.</p>
            <datalist id="listeCategories">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>">
                <?php endforeach; ?>
            </datalist>
            <div class="row g-2 d-none d-md-flex mb-1">
                <div class="col-md-4"><label class="form-label small mb-0">Nom / description *</label></div>
                <div class="col-md-3"><label class="form-label small mb-0">Catégorie</label></div>
                <div class="col-md-2"><label class="form-label small mb-0">Quantité *</label></div>
                <div class="col-md-2"><label class="form-label small mb-0">Péremption *</label></div>
                <div class="col-md-1"></div>
            </div>
            <div id="lignesProduits">
                <?php foreach ($lignes as $ligne): ?>
                    <div class="row g-2 align-items-center mb-2 ligne-produit">
                        <div class="col-md-4">
                            <input type="text" name="nom[]" class="form-control form-control-sm" value="<?php echo htmlspecialchars($ligne['nom']); ?>" placeholder="Ex : Pains et viennoiseries" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="categorie[]" class="form-control form-control-sm" list="listeCategories" value="<?php echo htmlspecialchars($ligne['categorie']); ?>" placeholder="Ex : Boulangerie">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="quantite[]" class="form-control form-control-sm" value="<?php echo (int)$ligne['quantite']; ?>" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_peremption[]" class="form-control form-control-sm" value="<?php echo htmlspecialchars($ligne['date_peremption']); ?>" required>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-supprimer-ligne" title="Retirer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-success px-4">
        <i class="bi bi-check-circle"></i> <?php echo $estCreation ? 'Envoyer la demande' : 'Enregistrer les modifications'; ?>
    </button>
    <a href="<?php echo url($estCreation ? '/commercant/collectes' : '/commercant/collectes/' . (int)$c['id']); ?>" class="btn btn-outline-secondary px-4">Annuler</a>
</form>

<script>
document.getElementById('btnAjouterLigne').addEventListener('click', function () {
    const conteneur = document.getElementById('lignesProduits');
    const ligne = conteneur.querySelector('.ligne-produit').cloneNode(true);
    ligne.querySelectorAll('input').forEach(function (input) {
        if (input.type === 'number') { input.value = 1; } else { input.value = ''; }
    });
    conteneur.appendChild(ligne);
});

document.getElementById('lignesProduits').addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-supprimer-ligne');
    if (!btn) return;
    const lignes = document.querySelectorAll('.ligne-produit');
    if (lignes.length > 1) {
        btn.closest('.ligne-produit').remove();
    }
});
</script>
