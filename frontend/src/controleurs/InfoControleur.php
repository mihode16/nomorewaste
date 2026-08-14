<?php

require_once __DIR__ . '/../noyau/Controleur.php';

/** Pages publiques d'information : mentions légales, équipe. */
class InfoControleur extends Controleur
{
    public function mentionsLegales(): void
    {
        $this->rendre('frontoffice/contenu/mentions_legales', [
            'titre' => 'Mentions légales',
        ], 'front');
    }

    public function equipe(): void
    {
        $this->rendre('frontoffice/contenu/equipe', [
            'titre' => 'Notre équipe',
        ], 'front');
    }
}
