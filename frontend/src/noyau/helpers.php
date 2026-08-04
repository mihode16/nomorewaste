<?php

function url(string $chemin = ''): string
{
    $base = defined('BASE_URL') ? BASE_URL : '/nomorewaste/frontend/public';
    if ($chemin === '' || $chemin === '/') {
        return rtrim($base, '/') . '/';
    }
    return rtrim($base, '/') . '/' . ltrim($chemin, '/');
}

function lang(): string
{
    return $_SESSION['lang'] ?? 'fr';
}

function lang_url(string $code): string
{
    return url('/lang/' . $code);
}

function __(string $key): string
{
    static $traductions = null;
    if ($traductions === null) {
        $traductions = [
            'fr' => [
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
                'adhesion_success' => 'Votre adhésion a été enregistrée avec succès !',
                'adhesion_error' => 'Erreur lors de l\'inscription.',
                'nav_home' => 'Accueil',
                'nav_services' => 'Services',
                'nav_adhesion' => 'Adhérer',
                'nav_admin' => 'Administration',
                'btn_submit' => 'Envoyer',
                'btn_register' => 'S\'inscrire',
            ],
            'en' => [
                'home_title' => 'Home — NO MORE WASTE',
                'home_hero' => 'Together against food waste',
                'home_intro' => 'NO MORE WASTE collects unsold commercial products and redistributes them to charities and people in need.',
                'home_mission' => 'Our mission',
                'home_mission_text' => 'Collect, store and redistribute food products to fight against waste.',
                'home_agencies' => 'Our agencies',
                'services_title' => 'Our services',
                'services_intro' => 'Discover the services offered to association members.',
                'adhesion_title' => 'Become a member',
                'adhesion_intro' => 'Join NO MORE WASTE and access all our services.',
                'adhesion_success' => 'Your membership has been successfully registered!',
                'adhesion_error' => 'Registration error.',
                'nav_home' => 'Home',
                'nav_services' => 'Services',
                'nav_adhesion' => 'Join',
                'nav_admin' => 'Administration',
                'btn_submit' => 'Submit',
                'btn_register' => 'Register',
            ],
            'es' => [
                'home_title' => 'Inicio — NO MORE WASTE',
                'home_hero' => 'Juntos contra el desperdicio alimentario',
                'home_intro' => 'NO MORE WASTE recoge productos comerciales no vendidos y los redistribuye a asociaciones benéficas.',
                'home_mission' => 'Nuestra misión',
                'home_mission_text' => 'Recoger, almacenar y redistribuir productos alimentarios para luchar contra el desperdicio.',
                'home_agencies' => 'Nuestras agencias',
                'services_title' => 'Nuestros servicios',
                'services_intro' => 'Descubra los servicios ofrecidos a los miembros de la asociación.',
                'adhesion_title' => 'Hacerse miembro',
                'adhesion_intro' => 'Únase a NO MORE WASTE y acceda a todos nuestros servicios.',
                'adhesion_success' => '¡Su inscripción se ha registrado con éxito!',
                'adhesion_error' => 'Error de inscripción.',
                'nav_home' => 'Inicio',
                'nav_services' => 'Servicios',
                'nav_adhesion' => 'Unirse',
                'nav_admin' => 'Administración',
                'btn_submit' => 'Enviar',
                'btn_register' => 'Inscribirse',
            ],
            'it' => [
                'home_title' => 'Home — NO MORE WASTE',
                'home_hero' => 'Insieme contro lo spreco alimentare',
                'home_intro' => 'NO MORE WASTE raccoglie prodotti commerciali invenduti e li ridistribuisce alle associazioni di beneficenza.',
                'home_mission' => 'La nostra missione',
                'home_mission_text' => 'Raccogliere, immagazzinare e ridistribuire prodotti alimentari per combattere lo spreco.',
                'home_agencies' => 'Le nostre agenzie',
                'services_title' => 'I nostri servizi',
                'services_intro' => 'Scopri i servizi offerti ai membri dell\'associazione.',
                'adhesion_title' => 'Diventa membro',
                'adhesion_intro' => 'Unisciti a NO MORE WASTE e accedi a tutti i nostri servizi.',
                'adhesion_success' => 'La tua iscrizione è stata registrata con successo!',
                'adhesion_error' => 'Errore di iscrizione.',
                'nav_home' => 'Home',
                'nav_services' => 'Servizi',
                'nav_adhesion' => 'Iscriviti',
                'nav_admin' => 'Amministrazione',
                'btn_submit' => 'Invia',
                'btn_register' => 'Registrati',
            ],
        ];
    }
    $l = lang();
    return $traductions[$l][$key] ?? ($traductions['fr'][$key] ?? $key);
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
