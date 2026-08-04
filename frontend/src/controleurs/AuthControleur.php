<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

class AuthControleur extends Controleur
{
    private ClientApi $apiClient;

    public function __construct()
    {
        parent::__construct();
        $this->apiClient = new ClientApi();
    }

    public function login(): void
    {
        if ($this->estConnecte()) {
            $this->rediriger('/admin/dashboard');
            return;
        }
        $this->rendre('backoffice/contenu/auth_login', [
            'titre' => 'Connexion administration',
        ], 'none');
    }

    public function authentifier(): void
    {
        $email = $this->getParam('email');
        $motDePasse = $this->getParam('mot_de_passe');

        $response = $this->apiClient->post('/api/auth/login', [
            'email' => $email,
            'mot_de_passe' => $motDePasse,
        ]);

        if ($response['code'] === 200 && !empty($response['data'])) {
            $_SESSION['user'] = $response['data'];
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Bienvenue !'];
            $this->rediriger('/admin/dashboard');
        }

        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Identifiants incorrects'];
        $this->rediriger('/admin');
    }

    public function deconnecter(): void
    {
        session_destroy();
        session_start();
        $this->rediriger('/admin');
    }
}
