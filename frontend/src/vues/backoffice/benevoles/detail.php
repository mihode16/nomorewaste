<?php $b = $benevole; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($b['prenom'] . ' ' . $b['nom']); ?></h2>
    <a href="<?php echo url('/admin/benevoles'); ?>" class="btn btn-secondary">Retour</a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">Informations</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9"><?php echo htmlspecialchars($b['email']); ?></dd>
                    <dt class="col-sm-3">Téléphone</dt>
                    <dd class="col-sm-9"><?php echo htmlspecialchars($b['telephone'] ?? '-'); ?></dd>
                    <dt class="col-sm-3">Adresse</dt>
                    <dd class="col-sm-9"><?php echo htmlspecialchars($b['adresse'] ?? '-'); ?></dd>
                    <dt class="col-sm-3">Candidature</dt>
                    <dd class="col-sm-9"><?php echo format_date($b['date_candidature']); ?></dd>
                    <dt class="col-sm-3">Statut</dt>
                    <dd class="col-sm-9"><?php echo badge_statut($b['statut_candidature']); ?></dd>
                </dl>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Compétences</div>
            <div class="card-body">
                <?php if (!empty($b['competences'])): ?>
                    <?php foreach ($b['competences'] as $comp): ?>
                        <span class="badge bg-primary me-1"><?php echo htmlspecialchars($comp['nom']); ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted mb-0">Aucune compétence renseignée</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Changer le statut</div>
            <div class="card-body">
                <form method="POST" action="<?php echo url('/admin/benevoles/' . (int)$b['id'] . '/statut'); ?>">
                    <select name="statut" class="form-select mb-3">
                        <?php foreach (['En attente', 'Validé', 'Refusé'] as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo ($b['statut_candidature'] ?? '') === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary w-100">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>
</div>
