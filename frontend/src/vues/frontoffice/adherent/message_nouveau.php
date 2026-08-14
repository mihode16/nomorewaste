<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $autresAdherents
 */
?>
<h2 class="mb-4">Nouveau message</h2>

<div class="card border-0 shadow-sm" style="max-width: 650px;">
    <div class="card-body">
        <form method="POST" action="<?php echo url('/adherent/messages'); ?>">
            <div class="mb-3">
                <label class="form-label d-block">Destinataire</label>
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="cible" id="cibleAssociation" value="association" checked onclick="basculerDestinataire()">
                    <label class="btn btn-outline-success" for="cibleAssociation"><i class="bi bi-building"></i> L'association</label>

                    <input type="radio" class="btn-check" name="cible" id="cibleAdherent" value="adherent" onclick="basculerDestinataire()">
                    <label class="btn btn-outline-success" for="cibleAdherent"><i class="bi bi-people"></i> Un autre adhérent</label>
                </div>
            </div>
            <div class="mb-3" id="blocDestinataire" style="display:none;">
                <label class="form-label">Adhérent</label>
                <select name="destinataire_id" class="form-select">
                    <option value="">— Choisir un adhérent —</option>
                    <?php foreach ($autresAdherents as $ad): ?>
                        <option value="<?php echo (int)$ad['id']; ?>"><?php echo htmlspecialchars($ad['prenom'] . ' ' . $ad['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Sujet *</label>
                <input type="text" name="sujet" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Message *</label>
                <textarea name="contenu" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> Envoyer le message</button>
        </form>
    </div>
</div>

<script>
function basculerDestinataire() {
    const versAdherent = document.getElementById('cibleAdherent').checked;
    document.getElementById('blocDestinataire').style.display = versAdherent ? 'block' : 'none';
    document.querySelector('select[name="destinataire_id"]').required = versAdherent;
}
</script>
