<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $benevoles
 * @var array $lieux
 * @var array $produits
 */
?>
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
                    <div class="d-flex gap-2">
                        <select name="lieu_distribution_id" class="form-select" id="lieuSelect" required>
                            <option value="">— Choisir —</option>
                            <?php foreach ($lieux as $l): ?>
                                <option value="<?php echo (int)$l['id']; ?>"><?php echo htmlspecialchars($l['nom'] . ' — ' . $l['adresse']); ?></option>
                            <?php endforeach; ?>
                            <option value="new">+ Ajouter un nouveau lieu</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#nouveauLieuModal">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Adresse de départ *</label>
                    <input type="text" name="adresse_depart" class="form-control" value="Siège NO MORE WASTE, 75001 Paris" required>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Bénévoles supplémentaires (facultatif)</label>
                    <select name="benevoles_supp[]" class="form-select" multiple size="3">
                        <?php foreach ($benevoles as $b): ?>
                            <option value="<?php echo (int)$b['id']; ?>"><?php echo htmlspecialchars($b['prenom'] . ' ' . $b['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Maintenez Ctrl pour sélectionner plusieurs.</small>
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

<!-- Modal pour ajouter un nouveau lieu -->
<div class="modal fade" id="nouveauLieuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouveau lieu de distribution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formNouveauLieu">
                    <div class="mb-3">
                        <label class="form-label">Nom *</label>
                        <input type="text" id="lieu_nom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adresse *</label>
                        <input type="text" id="lieu_adresse" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <input type="text" id="lieu_type" class="form-control" placeholder="Association, Centre social...">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" id="btnAjouterLieu">Ajouter</button>
            </div>
        </div>
    </div>
</div>

<script>
// Gestion de l'ajout de lieu via AJAX
document.getElementById('btnAjouterLieu').addEventListener('click', function() {
    const nom = document.getElementById('lieu_nom').value.trim();
    const adresse = document.getElementById('lieu_adresse').value.trim();
    const type = document.getElementById('lieu_type').value.trim();
    if (!nom || !adresse) {
        alert('Nom et adresse sont obligatoires.');
        return;
    }
    fetch('<?php echo api_url('/api/lieux-distribution'); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nom: nom, adresse: adresse, type: type })
    })
    .then(res => res.json())
    .then(data => {
        if (data.id) {
            const select = document.getElementById('lieuSelect');
            const opt = document.createElement('option');
            opt.value = data.id;
            opt.textContent = nom + ' — ' + adresse;
            select.insertBefore(opt, select.lastElementChild);
            select.value = data.id;
            bootstrap.Modal.getInstance(document.getElementById('nouveauLieuModal')).hide();
            document.getElementById('lieu_nom').value = '';
            document.getElementById('lieu_adresse').value = '';
            document.getElementById('lieu_type').value = '';
        } else {
            alert('Erreur lors de l\'ajout du lieu.');
        }
    })
    .catch(err => alert('Erreur réseau.'));
});
</script>