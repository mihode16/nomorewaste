<h1><?php echo __('services_title'); ?></h1>
<p class="lead"><?php echo __('services_intro'); ?></p>

<div class="row mb-5">
    <?php if (empty($services)): ?>
        <div class="col-12"><p class="text-muted">Aucun service disponible pour le moment.</p></div>
    <?php else: ?>
        <?php foreach ($services as $s): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="badge bg-success mb-2"><?php echo htmlspecialchars($s['type'] ?? ''); ?></span>
                        <h5 class="card-title"><?php echo htmlspecialchars($s['nom']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($s['description'] ?? ''); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<h2>Prochains créneaux</h2>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Service</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Places</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($plannings)): ?>
                <tr><td colspan="5" class="text-center">Aucun créneau planifié</td></tr>
            <?php else: ?>
                <?php foreach ($plannings as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['service']['nom'] ?? 'Service #' . $p['service_id']); ?></td>
                        <td><?php echo format_datetime($p['date_heure_debut']); ?></td>
                        <td><?php echo format_datetime($p['date_heure_fin']); ?></td>
                        <td><?php echo (int)$p['capacite_max']; ?></td>
                        <td><?php echo badge_statut($p['statut']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<p class="mt-4">
    <a href="<?php echo url('/adherer'); ?>" class="btn btn-success"><?php echo __('nav_adhesion'); ?></a>
</p>
