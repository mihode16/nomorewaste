<?php

date_default_timezone_set('Europe/Paris');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/nomorewaste/frontend/public');

require_once BASE_PATH . '/noyau/Autoloader.php';
require_once BASE_PATH . '/noyau/helpers.php';
Autoloader::charger();
