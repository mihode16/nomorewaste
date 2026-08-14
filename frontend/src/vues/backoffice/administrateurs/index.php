<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $administrateurs
 * @var int $moiId
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo htmlspecialchars($titre ?? 'Comptes administrateurs'); ?></h2>
    <a href="<?php echo url('/admin/administrateurs/creer'); ?>" class="btn btn-success btn-sm">
        <i class="bi bi-plus-circle"></i> Nouveau compte
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
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Inscrit le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($administrateurs)): ?>
                    <tr><td colspan="6" class="text-center">Aucun compte administrateur</td></tr>
                <?php else: ?>
                    <?php foreach ($administrateurs as $ad): ?>
                        <?php $estMoi = (int)$ad['id'] === (int)$moiId; ?>
                        <tr>
                            <td><?php echo (int)$ad['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($ad['prenom'] . ' ' . $ad['nom']); ?>
                                <?php if ($estMoi): ?><span class="badge bg-primary">Vous</span><?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($ad['email']); ?></td>
                            <td><?php echo htmlspecialchars($ad['telephone'] ?? '') ?: '—'; ?></td>
                            <td><?php echo format_date($ad['date_inscription']); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?php echo url('/admin/administrateurs/' . $ad['id'] . '/modifier'); ?>" class="btn btn-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if (!$estMoi): ?>
                                        <form method="POST" action="<?php echo url('/admin/administrateurs/' . $ad['id'] . '/supprimer'); ?>" class="d-inline" onsubmit="return confirm('Supprimer ce compte administrateur ?');">
                                            <button type="submit" class="btn btn-danger" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-danger" disabled title="Vous ne pouvez pas supprimer votre propre compte">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
