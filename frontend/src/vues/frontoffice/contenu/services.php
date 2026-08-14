<?php
/**
 * @var string $titre
 * @var array $services
 * @var array $plannings
 */
?>
<a href="<?php echo url('/'); ?>" class="text-decoration-none small text-muted d-inline-block mb-3">
    <i class="bi bi-arrow-left"></i> Retour à l'accueil
</a>
<div class="page-hero">
    <i class="bi bi-calendar-heart d-block mb-2"></i>
    <h1 class="mb-2"><?php echo __('services_title'); ?></h1>
    <p class="lead mb-0"><?php echo __('services_intro'); ?></p>
</div>

<h2 class="fw-bold mb-3">Le catalogue</h2>
<div class="row mb-5 g-4">
    <?php if (empty($services)): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-calendar-x fs-1"></i>
                    <p class="mb-0 mt-2">Aucun service disponible pour le moment.</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php
        $icones = [
            'Atelier' => 'bi-tools',
            'Cours' => 'bi-mortarboard',
            'Formation' => 'bi-award',
            'Service' => 'bi-people',
        ];
        ?>
        <?php foreach ($services as $s): ?>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    <div class="card-body">
                        <div class="mb-3 rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center" style="width:56px;height:56px;font-size:1.4rem;">
                            <i class="bi <?php echo $icones[$s['type'] ?? ''] ?? 'bi-star'; ?>"></i>
                        </div>
                        <span class="badge bg-success mb-2"><?php echo htmlspecialchars($s['type'] ?? ''); ?></span>
                        <h5 class="card-title"><?php echo htmlspecialchars($s['nom']); ?></h5>
                        <p class="card-text text-muted small"><?php echo htmlspecialchars($s['description'] ?? ''); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<h2 class="fw-bold mb-3">Prochains créneaux</h2>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Service</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Places</th>
                    <th class="pe-3">Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($plannings)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Aucun créneau planifié pour le moment</td></tr>
                <?php else: ?>
                    <?php foreach ($plannings as $p): ?>
                        <tr>
                            <td class="ps-3"><?php echo htmlspecialchars($p['service']['nom'] ?? 'Service #' . $p['service_id']); ?></td>
                            <td><?php echo format_datetime($p['date_heure_debut']); ?></td>
                            <td><?php echo format_datetime($p['date_heure_fin']); ?></td>
                            <td><?php echo (int)$p['capacite_max']; ?></td>
                            <td class="pe-3"><?php echo badge_statut($p['statut']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card border-0 shadow-sm bg-success bg-opacity-10">
    <div class="card-body text-center py-4">
        <h5 class="mb-2">Envie de profiter de ces services ?</h5>
        <p class="text-muted mb-3">Ils sont réservés à nos adhérents. L'inscription ne prend que quelques minutes.</p>
        <a href="<?php echo url('/adherer'); ?>" class="btn btn-success btn-lg"><?php echo __('nav_adhesion'); ?></a>
    </div>
</div>
