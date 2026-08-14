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

    /**
     * Vérifie que l'utilisateur est connecté ET a un profil autorisé pour ce contrôleur.
     * Par défaut restreint aux comptes 'responsable' (comportement historique du
     * backoffice) : tout contrôleur qui n'a pas explicitement besoin d'ouvrir l'accès
     * à un autre profil reste protégé sans rien avoir à changer. Les espaces dédiés
     * (ex: commerçant) passent explicitement leur(s) rôle(s) autorisé(s).
     */
    protected function verifierAuthentification(array $rolesAutorises = ['responsable']): void
    {
        if (!$this->estConnecte()) {
            $this->rediriger('/connexion');
            return;
        }
        $role = $_SESSION['user']['type_utilisateur'] ?? '';
        if (!empty($rolesAutorises) && !in_array($role, $rolesAutorises, true)) {
            $this->rediriger($this->espacePourRole($role));
        }
    }

    protected function espacePourRole(string $role): string
    {
        switch ($role) {
            case 'commercant':
                return '/commercant/dashboard';
            case 'adherent':
                return '/adherent/dashboard';
            case 'benevole':
                return '/benevole/dashboard';
            case 'responsable':
                return '/admin/dashboard';
            default:
                return '/connexion';
        }
    }
}