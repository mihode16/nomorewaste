<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/services'); ?>" class="btn btn-secondary">Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo url('/admin/services/plannings'); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Service *</label>
                    <select name="service_id" class="form-select" required>
                        <option value="">— Choisir —</option>
                        <?php foreach ($services as $s): ?>
                            <option value="<?php echo (int)$s['id']; ?>"><?php echo htmlspecialchars($s['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Bénévole animateur</label>
                    <select name="benevole_id" class="form-select">
                        <option value="0">— Aucun —</option>
                        <?php foreach ($benevoles as $b): ?>
                            <option value="<?php echo (int)$b['id']; ?>"><?php echo htmlspecialchars($b['prenom'] . ' ' . $b['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date/heure début *</label>
                    <input type="datetime-local" name="date_heure_debut" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date/heure fin *</label>
                    <input type="datetime-local" name="date_heure_fin" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Capacité max *</label>
                    <input type="number" name="capacite_max" class="form-control" value="10" min="1" required>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success">Créer le planning</button>
                </div>
            </div>
        </form>
    </div>
</div>
