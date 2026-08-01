<?php

require_once __DIR__ . '/../noyau/Controleur.php';

class AuthControleur extends Controleur
{
    public function login(): void
    {
        // Page de connexion
        $this->rendre('backoffice/auth/login', [
            'titre' => 'Connexion'
        ]);
    }

    public function authentifier(): void
    {
        // TODO: Implémenter l'authentification
        $_SESSION['user'] = ['id' => 1, 'nom' => 'Admin'];
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Bienvenue !'];
        $this->rediriger('/admin/dashboard');
    }

    public function deconnecter(): void
    {
        session_destroy();
        $this->rediriger('/admin');
    }
}