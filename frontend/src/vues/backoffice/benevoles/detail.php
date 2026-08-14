<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $benevole
 * @var array $toutesCompetences
 * @var array $plannings
 */
$b = $benevole; ?>
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
        <!-- Gestion des compétences -->
        <div class="card">
            <div class="card-header">Compétences</div>
            <div class="card-body">
                <!-- Compétences actuelles -->
                <h6 class="mb-3">Compétences attribuées</h6>
                <?php if (!empty($b['competences'])): ?>
                    <ul class="list-unstyled">
                        <?php foreach ($b['competences'] as $comp): ?>
                            <li class="mb-1 d-flex justify-content-between align-items-center">
                                <span><?php echo htmlspecialchars($comp['nom']); ?></span>
                                <form method="POST" action="<?php echo url('/admin/benevoles/' . $b['id'] . '/supprimer-competence'); ?>" class="d-inline" onsubmit="return confirm('Supprimer cette compétence ?');">
                                    <input type="hidden" name="competence_id" value="<?php echo (int)$comp['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-x-circle"></i></button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Aucune compétence attribuée.</p>
                <?php endif; ?>

                <!-- Ajouter une compétence -->
                <h6 class="mt-3">Ajouter une compétence</h6>
                <?php
                $existingIds = array_column($b['competences'] ?? [], 'id');
                $available = array_filter($toutesCompetences ?? [], function($c) use ($existingIds) {
                    return !in_array($c['id'], $existingIds);
                });
                ?>
                <?php if (!empty($available)): ?>
                    <form method="POST" action="<?php echo url('/admin/benevoles/' . $b['id'] . '/ajouter-competence'); ?>" class="row g-2 align-items-end">
                        <div class="col-md-8">
                            <select name="competence_id" class="form-select form-select-sm" required>
                                <option value="">Choisir une compétence</option>
                                <?php foreach ($available as $comp): ?>
                                    <option value="<?php echo (int)$comp['id']; ?>"><?php echo htmlspecialchars($comp['nom']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-sm btn-success w-100"><i class="bi bi-plus-circle"></i> Ajouter</button>
                        </div>
                    </form>
                <?php else: ?>
                    <p class="text-muted small">Toutes les compétences sont déjà attribuées.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4">
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

        <div class="card">
            <div class="card-header"><i class="bi bi-file-earmark-excel"></i> Planning Excel</div>
            <div class="card-body">
                <p class="text-muted small">
                    Génère un fichier Excel récapitulant les collectes, tournées et services affectés à ce
                    bénévole sur une période, envoyé directement dans son espace.
                </p>
                <form method="POST" action="<?php echo url('/admin/benevoles/' . (int)$b['id'] . '/planning'); ?>" class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small">Du</label>
                        <input type="date" name="date_debut" class="form-control form-control-sm" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Au</label>
                        <input type="date" name="date_fin" class="form-control form-control-sm" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-send"></i> Générer et envoyer
                        </button>
                    </div>
                </form>
                <?php if (!empty($plannings)): ?>
                    <h6 class="small text-muted">Plannings déjà envoyés</h6>
                    <ul class="list-unstyled small mb-0">
                        <?php foreach ($plannings as $p): ?>
                            <li class="mb-1">
                                <?php echo format_date($p['date_debut']); ?> au <?php echo format_date($p['date_fin']); ?>
                                <span class="text-muted">(<?php echo format_datetime($p['date_generation']); ?>)</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
