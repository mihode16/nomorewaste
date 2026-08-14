<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $disponibilites
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Mes disponibilités</h2>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ajouterModal">
        <i class="bi bi-plus-circle"></i> Ajouter un créneau
    </button>
</div>

<p class="text-muted">
    Indiquez vos disponibilités pour la semaine courante et la semaine prochaine. L'association s'en sert
    pour planifier vos affectations (collectes, tournées, services) et les visualise sur son calendrier des bénévoles.
</p>

<?php if (empty($disponibilites)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-calendar-x fs-1"></i>
            <p class="mb-0 mt-2">Aucun créneau de disponibilité renseigné.</p>
        </div>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Jour</th>
                        <th>Date</th>
                        <th>De</th>
                        <th>À</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($disponibilites as $d): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($d['jour_semaine']); ?></td>
                            <td><?php echo !empty($d['date']) ? format_date($d['date']) : '—'; ?></td>
                            <td><?php echo substr($d['heure_debut'], 0, 5); ?></td>
                            <td><?php echo substr($d['heure_fin'], 0, 5); ?></td>
                            <td class="text-end">
                                <form method="POST" action="<?php echo url('/benevole/disponibilites/' . (int)$d['id'] . '/supprimer'); ?>" onsubmit="return confirm('Supprimer ce créneau ?');">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<div class="modal fade" id="ajouterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo url('/benevole/disponibilites'); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un créneau de disponibilité</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Semaine</label>
                        <select name="semaine" class="form-select" required>
                            <option value="courante">Cette semaine</option>
                            <option value="prochaine">Semaine prochaine</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jour</label>
                        <select name="jour_semaine" class="form-select" required>
                            <option value="Lundi">Lundi</option>
                            <option value="Mardi">Mardi</option>
                            <option value="Mercredi">Mercredi</option>
                            <option value="Jeudi">Jeudi</option>
                            <option value="Vendredi">Vendredi</option>
                            <option value="Samedi">Samedi</option>
                            <option value="Dimanche">Dimanche</option>
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">De</label>
                            <input type="time" name="heure_debut" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">À</label>
                            <input type="time" name="heure_fin" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>
