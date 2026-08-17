<?php

date_default_timezone_set('Europe/Paris');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_PATH', dirname(__DIR__));
// En local (MAMP), le site vit sous /nomorewaste/frontend/public. En conteneur Docker, le
// DocumentRoot pointe directement sur public/ et le site est servi à la racine du domaine :
// BASE_URL doit alors être vide (voir docker-compose.yml).
define('BASE_URL', getenv('BASE_URL') !== false ? getenv('BASE_URL') : '/nomorewaste/frontend/public');

require_once BASE_PATH . '/noyau/Autoloader.php';
require_once BASE_PATH . '/noyau/helpers.php';
Autoloader::charger();
