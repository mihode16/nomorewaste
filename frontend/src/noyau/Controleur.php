<?php

class Controleur
{
    /**
     * Rendre une vue avec des données
     * @param string $vue Chemin de la vue (ex: 'backoffice/commercants/index')
     * @param array $donnees Données à passer à la vue
     */
    protected function rendre(string $vue, array $donnees = []): void
    {
        // Extraire les données pour les rendre disponibles dans la vue
        extract($donnees);
        
        // Définir le chemin complet de la vue
        $fichierVue = __DIR__ . '/../vues/' . $vue . '.php';
        
        if (file_exists($fichierVue)) {
            // Inclure l'entête
            require_once __DIR__ . '/../vues/layouts/entete.php';
            // Inclure le contenu de la vue
            require_once $fichierVue;
            // Inclure le pied de page
            require_once __DIR__ . '/../vues/layouts/pied.php';
        } else {
            throw new Exception("Vue non trouvée : " . $vue);
        }
    }
    
    /**
     * Rediriger vers une URL
     * @param string $url URL de destination
     */
    protected function rediriger(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Récupérer une donnée POST ou GET
     * @param string $nom Nom du champ
     * @param mixed $defaut Valeur par défaut
     * @return mixed
     */
    protected function getParam(string $nom, $defaut = null)
    {
        if (isset($_POST[$nom])) {
            return $_POST[$nom];
        }
        if (isset($_GET[$nom])) {
            return $_GET[$nom];
        }
        return $defaut;
    }
    
    /**
     * Vérifier si la requête est POST
     */
    protected function estPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Vérifier si l'utilisateur est connecté
     */
    protected function estConnecte(): bool
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }
    
    /**
     * Vérifier que l'utilisateur est connecté, sinon rediriger
     */
    protected function verifierAuthentification(): void
    {
        if (!$this->estConnecte()) {
            $this->rediriger('/admin');
        }
    }
}