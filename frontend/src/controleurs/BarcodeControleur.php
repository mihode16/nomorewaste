<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/BarcodeGenerator.php';

class BarcodeControleur extends Controleur
{
    public function generer(string $code): void
    {
        try {
            // Nettoyer le code
            $code = preg_replace('/[^0-9]/', '', $code);
            
            // Valider le code (doit faire 13 chiffres ou 12 + on ajoute la clé)
            if (strlen($code) === 12) {
                $generator = new BarcodeGenerator();
                $code = $generator->appendEAN13CheckDigit($code);
            }
            
            if (strlen($code) !== 13) {
                throw new \Exception('Code EAN-13 invalide');
            }

            $generator = new BarcodeGenerator();
            $imageData = $generator->generateEAN13($code, 2, 80);

            header('Content-Type: image/svg+xml');
            header('Content-Length: ' . strlen($imageData));
            header('Cache-Control: public, max-age=31536000');
            echo $imageData;
            exit;
        } catch (\Exception $e) {
            http_response_code(400);
            echo 'Code-barres invalide';
            exit;
        }
    }
}