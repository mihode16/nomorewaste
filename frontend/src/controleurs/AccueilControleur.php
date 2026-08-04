<?php

require_once __DIR__ . '/../noyau/Controleur.php';

class AccueilControleur extends Controleur
{
    public function index(): void
    {
        $this->rendre('front/contenu/accueil', [
            'titre' => __('home_title'),
        ], 'front');
    }
}
