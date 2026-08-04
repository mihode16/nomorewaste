<?php $c = $collecte; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/collectes'); ?>" class="btn btn-secondary">Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo url('/admin/collectes/' . (int)$c['id']); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date et heure</label>
                    <input type="datetime-local" name="date_heure_collecte" class="form-control"
                           value="<?php echo date('Y-m-d\TH:i', strtotime($c['date_heure_collecte'])); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <?php foreach (['Planifiée', 'Terminée'] as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo ($c['statut'] ?? '') === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Commerçant</label>
                    <select name="commercant_id" class="form-select" required>
                        <?php foreach ($commercants as $com): ?>
                            <option value="<?php echo (int)$com['id']; ?>" <?php echo (int)$com['id'] === (int)$c['commercant_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($com['raison_sociale']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Adresse</label>
                    <textarea name="adresse_collecte" class="form-control" rows="2" required><?php echo htmlspecialchars($c['adresse_collecte']); ?></textarea>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Commentaire</label>
                    <textarea name="commentaire" class="form-control" rows="2"><?php echo htmlspecialchars($c['commentaire'] ?? ''); ?></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </div>
        </form>
    </div>
</div>
