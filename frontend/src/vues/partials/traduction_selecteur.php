<?php
/**
 * Sélecteur de langue (fr/en/it/pt/ga), à inclure dans la barre de navigation de chaque layout.
 * Définir $traductionSelecteurClasse avant l'include pour adapter la couleur du bouton au fond
 * de la barre de navigation (ex. 'btn-outline-light' sur fond coloré/sombre) ; par défaut
 * 'btn-outline-secondary', adapté à une barre de navigation claire.
 */
$traductionSelecteurClasse = $traductionSelecteurClasse ?? 'btn-outline-secondary';
?>
<div class="dropdown">
    <button class="btn <?php echo $traductionSelecteurClasse; ?> btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <i class="bi bi-globe2"></i> <span class="langue-actuelle">FR</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="#" onclick="changerLangueSite('fr'); return false;">🇫🇷 Français</a></li>
        <li><a class="dropdown-item" href="#" onclick="changerLangueSite('en'); return false;">🇬🇧 English</a></li>
        <li><a class="dropdown-item" href="#" onclick="changerLangueSite('it'); return false;">🇮🇹 Italiano</a></li>
        <li><a class="dropdown-item" href="#" onclick="changerLangueSite('pt'); return false;">🇵🇹 Português</a></li>
        <li><a class="dropdown-item" href="#" onclick="changerLangueSite('ga'); return false;">🇮🇪 Gaeilge</a></li>
    </ul>
</div>
