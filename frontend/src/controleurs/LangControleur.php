<?php

require_once __DIR__ . '/../noyau/Controleur.php';

class LangControleur extends Controleur
{
    public function changer(string $code): void
    {
        $langues = ['fr', 'en', 'es', 'it'];
        if (in_array($code, $langues, true)) {
            $_SESSION['lang'] = $code;
        }
        $retour = $_SERVER['HTTP_REFERER'] ?? url('/');
        header('Location: ' . $retour);
        exit;
    }
}
