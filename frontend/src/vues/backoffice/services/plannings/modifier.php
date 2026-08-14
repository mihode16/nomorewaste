<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $planning
 * @var array $services
 * @var array $benevoles
 */
$p = $planning; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre); ?></h2>
    <a href="<?php echo url('/admin/services/plannings/' . (int)$p['id']); ?>" class="btn btn-outline-secondary">
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

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="<?php echo url('/admin/services/plannings/' . (int)$p['id']); ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Service *</label>
                    <select name="service_id" class="form-select" required>
                        <option value="">— Choisir —</option>
                        <?php foreach ($services as $s): ?>
                            <option value="<?php echo (int)$s['id']; ?>" <?php echo ((int)$s['id'] === (int)$p['service_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Bénévole animateur</label>
                    <select name="benevole_id" class="form-select">
                        <option value="0">— Aucun —</option>
                        <?php foreach ($benevoles as $b): ?>
                            <option value="<?php echo (int)$b['id']; ?>" <?php echo ((int)$b['id'] === (int)$p['benevole_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($b['prenom'] . ' ' . $b['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date/heure début *</label>
                    <input type="datetime-local" name="date_heure_debut" class="form-control"
                           value="<?php echo date('Y-m-d\TH:i', strtotime($p['date_heure_debut'])); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date/heure fin *</label>
                    <input type="datetime-local" name="date_heure_fin" class="form-control"
                           value="<?php echo date('Y-m-d\TH:i', strtotime($p['date_heure_fin'])); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Capacité max *</label>
                    <input type="number" name="capacite_max" class="form-control" value="<?php echo (int)$p['capacite_max']; ?>" min="1" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <?php foreach (['Ouvert', 'Complet', 'Annulé', 'Terminé'] as $st): ?>
                            <option value="<?php echo $st; ?>" <?php echo ($p['statut'] ?? '') === $st ? 'selected' : ''; ?>><?php echo $st; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Enregistrer
                    </button>
                    <a href="<?php echo url('/admin/services/plannings/' . (int)$p['id']); ?>" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </div>
        </form>
    </div>
</div>
