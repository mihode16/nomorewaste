<?php

class Autoloader
{
    public static function charger(): void
    {
        spl_autoload_register(function ($classe) {
            // Chemins possibles pour les classes
            $chemins = [
                __DIR__ . '/../controleurs/',
                __DIR__ . '/../modeles/',
                __DIR__ . '/../noyau/',
            ];

            foreach ($chemins as $chemin) {
                $fichier = $chemin . $classe . '.php';
                if (file_exists($fichier)) {
                    require_once $fichier;
                    return;
                }
            }
        });
    }
}