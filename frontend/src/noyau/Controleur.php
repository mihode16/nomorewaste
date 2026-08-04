<?php

class Controleur
{
    /**
     * Constructeur public (vide) pour permettre l'appel parent::__construct()
     * dans les classes filles (comme DashboardControleur).
     */
    public function __construct()
    {
        // Rien à initialiser pour l'instant
    }

    protected function rendre(string $vue, array $donnees = [], string $layout = 'backoffice'): void
    {
        extract($donnees);

        $fichierVue = __DIR__ . '/../vues/' . $vue . '.php';
        if (!file_exists($fichierVue)) {
            throw new Exception('Vue non trouvée : ' . $vue);
        }

        $layoutFile = __DIR__ . '/../vues/layouts/' . $layout . '.php';
        if ($layout === 'none') {
            require $fichierVue;
            return;
        }
        if (file_exists($layoutFile)) {
            ob_start();
            require $fichierVue;
            $contenu = ob_get_clean();
            require $layoutFile;
            return;
        }

        require_once __DIR__ . '/../vues/layouts/entete.php';
        require $fichierVue;
        require_once __DIR__ . '/../vues/layouts/pied.php';
    }

    protected function rediriger(string $url): void
    {
        if (str_starts_with($url, 'http') || str_starts_with($url, '/nomorewaste')) {
            header('Location: ' . $url);
            exit;
        }
        header('Location: ' . url($url));
        exit;
    }

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

    protected function estPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function estConnecte(): bool
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }

    protected function verifierAuthentification(): void
    {
        if (!$this->estConnecte()) {
            $this->rediriger('/admin');
        }
    }
}