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

        $recherche = $this->getParam('recherche', '');
        $categorie = $this->getParam('categorie', '');
        $tri = $this->getParam('tri', 'date_peremption_asc');

        $categoriesResponse = $this->apiClient->get('/api/produits/categories');
        $categories = ($categoriesResponse['code'] === 200 && is_array($categoriesResponse['data'])) ? $categoriesResponse['data'] : [];

        $query = http_build_query(array_filter([
            'recherche' => $recherche,
            'categorie' => $categorie,
            'tri' => $tri,
        ]));
        $endpoint = '/api/produits' . ($query ? '?' . $query : '');

        $response = $this->apiClient->get($endpoint);
        $produits = ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];

        $this->rendre('backoffice/produits/index', [
            'titre' => 'Stock produits',
            'pageActive' => 'produits',
            'produits' => $produits,
            'filtre_recherche' => $recherche,
            'filtre_categorie' => $categorie,
            'filtre_tri' => $tri,
            'categories' => $categories,
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
            return;
        }

        $collecteId = (int)$this->getParam('collecte_id', 0);

        if ($collecteId > 0) {
            $collecteResponse = $this->apiClient->get('/api/collectes/' . $collecteId);
            if ($collecteResponse['code'] === 200) {
                $collecte = $collecteResponse['data'];
                if (empty($collecte['validee']) || ($collecte['statut'] ?? '') !== 'Terminée') {
                    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Impossible d\'ajouter un produit : la collecte doit être validée et terminée.'];
                    $this->rediriger('/admin/collectes/' . $collecteId);
                    return;
                }
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Collecte introuvable.'];
                $this->rediriger('/admin/collectes');
                return;
            }
        }

        $data = [
            'code_barre' => $this->getParam('code_barre', ''),
            'nom' => $this->getParam('nom'),
            'categorie' => $this->getParam('categorie'),
            'quantite' => (int)$this->getParam('quantite', 1),
            'date_peremption' => $this->getParam('date_peremption'),
            'collecte_id' => $collecteId,
        ];

        $response = $this->apiClient->post('/api/produits', $data);

        if ($response['code'] === 201) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Produit ajouté avec succès.'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de l\'ajout';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }

        if ($collecteId > 0) {
            $this->rediriger('/admin/collectes/' . $collecteId);
        } else {
            $this->rediriger('/admin/produits');
        }
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