<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

class AdhesionControleur extends Controleur
{
    private ClientApi $apiClient;

    public function __construct()
    {
        parent::__construct();
        $this->apiClient = new ClientApi();
    }

    public function index(): void
    {
        $this->rendre('front/contenu/adhesion', [
            'titre' => __('adhesion_title'),
        ], 'front');
    }

    public function enregistrer(): void
    {
        if (!$this->estPost()) {
            $this->rediriger('/adherer');
        }

        $data = [
            'email' => $this->getParam('email'),
            'mot_de_passe' => $this->getParam('mot_de_passe'),
            'nom' => $this->getParam('nom'),
            'prenom' => $this->getParam('prenom'),
            'telephone' => $this->getParam('telephone', ''),
            'adresse' => $this->getParam('adresse', ''),
            'date_debut_adhesion' => $this->getParam('date_debut_adhesion', date('Y-m-d')),
            'date_fin_adhesion' => $this->getParam('date_fin_adhesion', date('Y-m-d', strtotime('+1 year'))),
        ];

        $response = $this->apiClient->post('/api/adherents', $data);

        if ($response['code'] === 201 || $response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => __('adhesion_success')];
        } else {
            $erreur = $response['data']['error'] ?? __('adhesion_error');
            if (is_string($response['data']) && $response['data'] !== '') {
                $erreur = $response['data'];
            }
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $erreur];
        }

        $this->rediriger('/adherer');
    }
}
