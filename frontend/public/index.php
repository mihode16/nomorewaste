<?php
// Point d'entrée principal

// Démarrer la session
session_start();

// Définir le chemin de base
define('BASE_PATH', dirname(__DIR__));

// Définir le chemin de base pour les URLs (si sous-dossier)
define('BASE_URL', '/nomorewaste');

// Charger l'autoloader
require_once BASE_PATH . '../src/noyau/Autoloader.php';
Autoloader::charger();

// Charger le routeur
$routeur = new Routeur();
$routeur->executer();