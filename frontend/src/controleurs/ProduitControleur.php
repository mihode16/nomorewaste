<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

class ProduitControleur extends Controleur
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
        $code = $this->getParam('code_barre', '');
        $endpoint = '/api/produits' . ($code !== '' ? '?code_barre=' . urlencode($code) : '');
        $response = $this->apiClient->get($endpoint);
        $this->rendre('backoffice/produits/index', [
            'titre' => 'Stock produits',
            'pageActive' => 'produits',
            'produits' => ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [],
            'code_barre' => $code,
        ]);
    }

    public function creer(): void
    {
        $this->verifierAuthentification();
        $collectes = $this->apiClient->get('/api/collectes');
        $this->rendre('backoffice/produits/creer', [
            'titre' => 'Ajouter un produit',
            'pageActive' => 'produits',
            'collectes' => ($collectes['code'] === 200) ? $collectes['data'] : [],
        ]);
    }

    public function enregistrer(): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/produits/creer');
        }
        $data = [
            'code_barre' => $this->getParam('code_barre'),
            'nom' => $this->getParam('nom'),
            'categorie' => $this->getParam('categorie'),
            'quantite' => (int)$this->getParam('quantite', 1),
            'date_peremption' => $this->getParam('date_peremption'),
            'collecte_id' => (int)$this->getParam('collecte_id'),
        ];
        $response = $this->apiClient->post('/api/produits', $data);
        $_SESSION['flash'] = ['type' => ($response['code'] === 201) ? 'success' : 'danger', 'message' => ($response['code'] === 201) ? 'Produit enregistré' : 'Erreur'];
        $this->rediriger('/admin/produits');
    }

    public function modifier(int $id): void
    {
        $this->verifierAuthentification();
        $produit = $this->apiClient->get('/api/produits/' . $id);
        $collectes = $this->apiClient->get('/api/collectes');
        if ($produit['code'] !== 200) {
            $this->rediriger('/admin/produits');
        }
        $this->rendre('backoffice/produits/modifier', [
            'titre' => 'Modifier le produit',
            'pageActive' => 'produits',
            'produit' => $produit['data'],
            'collectes' => ($collectes['code'] === 200) ? $collectes['data'] : [],
        ]);
    }

    public function mettreAJour(int $id): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/produits/' . $id . '/modifier');
        }
        $data = [
            'code_barre' => $this->getParam('code_barre'),
            'nom' => $this->getParam('nom'),
            'categorie' => $this->getParam('categorie'),
            'quantite' => (int)$this->getParam('quantite'),
            'date_peremption' => $this->getParam('date_peremption'),
            'statut' => $this->getParam('statut'),
            'collecte_id' => (int)$this->getParam('collecte_id'),
        ];
        $this->apiClient->put('/api/produits/' . $id, $data);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Produit mis à jour'];
        $this->rediriger('/admin/produits');
    }

    public function supprimer(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $this->apiClient->delete('/api/produits/' . $id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Produit supprimé'];
        }
        $this->rediriger('/admin/produits');
    }
}
