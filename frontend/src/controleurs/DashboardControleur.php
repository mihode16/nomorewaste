<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

class DashboardControleur extends Controleur
{
    private ClientApi $apiClient;

    public function __construct()
    {
        parent::__construct();
        $this->apiClient = new ClientApi();
    }

    public function index(): void
    {
        // Récupérer quelques statistiques
        $commercants = $this->apiClient->get('/api/commercants');
        $collectes = $this->apiClient->get('/api/collectes');
        
        $this->rendre('backoffice/dashboard', [
            'titre' => 'Tableau de bord',
            'nbCommercants' => isset($commercants['data']) ? count($commercants['data']) : 0,
            'nbCollectes' => isset($collectes['data']) ? count($collectes['data']) : 0
        ]);
    }
}