<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

class AdministrateurControleur extends Controleur
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
        $response = $this->apiClient->get('/api/administrateurs');
        $administrateurs = ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];

        $this->rendre('backoffice/administrateurs/index', [
            'titre' => 'Comptes administrateurs',
            'pageActive' => 'administrateurs',
            'administrateurs' => $administrateurs,
            'moiId' => (int)($_SESSION['user']['id'] ?? 0),
        ]);
    }

    public function creer(): void
    {
        $this->verifierAuthentification();
        $this->rendre('backoffice/administrateurs/creer', [
            'titre' => 'Nouveau compte administrateur',
            'pageActive' => 'administrateurs',
        ]);
    }

    public function enregistrer(): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/administrateurs/creer');
            return;
        }
        $data = [
            'email' => $this->getParam('email'),
            'mot_de_passe' => $this->getParam('mot_de_passe'),
            'nom' => $this->getParam('nom'),
            'prenom' => $this->getParam('prenom'),
            'telephone' => $this->getParam('telephone', ''),
            'adresse' => $this->getParam('adresse', ''),
        ];
        $response = $this->apiClient->post('/api/administrateurs', $data);
        if ($response['code'] === 201 || $response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Compte administrateur créé'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la création';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/admin/administrateurs');
    }

    public function modifier(int $id): void
    {
        $this->verifierAuthentification();
        $response = $this->apiClient->get('/api/administrateurs/' . $id);
        if ($response['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Administrateur non trouvé'];
            $this->rediriger('/admin/administrateurs');
            return;
        }
        $this->rendre('backoffice/administrateurs/modifier', [
            'titre' => 'Modifier un compte administrateur',
            'pageActive' => 'administrateurs',
            'administrateur' => $response['data'],
        ]);
    }

    public function mettreAJour(int $id): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/administrateurs/' . $id . '/modifier');
            return;
        }
        $data = [
            'nom' => $this->getParam('nom'),
            'prenom' => $this->getParam('prenom'),
            'telephone' => $this->getParam('telephone', ''),
            'adresse' => $this->getParam('adresse', ''),
        ];
        $motDePasse = $this->getParam('mot_de_passe', '');
        if ($motDePasse !== '') {
            $data['mot_de_passe'] = $motDePasse;
        }
        $response = $this->apiClient->put('/api/administrateurs/' . $id, $data);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Compte mis à jour'];
            if ($id === (int)($_SESSION['user']['id'] ?? 0)) {
                // Garder la session à jour si l'admin modifie son propre compte
                $_SESSION['user']['nom'] = $data['nom'];
                $_SESSION['user']['prenom'] = $data['prenom'];
            }
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la mise à jour';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/admin/administrateurs');
    }

    public function supprimer(int $id): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/administrateurs');
            return;
        }
        if ($id === (int)($_SESSION['user']['id'] ?? 0)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Vous ne pouvez pas supprimer votre propre compte'];
            $this->rediriger('/admin/administrateurs');
            return;
        }
        $response = $this->apiClient->delete('/api/administrateurs/' . $id);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Compte administrateur supprimé'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la suppression';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/admin/administrateurs');
    }
}
