<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre ?? 'Gestion des bénévoles'); ?></h2>
    <a href="<?php echo url('/admin/benevoles/creer'); ?>" class="btn btn-success">
        <i class="bi bi-person-plus"></i> Inscrire
    </a>
</div>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?> alert-dismissible fade show">
        <?php echo $_SESSION['flash']['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Compétences</th>
                    <th>Statut</th>
                    <th>Candidature</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($benevoles)): ?>
                    <tr><td colspan="6" class="text-center">Aucun bénévole</td></tr>
                <?php else: ?>
                    <?php foreach ($benevoles as $b): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($b['prenom'] . ' ' . $b['nom']); ?></td>
                            <td><?php echo htmlspecialchars($b['email']); ?></td>
                            <td>
                                <?php if (!empty($b['competences'])): ?>
                                    <?php foreach ($b['competences'] as $comp): ?>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($comp['nom']); ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo badge_statut($b['statut_candidature']); ?></td>
                            <td><?php echo format_date($b['date_candidature']); ?></td>
                            <td>
                                <a href="<?php echo url('/admin/benevoles/' . $b['id']); ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>