<?php
/**
 * @var string $titre
 * @var string $pageActive
 * @var array $plannings
 */
?>
<h2 class="mb-4">Mon planning</h2>

<p class="text-muted">
    L'association vous envoie ici votre planning au format Excel, avec le détail de vos collectes,
    tournées et services affectés sur la période couverte.
</p>

<?php if (empty($plannings)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-file-earmark-excel fs-1"></i>
            <p class="mb-0 mt-2">Aucun planning ne vous a encore été envoyé.</p>
        </div>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Période couverte</th>
                        <th>Généré le</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plannings as $p): ?>
                        <tr>
                            <td><?php echo format_date($p['date_debut']); ?> au <?php echo format_date($p['date_fin']); ?></td>
                            <td><?php echo format_datetime($p['date_generation']); ?></td>
                            <td class="text-end">
                                <a href="<?php echo url('/benevole/planning/' . (int)$p['id'] . '/telecharger'); ?>" class="btn btn-sm btn-success">
                                    <i class="bi bi-download"></i> Télécharger
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
