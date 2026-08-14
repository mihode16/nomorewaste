<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

class ServicesPublicControleur extends Controleur
{
    private ClientApi $apiClient;

    public function __construct()
    {
        parent::__construct();
        $this->apiClient = new ClientApi();
    }

    public function index(): void
    {
        $services = $this->apiClient->get('/api/services');
        $plannings = $this->apiClient->get('/api/service-plannings');

        $this->rendre('frontoffice/contenu/services', [
            'titre' => __('services_title'),
            'services' => ($services['code'] === 200 && is_array($services['data'])) ? $services['data'] : [],
            'plannings' => ($plannings['code'] === 200 && is_array($plannings['data'])) ? $plannings['data'] : [],
        ], 'front');
    }
}
