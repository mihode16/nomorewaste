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
        
         $nbCommercants = ($commercants['code'] === 200) ? count($commercants['data']) : 0;
        $nbCollectes = ($collectes['code'] === 200) ? count($collectes['data']) : 0;
        
        $this->rendre('backoffice/dashboard', [
            'titre' => 'Tableau de bord',
            'nbCommercants' => $nbCommercants,
            'nbCollectes' => $nbCollectes,
            'user' => $_SESSION['user'] ?? null
        ]);
    }
}