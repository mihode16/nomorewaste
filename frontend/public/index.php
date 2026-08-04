<?php

require_once dirname(__DIR__) . '/src/noyau/app.php';

$routeur = new Routeur();
$routeur->executer();
