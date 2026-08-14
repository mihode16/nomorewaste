<?php
/**
 * @var string $titre
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Contacter l'association</h2>
    <a href="<?php echo url('/benevole/messages'); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Mes messages
    </a>
</div>

<div class="card border-0 shadow-sm" style="max-width: 650px;">
    <div class="card-body">
        <p class="text-muted">
            Une question, une disponibilité à signaler, un empêchement pour une affectation ? Envoyez un
            message à l'équipe NO MORE WASTE, elle vous répondra dans les meilleurs délais.
        </p>
        <form method="POST" action="<?php echo url('/benevole/messages'); ?>">
            <div class="mb-3">
                <label class="form-label">Sujet *</label>
                <input type="text" name="sujet" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Message *</label>
                <textarea name="contenu" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> Envoyer le message</button>
        </form>
    </div>
</div>
