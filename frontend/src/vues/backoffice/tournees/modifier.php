<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $tournee
 * @var array $chauffeurs
 * @var array $benevoles
 * @var array $lieux
 * @var array $produits
 */
$t = $tournee; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><?php echo htmlspecialchars($titre); ?></h2>
        <div class="d-flex align-items-center gap-2 text-muted">
            <span>Tournée #<?php echo (int)$t['id']; ?></span>
            <?php echo badge_statut($t['statut']); ?>
        </div>
    </div>
    <a href="<?php echo url('/admin/tournees'); ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?> alert-dismissible fade show">
        <?php echo $_SESSION['flash']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<form method="POST" action="<?php echo url('/admin/tournees/' . (int)$t['id']); ?>">

    <!-- Informations générales -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex align-items-center gap-2">
            <i class="bi bi-info-circle text-primary"></i>
            <strong>Informations générales</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="bi bi-calendar-event me-1 text-muted"></i>Date et heure de départ *</label>
                    <input type="datetime-local" name="date_heure_depart" class="form-control"
                           value="<?php echo date('Y-m-d\TH:i', strtotime($t['date_heure_depart'])); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="bi bi-person-badge me-1 text-muted"></i>Bénévole chauffeur *</label>
                    <select name="benevole_id" class="form-select" required>
                        <option value="">— Choisir —</option>
                        <?php if (!empty($chauffeurs)): ?>
                            <?php foreach ($chauffeurs as $b): ?>
                                <option value="<?php echo (int)$b['id']; ?>" <?php echo ((int)$b['id'] === (int)$t['benevole_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($b['prenom'] . ' ' . $b['nom']); ?>
                                    <?php if (!empty($b['competences'])): ?>
                                        (<?php echo implode(', ', array_column($b['competences'], 'nom')); ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">Aucun bénévole disponible</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="bi bi-geo-alt me-1 text-muted"></i>Lieu de distribution *</label>
                    <div class="d-flex gap-2">
                        <select name="lieu_distribution_id" class="form-select" id="lieuSelect" required>
                            <option value="">— Choisir —</option>
                            <?php foreach ($lieux as $l): ?>
                                <option value="<?php echo (int)$l['id']; ?>" <?php echo ((int)$l['id'] === (int)$t['lieu_distribution_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($l['nom'] . ' — ' . $l['adresse']); ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="new">+ Ajouter un nouveau lieu</option>
                        </select>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#nouveauLieuModal" title="Ajouter un lieu">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="bi bi-signpost-split me-1 text-muted"></i>Adresse de départ *</label>
                    <input type="text" name="adresse_depart" class="form-control"
                           value="<?php echo htmlspecialchars($t['adresse_depart']); ?>" required>
                </div>
            </div>
        </div>
    </div>

    <!-- Bénévoles supplémentaires -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex align-items-center gap-2">
            <i class="bi bi-people text-primary"></i>
            <strong>Bénévoles supplémentaires</strong>
        </div>
        <div class="card-body">
            <?php if (empty($benevoles)): ?>
                <p class="text-muted mb-0">Aucun bénévole disponible.</p>
            <?php else: ?>
                <?php $existingBenevoles = array_column($t['benevoles'] ?? [], 'id'); ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2">
                    <?php foreach ($benevoles as $b): ?>
                        <?php if ((int)$b['id'] === (int)$t['benevole_id']) continue; ?>
                        <div class="col">
                            <label class="d-flex align-items-start gap-2 p-2 border rounded h-100 mb-0" style="cursor:pointer;">
                                <input class="form-check-input mt-1" type="checkbox" name="benevoles_supp[]"
                                       value="<?php echo (int)$b['id']; ?>"
                                       <?php echo in_array((int)$b['id'], $existingBenevoles) ? 'checked' : ''; ?>>
                                <span>
                                    <span class="d-block"><?php echo htmlspecialchars($b['prenom'] . ' ' . $b['nom']); ?></span>
                                    <?php if (!empty($b['competences'])): ?>
                                        <span class="d-block mt-1">
                                            <?php foreach ($b['competences'] as $comp): ?>
                                                <span class="badge bg-secondary me-1"><?php echo htmlspecialchars($comp['nom']); ?></span>
                                            <?php endforeach; ?>
                                        </span>
                                    <?php endif; ?>
                                </span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Produits à livrer -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex align-items-center gap-2">
            <i class="bi bi-box-seam text-primary"></i>
            <strong>Produits à livrer</strong>
        </div>
        <div class="card-body">
            <?php if (empty($produits)): ?>
                <p class="text-muted mb-0">Aucun produit disponible.</p>
            <?php else: ?>
                <?php $existingProduits = array_column($t['produits'] ?? [], 'id'); ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2">
                    <?php foreach ($produits as $p): ?>
                        <div class="col">
                            <label class="d-flex align-items-center gap-2 p-2 border rounded h-100 mb-0" style="cursor:pointer;">
                                <input class="form-check-input" type="checkbox" name="produits_ids[]"
                                       value="<?php echo (int)$p['id']; ?>"
                                       id="prod<?php echo (int)$p['id']; ?>"
                                       <?php echo in_array((int)$p['id'], $existingProduits) ? 'checked' : ''; ?>>
                                <span>
                                    <span class="d-block"><?php echo htmlspecialchars($p['nom']); ?></span>
                                    <small class="text-muted">Qté : <?php echo (int)$p['quantite']; ?></small>
                                </span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-circle"></i> Mettre à jour
        </button>
        <a href="<?php echo url('/admin/tournees'); ?>" class="btn btn-outline-secondary px-4">Annuler</a>
    </div>
</form>

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
