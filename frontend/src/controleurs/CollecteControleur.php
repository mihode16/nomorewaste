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
    
    /**
     * Afficher la liste des collectes
     */
    public function index(): void
    {
        $this->verifierAuthentification();
        
        $response = $this->apiClient->get('/api/collectes');
        $collectes = $response['code'] === 200 ? $response['data'] : [];
        
        $this->rendre('backoffice/collectes/index', [
            'titre' => 'Gestion des collectes',
            'collectes' => $collectes
        ]);
    }
    
    /**
     * Afficher le formulaire de création
     */
    public function creer(): void
    {
        $this->verifierAuthentification();
        
        // Récupérer les commerçants pour le select
        $response = $this->apiClient->get('/api/commercants');
        $commercants = $response['code'] === 200 ? $response['data'] : [];
        
        $this->rendre('backoffice/collectes/creer', [
            'titre' => 'Nouvelle collecte',
            'commercants' => $commercants
        ]);
    }
    
    /**
     * Enregistrer une nouvelle collecte
     */
    public function enregistrer(): void
    {
        $this->verifierAuthentification();
        
        if (!$this->estPost()) {
            $this->rediriger('/admin/collectes/creer');
            return;
        }
        
        $data = [
            'date_heure_collecte' => $this->getParam('date_heure_collecte'),
            'adresse_collecte' => $this->getParam('adresse_collecte'),
            'commercant_id' => (int)$this->getParam('commercant_id'),
            'commentaire' => $this->getParam('commentaire')
        ];
        
        $response = $this->apiClient->post('/api/collectes', $data);
        
        if ($response['code'] === 201 || $response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Collecte créée avec succès !'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la création';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        
        $this->rediriger('/admin/collectes');
    }
    
    /**
     * Afficher le formulaire de modification
     */
    public function modifier(int $id): void
    {
        $this->verifierAuthentification();
        
        $response = $this->apiClient->get('/api/collectes/' . $id);
        
        if ($response['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Collecte non trouvée'];
            $this->rediriger('/admin/collectes');
            return;
        }
        
        $commercantsResponse = $this->apiClient->get('/api/commercants');
        $commercants = $commercantsResponse['code'] === 200 ? $commercantsResponse['data'] : [];
        
        $this->rendre('backoffice/collectes/modifier', [
            'titre' => 'Modifier une collecte',
            'collecte' => $response['data'],
            'commercants' => $commercants
        ]);
    }
    
    /**
     * Mettre à jour une collecte
     */
    public function mettreAJour(int $id): void
    {
        $this->verifierAuthentification();
        
        if (!$this->estPost()) {
            $this->rediriger('/admin/collectes/' . $id . '/modifier');
            return;
        }
        
        $data = [
            'date_heure_collecte' => $this->getParam('date_heure_collecte'),
            'adresse_collecte' => $this->getParam('adresse_collecte'),
            'commercant_id' => (int)$this->getParam('commercant_id'),
            'commentaire' => $this->getParam('commentaire')
        ];
        
        $response = $this->apiClient->put('/api/collectes/' . $id, $data);
        
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Collecte mise à jour avec succès'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la mise à jour';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        
        $this->rediriger('/admin/collectes');
    }
    
    /**
     * Marquer une collecte comme terminée
     */
    public function terminer(int $id): void
    {
        $this->verifierAuthentification();
        
        if (!$this->estPost()) {
            $this->rediriger('/admin/collectes');
            return;
        }
        
        $response = $this->apiClient->post('/api/collectes/' . $id . '/terminer', []);
        
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Collecte terminée avec succès'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur lors du marquage'];
        }
        
        $this->rediriger('/admin/collectes');
    }
    
    /**
     * Supprimer une collecte
     */
    public function supprimer(int $id): void
    {
        $this->verifierAuthentification();
        
        if (!$this->estPost()) {
            $this->rediriger('/admin/collectes');
            return;
        }
        
        $response = $this->apiClient->delete('/api/collectes/' . $id);
        
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Collecte supprimée avec succès'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur lors de la suppression'];
        }
        
        $this->rediriger('/admin/collectes');
    }
}