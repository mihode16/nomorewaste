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
            $this->rediriger($this->espacePourRole($_SESSION['user']['type_utilisateur'] ?? ''));
            return;
        }
        $this->rendre('contenu/auth_login', [
            'titre' => 'Connexion',
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

        if ($response['code'] === 200 && !empty($response['data']) && empty($response['data']['error'])) {
            $_SESSION['user'] = $response['data'];
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Bienvenue !'];
            $this->rediriger($this->espacePourRole($response['data']['type_utilisateur'] ?? ''));
            return;
        }

        $message = $response['data']['error'] ?? 'Identifiants incorrects';
        $_SESSION['flash'] = ['type' => 'danger', 'message' => $message];
        $this->rediriger('/connexion');
    }

    public function deconnecter(): void
    {
        session_destroy();
        session_start();
        $this->rediriger('/connexion');
    }
}
