<?php
// Point d'entrée principal
require_once __DIR__ . '../src/noyau/Routeur.php';

$routeur = new Routeur();
$routeur->executer();