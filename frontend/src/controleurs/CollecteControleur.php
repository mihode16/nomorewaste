<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

class CollecteControleur extends Controleur
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
        $response = $this->apiClient->get('/api/collectes');
        $collectes = ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];
        $this->rendre('backoffice/collectes/index', [
            'titre' => 'Collectes',
            'pageActive' => 'collectes',
            'collectes' => $collectes,
        ]);
    }

    public function creer(): void
    {
        $this->verifierAuthentification();
        $commercants = $this->apiClient->get('/api/commercants');
        $this->rendre('backoffice/collectes/creer', [
            'titre' => 'Planifier une collecte',
            'pageActive' => 'collectes',
            'commercants' => ($commercants['code'] === 200) ? $commercants['data'] : [],
        ]);
    }

    public function enregistrer(): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/collectes/creer');
        }
        $dt = $this->getParam('date_heure_collecte');
        $data = [
            'date_heure_collecte' => str_replace('T', ' ', $dt) . (strlen($dt) === 16 ? ':00' : ''),
            'adresse_collecte' => $this->getParam('adresse_collecte'),
            'commercant_id' => (int)$this->getParam('commercant_id'),
            'commentaire' => $this->getParam('commentaire', ''),
        ];
        $response = $this->apiClient->post('/api/collectes', $data);
        $_SESSION['flash'] = [
            'type' => ($response['code'] === 201 || $response['code'] === 200) ? 'success' : 'danger',
            'message' => ($response['code'] === 201 || $response['code'] === 200) ? 'Collecte créée' : ('Erreur : ' . ($response['data']['error'] ?? 'échec')),
        ];
        $this->rediriger('/admin/collectes');
    }

    public function valider(int $id): void
    {
        $this->verifierAuthentification();

        if (!$this->estPost()) {
            $this->rediriger('/admin/collectes');
            return;
        }

        $response = $this->apiClient->post('/api/collectes/' . $id . '/valider', []);

        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Collecte validée avec succès'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la validation';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }

        $this->rediriger('/admin/collectes');
    }

    public function modifier(int $id): void
    {
        $this->verifierAuthentification();
        $collecte = $this->apiClient->get('/api/collectes/' . $id);
        $commercants = $this->apiClient->get('/api/commercants');
        if ($collecte['code'] !== 200) {
            $this->rediriger('/admin/collectes');
        }
        $this->rendre('backoffice/collectes/modifier', [
            'titre' => 'Modifier la collecte',
            'pageActive' => 'collectes',
            'collecte' => $collecte['data'],
            'commercants' => ($commercants['code'] === 200) ? $commercants['data'] : [],
        ]);
    }

    public function mettreAJour(int $id): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/collectes/' . $id . '/modifier');
        }
        $dt = $this->getParam('date_heure_collecte');
        $data = [
            'date_heure_collecte' => str_replace('T', ' ', $dt),
            'adresse_collecte' => $this->getParam('adresse_collecte'),
            'commercant_id' => (int)$this->getParam('commercant_id'),
            'commentaire' => $this->getParam('commentaire', ''),
            'statut' => $this->getParam('statut', 'Planifiée'),
        ];
        $response = $this->apiClient->put('/api/collectes/' . $id, $data);
        $_SESSION['flash'] = ['type' => $response['code'] === 200 ? 'success' : 'danger', 'message' => $response['code'] === 200 ? 'Collecte mise à jour' : 'Erreur'];
        $this->rediriger('/admin/collectes');
    }

    public function terminer(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $this->apiClient->post('/api/collectes/' . $id . '/terminer', []);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Collecte terminée'];
        }
        $this->rediriger('/admin/collectes');
    }

    public function supprimer(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $this->apiClient->delete('/api/collectes/' . $id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Collecte supprimée'];
        }
        $this->rediriger('/admin/collectes');
    }
}