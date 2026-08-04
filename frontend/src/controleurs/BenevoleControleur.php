<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

class BenevoleControleur extends Controleur
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
        $response = $this->apiClient->get('/api/benevoles');
        $this->rendre('backoffice/benevoles/index', [
            'titre' => 'Gestion des bénévoles',
            'pageActive' => 'benevoles',
            'benevoles' => ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [],
        ]);
    }

    public function creer(): void
    {
        $this->verifierAuthentification();
        $competences = $this->apiClient->get('/api/competences');
        $this->rendre('backoffice/benevoles/creer', [
            'titre' => 'Nouveau bénévole',
            'pageActive' => 'benevoles',
            'competences' => ($competences['code'] === 200) ? $competences['data'] : [],
        ]);
    }

    public function enregistrer(): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/benevoles/creer');
        }

        $competences = $this->getParam('competences', []);
        if (!is_array($competences)) {
            $competences = [$competences];
        }
        $competences = array_map('intval', array_filter($competences));

        $data = [
            'email' => $this->getParam('email'),
            'mot_de_passe' => $this->getParam('mot_de_passe'),
            'nom' => $this->getParam('nom'),
            'prenom' => $this->getParam('prenom'),
            'telephone' => $this->getParam('telephone', ''),
            'adresse' => $this->getParam('adresse', ''),
            'competences' => $competences,
        ];

        $response = $this->apiClient->post('/api/benevoles', $data);
        $_SESSION['flash'] = [
            'type' => ($response['code'] === 201) ? 'success' : 'danger',
            'message' => ($response['code'] === 201) ? 'Bénévole inscrit avec succès' : 'Erreur lors de l\'inscription',
        ];
        $this->rediriger('/admin/benevoles');
    }

    public function detail(int $id): void
    {
        $this->verifierAuthentification();
        $response = $this->apiClient->get('/api/benevoles/' . $id);
        if ($response['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Bénévole non trouvé'];
            $this->rediriger('/admin/benevoles');
        }
        $this->rendre('backoffice/benevoles/detail', [
            'titre' => 'Détail bénévole',
            'pageActive' => 'benevoles',
            'benevole' => $response['data'],
        ]);
    }

    public function changerStatut(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $statut = $this->getParam('statut', 'Validé');
            $this->apiClient->post('/api/benevoles/' . $id . '/statut', ['statut' => $statut]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Statut mis à jour : ' . $statut];
        }
        $this->rediriger('/admin/benevoles/' . $id);
    }
}
