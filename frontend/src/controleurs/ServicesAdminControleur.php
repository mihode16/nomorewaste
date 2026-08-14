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

    public function creerService(): void
    {
        $this->verifierAuthentification();
        $this->rendre('backoffice/services/creer', [
            'titre' => 'Nouveau service',
            'pageActive' => 'services',
        ]);
    }

    public function enregistrerService(): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/services/creer');
            return;
        }
        $data = [
            'nom' => $this->getParam('nom'),
            'description' => $this->getParam('description', ''),
            'type' => $this->getParam('type', ''),
            'nouvelle_competence' => $this->getParam('nouvelle_competence', ''),
            'description_competence' => $this->getParam('description_competence', ''),
        ];
        $response = $this->apiClient->post('/api/services', $data);
        $_SESSION['flash'] = [
            'type' => ($response['code'] === 201) ? 'success' : 'danger',
            'message' => ($response['code'] === 201) ? 'Service créé' : 'Erreur lors de la création',
        ];
        $this->rediriger('/admin/services');
    }

    public function modifierService(int $id): void
    {
        $this->verifierAuthentification();
        $service = $this->apiClient->get('/api/services/' . $id);
        if ($service['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Service non trouvé'];
            $this->rediriger('/admin/services');
            return;
        }
        $this->rendre('backoffice/services/modifier', [
            'titre' => 'Modifier le service',
            'pageActive' => 'services',
            'service' => $service['data'],
        ]);
    }

    public function mettreAJourService(int $id): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/services/' . $id . '/modifier');
            return;
        }
        $data = [
            'nom' => $this->getParam('nom'),
            'description' => $this->getParam('description', ''),
            'type' => $this->getParam('type', ''),
        ];
        $response = $this->apiClient->put('/api/services/' . $id, $data);
        $_SESSION['flash'] = [
            'type' => ($response['code'] === 200) ? 'success' : 'danger',
            'message' => ($response['code'] === 200) ? 'Service mis à jour' : 'Erreur lors de la mise à jour',
        ];
        $this->rediriger('/admin/services');
    }

    public function supprimerService(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $this->apiClient->delete('/api/services/' . $id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Service supprimé'];
        }
        $this->rediriger('/admin/services');
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

    public function detailPlanning(int $id): void
    {
        $this->verifierAuthentification();
        $planning = $this->apiClient->get('/api/service-plannings/' . $id);
        if ($planning['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Planning non trouvé'];
            $this->rediriger('/admin/services');
            return;
        }
        $this->rendre('backoffice/services/plannings/detail', [
            'titre' => 'Détail du planning',
            'pageActive' => 'services',
            'planning' => $planning['data'],
        ]);
    }

    public function modifierPlanning(int $id): void
    {
        $this->verifierAuthentification();
        $planning = $this->apiClient->get('/api/service-plannings/' . $id);
        if ($planning['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Planning non trouvé'];
            $this->rediriger('/admin/services');
            return;
        }
        $services = $this->apiClient->get('/api/services');
        $benevoles = $this->apiClient->get('/api/benevoles/valides');

        $this->rendre('backoffice/services/plannings/modifier', [
            'titre' => 'Modifier le planning',
            'pageActive' => 'services',
            'planning' => $planning['data'],
            'services' => ($services['code'] === 200) ? $services['data'] : [],
            'benevoles' => ($benevoles['code'] === 200) ? $benevoles['data'] : [],
        ]);
    }

    public function mettreAJourPlanning(int $id): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/services/plannings/' . $id . '/modifier');
            return;
        }

        $debut = $this->getParam('date_heure_debut');
        $fin = $this->getParam('date_heure_fin');
        $data = [
            'date_heure_debut' => str_replace('T', ' ', $debut) . (strlen($debut) === 16 ? ':00' : ''),
            'date_heure_fin' => str_replace('T', ' ', $fin) . (strlen($fin) === 16 ? ':00' : ''),
            'capacite_max' => (int)$this->getParam('capacite_max', 10),
            'service_id' => (int)$this->getParam('service_id'),
            'benevole_id' => (int)$this->getParam('benevole_id'),
            'statut' => $this->getParam('statut', 'Ouvert'),
        ];

        $response = $this->apiClient->put('/api/service-plannings/' . $id, $data);
        $_SESSION['flash'] = [
            'type' => ($response['code'] === 200) ? 'success' : 'danger',
            'message' => ($response['code'] === 200) ? 'Planning mis à jour' : 'Erreur lors de la mise à jour',
        ];
        $this->rediriger('/admin/services/plannings/' . $id);
    }

    public function supprimerPlanning(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $this->apiClient->delete('/api/service-plannings/' . $id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Planning supprimé'];
        }
        $this->rediriger('/admin/services');
    }
}
