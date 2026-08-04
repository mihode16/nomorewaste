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
        $this->verifierAuthentification();

        $commercants = $this->apiClient->get('/api/commercants');
        $collectes = $this->apiClient->get('/api/collectes');
        $produits = $this->apiClient->get('/api/produits');
        $benevoles = $this->apiClient->get('/api/benevoles');
        $tournees = $this->apiClient->get('/api/tournees');
        $expirantes = $this->apiClient->get('/api/commercants/adhesions/expirantes');

        $nbCommercants = ($commercants['code'] === 200 && is_array($commercants['data'])) ? count($commercants['data']) : 0;
        $nbCollectes = ($collectes['code'] === 200 && is_array($collectes['data'])) ? count($collectes['data']) : 0;
        $nbProduits = ($produits['code'] === 200 && is_array($produits['data'])) ? count($produits['data']) : 0;
        $nbBenevoles = ($benevoles['code'] === 200 && is_array($benevoles['data'])) ? count($benevoles['data']) : 0;
        $nbTournees = ($tournees['code'] === 200 && is_array($tournees['data'])) ? count($tournees['data']) : 0;
        $adhesionsExpirantes = ($expirantes['code'] === 200 && is_array($expirantes['data'])) ? $expirantes['data'] : [];

        $this->rendre('backoffice/contenu/dashboard', [
            'titre' => 'Tableau de bord',
            'pageActive' => 'dashboard',
            'nbCommercants' => $nbCommercants,
            'nbCollectes' => $nbCollectes,
            'nbProduits' => $nbProduits,
            'nbBenevoles' => $nbBenevoles,
            'nbTournees' => $nbTournees,
            'adhesionsExpirantes' => $adhesionsExpirantes,
            'user' => $_SESSION['user'] ?? null,
        ]);
    }
}