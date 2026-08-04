<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

class TourneeControleur extends Controleur
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
        $response = $this->apiClient->get('/api/tournees');
        $this->rendre('backoffice/tournees/index', [
            'titre' => 'Tournées de distribution',
            'pageActive' => 'tournees',
            'tournees' => ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [],
        ]);
    }

    public function creer(): void
    {
        $this->verifierAuthentification();
        $benevoles = $this->apiClient->get('/api/benevoles/valides');
        $lieux = $this->apiClient->get('/api/lieux-distribution');
        $produits = $this->apiClient->get('/api/produits?statut=Stocké');

        $this->rendre('backoffice/tournees/creer', [
            'titre' => 'Planifier une tournée',
            'pageActive' => 'tournees',
            'benevoles' => ($benevoles['code'] === 200) ? $benevoles['data'] : [],
            'lieux' => ($lieux['code'] === 200) ? $lieux['data'] : [],
            'produits' => ($produits['code'] === 200) ? $produits['data'] : [],
        ]);
    }

    public function enregistrer(): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/tournees/creer');
        }

        $produits = $this->getParam('produits_ids', []);
        if (!is_array($produits)) {
            $produits = [$produits];
        }

        $dt = $this->getParam('date_heure_depart');
        $data = [
            'date_heure_depart' => str_replace('T', ' ', $dt) . (strlen($dt) === 16 ? ':00' : ''),
            'adresse_depart' => $this->getParam('adresse_depart'),
            'benevole_id' => (int)$this->getParam('benevole_id'),
            'lieu_distribution_id' => (int)$this->getParam('lieu_distribution_id'),
            'produits_ids' => array_map('intval', array_filter($produits)),
        ];

        $response = $this->apiClient->post('/api/tournees', $data);
        $_SESSION['flash'] = [
            'type' => ($response['code'] === 201) ? 'success' : 'danger',
            'message' => ($response['code'] === 201) ? 'Tournée créée' : 'Erreur lors de la création',
        ];
        $this->rediriger('/admin/tournees');
    }

    public function detail(int $id): void
    {
        $this->verifierAuthentification();
        $response = $this->apiClient->get('/api/tournees/' . $id);
        if ($response['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Tournée non trouvée'];
            $this->rediriger('/admin/tournees');
        }
        $this->rendre('backoffice/tournees/detail', [
            'titre' => 'Détail tournée #' . $id,
            'pageActive' => 'tournees',
            'tournee' => $response['data'],
        ]);
    }

    public function terminer(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $this->apiClient->post('/api/tournees/' . $id . '/terminer', []);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Tournée terminée'];
        }
        $this->rediriger('/admin/tournees/' . $id);
    }
}
