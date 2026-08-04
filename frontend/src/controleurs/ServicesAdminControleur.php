<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

class ServicesAdminControleur extends Controleur
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
        $services = $this->apiClient->get('/api/services');
        $plannings = $this->apiClient->get('/api/service-plannings');

        $this->rendre('backoffice/services/index', [
            'titre' => 'Gestion des services',
            'pageActive' => 'services',
            'services' => ($services['code'] === 200 && is_array($services['data'])) ? $services['data'] : [],
            'plannings' => ($plannings['code'] === 200 && is_array($plannings['data'])) ? $plannings['data'] : [],
        ]);
    }

    public function creerPlanning(): void
    {
        $this->verifierAuthentification();
        $services = $this->apiClient->get('/api/services');
        $benevoles = $this->apiClient->get('/api/benevoles/valides');

        $this->rendre('backoffice/services/plannings/creer', [
            'titre' => 'Nouveau planning',
            'pageActive' => 'services',
            'services' => ($services['code'] === 200) ? $services['data'] : [],
            'benevoles' => ($benevoles['code'] === 200) ? $benevoles['data'] : [],
        ]);
    }

    public function enregistrerPlanning(): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/services/plannings/creer');
        }

        $debut = $this->getParam('date_heure_debut');
        $fin = $this->getParam('date_heure_fin');
        $data = [
            'date_heure_debut' => str_replace('T', ' ', $debut) . (strlen($debut) === 16 ? ':00' : ''),
            'date_heure_fin' => str_replace('T', ' ', $fin) . (strlen($fin) === 16 ? ':00' : ''),
            'capacite_max' => (int)$this->getParam('capacite_max', 10),
            'service_id' => (int)$this->getParam('service_id'),
            'benevole_id' => (int)$this->getParam('benevole_id'),
        ];

        $response = $this->apiClient->post('/api/service-plannings', $data);
        $_SESSION['flash'] = [
            'type' => ($response['code'] === 201) ? 'success' : 'danger',
            'message' => ($response['code'] === 201) ? 'Planning créé' : 'Erreur lors de la création',
        ];
        $this->rediriger('/admin/services');
    }
}
