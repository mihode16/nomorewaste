<?php

function url(string $chemin = ''): string
{
    $base = defined('BASE_URL') ? BASE_URL : '/nomorewaste/frontend/public';
    if ($chemin === '' || $chemin === '/') {
        return rtrim($base, '/') . '/';
    }
    return rtrim($base, '/') . '/' . ltrim($chemin, '/');
}

function api_url(string $chemin = ''): string
{
    $base = getenv('API_URL') ?: 'http://localhost:8081';
    return rtrim($base, '/') . '/' . ltrim($chemin, '/');
}

function lang(): string
{
    return 'fr';
}

function __(string $key): string
{
    static $traductions = [
        'home_title' => 'Accueil — NO MORE WASTE',
        'home_hero' => 'Ensemble contre le gaspillage alimentaire',
        'home_intro' => 'NO MORE WASTE récolte les invendus commerciaux et les redistribue aux associations caritatives et aux personnes en détresse.',
        'home_mission' => 'Notre mission',
        'home_mission_text' => 'Collecter, stocker et redistribuer les produits alimentaires pour lutter contre le gaspillage.',
        'home_agencies' => 'Nos agences',
        'services_title' => 'Nos services',
        'services_intro' => 'Découvrez les services proposés aux adhérents de l\'association.',
        'adhesion_title' => 'Devenir adhérent',
        'adhesion_intro' => 'Rejoignez NO MORE WASTE et accédez à tous nos services.',
        'adhesion_success' => 'Votre demande d\'adhésion a bien été envoyée ! Elle sera étudiée par nos administrateurs avant validation de votre compte.',
        'adhesion_error' => 'Erreur lors de l\'inscription.',
        'nav_home' => 'Accueil',
        'nav_services' => 'Services',
        'nav_adhesion' => 'Adhérer',
        'nav_admin' => 'Connexion',
        'btn_submit' => 'Envoyer',
        'btn_register' => 'S\'inscrire',
    ];
    return $traductions[$key] ?? $key;
}

function format_date(?string $date, string $format = 'd/m/Y'): string
{
    if (empty($date)) {
        return '-';
    }
    $ts = strtotime($date);
    return $ts ? date($format, $ts) : '-';
}

function format_datetime(?string $date, string $format = 'd/m/Y H:i'): string
{
    if (empty($date)) {
        return '-';
    }
    $ts = strtotime($date);
    return $ts ? date($format, $ts) : '-';
}

/** Une adhésion est valide si elle a été validée par un admin ET n'est pas expirée. */
function adhesion_est_valide(?array $entite): bool
{
    if (!$entite || ($entite['statut_adhesion'] ?? '') !== 'valide') {
        return false;
    }
    return strtotime($entite['date_fin_adhesion'] ?? '') >= strtotime('today');
}

/** Nom affiché pour un admin auteur d'un message : "Admin " + initiale du prénom + nom. */
function nom_affichage_admin(string $prenom, string $nom): string
{
    $initiale = $prenom !== '' ? mb_strtoupper(mb_substr($prenom, 0, 1)) : '';
    return trim('Admin ' . $initiale . ' ' . $nom);
}

/**
 * Nom affiché pour un participant de conversation (initiateur, destinataire ou auteur d'un
 * message), quel que soit son profil : admin, commerçant (raison sociale) ou adhérent (nom complet).
 */
function nom_affichage_participant(?array $p): string
{
    if (!$p) {
        return '';
    }
    if (($p['type_utilisateur'] ?? '') === 'responsable') {
        return nom_affichage_admin($p['prenom'] ?? '', $p['nom'] ?? '');
    }
    if (($p['type_utilisateur'] ?? '') === 'commercant' && !empty($p['raison_sociale'])) {
        return $p['raison_sociale'];
    }
    return trim(($p['prenom'] ?? '') . ' ' . ($p['nom'] ?? ''));
}

/** Icône Bootstrap représentant le profil d'un participant de conversation. */
function icone_type_participant(?array $p): string
{
    switch ($p['type_utilisateur'] ?? '') {
        case 'commercant':
            return 'bi-shop';
        case 'benevole':
            return 'bi-person-heart';
        default:
            return 'bi-person-badge';
    }
}

function badge_statut(string $statut): string
{
    $classes = [
        'Planifiée' => 'bg-info',
        'Terminée' => 'bg-success',
        'Prévue' => 'bg-primary',
        'Stocké' => 'bg-success',
        'En tournée' => 'bg-warning text-dark',
        'Distribué' => 'bg-secondary',
        'Validé' => 'bg-success',
        'En attente' => 'bg-warning text-dark',
        'Refusé' => 'bg-danger',
        'Ouvert' => 'bg-success',
    ];
    $class = $classes[$statut] ?? 'bg-secondary';
    return '<span class="badge ' . $class . '">' . htmlspecialchars($statut) . '</span>';
}

/**
 * Calcule le statut d'affichage d'une adhésion (adhérent ou commerçant) à partir de son
 * statut de validation et de sa date de fin. Une adhésion validée bascule automatiquement
 * en "Expire bientôt" dès que sa date de fin tombe à 1 mois ou moins de la date du jour.
 */
function statut_adhesion_calcule(array $item): array
{
    $statutBase = $item['statut_adhesion'] ?? 'en_attente';
    if ($statutBase !== 'valide') {
        return ['label' => 'En attente', 'classe' => 'bg-warning text-dark'];
    }

    $fin = strtotime($item['date_fin_adhesion']);
    $now = time();
    $seuil = strtotime('+1 month', $now);
    $aDemande = !empty($item['demande_renouvellement']);

    // Une demande de renouvellement en attente prime sur tout le reste — y compris sur une
    // adhésion déjà expirée : dès que le commerçant/adhérent l'a demandé, l'admin doit le
    // voir immédiatement, sans attendre que l'adhésion approche de sa date de fin.
    if ($aDemande) {
        return ['label' => 'À renouveler', 'classe' => 'bg-info text-dark'];
    }
    if ($fin < $now) {
        return ['label' => 'Expirée', 'classe' => 'bg-danger'];
    }
    if ($fin <= $seuil) {
        return ['label' => 'Expire bientôt', 'classe' => 'bg-warning text-dark'];
    }
    return ['label' => 'Active', 'classe' => 'bg-success'];
}
