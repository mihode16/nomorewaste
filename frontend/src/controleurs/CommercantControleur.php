<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

class CommercantControleur extends Controleur
{
    private ClientApi $apiClient;
    
    public function __construct()
    {
        parent::__construct();
        $this->apiClient = new ClientApi();
    }
    
    /**
     * Afficher la liste des commerçants
     */
    public function index(): void
    {
        $this->verifierAuthentification();
        
        $response = $this->apiClient->get('/api/commercants');
        $commercants = $response['code'] === 200 ? $response['data'] : [];
        
        $this->rendre('backoffice/commercants/index', [
            'titre' => 'Gestion des commerçants',
            'pageActive' => 'commercants',
            'commercants' => $commercants
        ]);
    }
    
    /**
     * Afficher le formulaire de création
     */
    public function creer(): void
    {
        $this->verifierAuthentification();
        
        $this->rendre('backoffice/commercants/creer', [
            'titre' => 'Nouveau commerçant',
            'pageActive' => 'commercants',
        ]);
    }
    
    /**
     * Enregistrer un nouveau commerçant
     */
    public function enregistrer(): void
    {
        $this->verifierAuthentification();
        
        if (!$this->estPost()) {
            $this->rediriger('/admin/commercants/creer');
            return;
        }
        
        // Récupérer les données du formulaire
        $data = [
            'email' => $this->getParam('email'),
            'mot_de_passe' => $this->getParam('mot_de_passe'),
            'nom' => $this->getParam('nom'),
            'prenom' => $this->getParam('prenom'),
            'telephone' => $this->getParam('telephone'),
            'adresse' => $this->getParam('adresse'),
            'siret' => $this->getParam('siret'),
            'raison_sociale' => $this->getParam('raison_sociale'),
            'type_commerce' => $this->getParam('type_commerce'),
            'date_debut_adhesion' => $this->getParam('date_debut_adhesion', date('Y-m-d')),
            'date_fin_adhesion' => $this->getParam('date_fin_adhesion', date('Y-m-d', strtotime('+1 year'))),
            'est_renouvele_automatiquement' => (bool)$this->getParam('est_renouvele_automatiquement', false)
        ];
        
         error_log(json_encode($data));

        // Envoyer à l'API
        $response = $this->apiClient->post('/api/commercants', $data);
        
        if ($response['code'] === 201 || $response['code'] === 200) {
            $_SESSION['flash'] = [
                'type' => 'success', 
                'message' => 'Commerçant créé avec succès !'
            ];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la création';
            $_SESSION['flash'] = [
                'type' => 'danger', 
                'message' => 'Erreur : ' . $erreur
            ];
        }
        
        $this->rediriger('/admin/commercants');
    }
    
    /**
     * Afficher le formulaire de modification
     */
    public function modifier(int $id): void
    {
        $this->verifierAuthentification();
        
        $response = $this->apiClient->get('/api/commercants/' . $id);
        
        if ($response['code'] !== 200) {
            $_SESSION['flash'] = [
                'type' => 'danger', 
                'message' => 'Commerçant non trouvé'
            ];
            $this->rediriger('/admin/commercants');
            return;
        }
        
        $this->rendre('backoffice/commercants/modifier', [
            'titre' => 'Modifier un commerçant',
            'pageActive' => 'commercants',
            'commercant' => $response['data']
        ]);
    }
    
    /**
     * Mettre à jour un commerçant
     */
    public function mettreAJour(int $id): void
    {
        $this->verifierAuthentification();
        
        if (!$this->estPost()) {
            $this->rediriger('/admin/commercants/' . $id . '/modifier');
            return;
        }
        
        $data = [
            'nom' => $this->getParam('nom'),
            'prenom' => $this->getParam('prenom'),
            'telephone' => $this->getParam('telephone'),
            'adresse' => $this->getParam('adresse'),
            'siret' => $this->getParam('siret'),
            'raison_sociale' => $this->getParam('raison_sociale'),
            'type_commerce' => $this->getParam('type_commerce'),
            'date_debut_adhesion' => $this->getParam('date_debut_adhesion'),
            'date_fin_adhesion' => $this->getParam('date_fin_adhesion'),
            'est_renouvele_automatiquement' => (bool)$this->getParam('est_renouvele_automatiquement', false)
        ];
        
        $response = $this->apiClient->put('/api/commercants/' . $id, $data);
        
        if ($response['code'] === 200) {
            $_SESSION['flash'] = [
                'type' => 'success', 
                'message' => 'Commerçant mis à jour avec succès'
            ];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la mise à jour';
            $_SESSION['flash'] = [
                'type' => 'danger', 
                'message' => 'Erreur : ' . $erreur
            ];
        }
        
        $this->rediriger('/admin/commercants');
    }
    
    /**
     * Supprimer un commerçant
     */
    public function supprimer(int $id): void
    {
        $this->verifierAuthentification();
        
        if (!$this->estPost()) {
            $this->rediriger('/admin/commercants');
            return;
        }
        
        $response = $this->apiClient->delete('/api/commercants/' . $id);
        
        if ($response['code'] === 200) {
            $_SESSION['flash'] = [
                'type' => 'success', 
                'message' => 'Commerçant supprimé avec succès'
            ];
        } else {
            $_SESSION['flash'] = [
                'type' => 'danger', 
                'message' => 'Erreur lors de la suppression'
            ];
        }
        
        $this->rediriger('/admin/commercants');
    }

    public function valider(int $id): void
    {
        $this->verifierAuthentification();

        if (!$this->estPost()) {
            $this->rediriger('/admin/commercants');
            return;
        }

        $response = $this->apiClient->post('/api/commercants/' . $id . '/valider', []);

        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Adhésion validée avec succès'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la validation';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }

        $this->rediriger('/admin/commercants');
    }

    public function demanderRenouvellement(int $id): void
{
    $this->verifierAuthentification();

    if (!$this->estPost()) {
        $this->rediriger('/admin/commercants');
        return;
    }

    $response = $this->apiClient->post('/api/commercants/' . $id . '/demander-renouvellement', []);

    if ($response['code'] === 200) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Demande de renouvellement enregistrée'];
    } else {
        $erreur = $response['data']['error'] ?? 'Erreur lors de la demande';
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
    }

    $this->rediriger('/admin/commercants');
}
    
    /**
     * Renouveler l'adhésion d'un commerçant
     */
    public function renouveler(int $id): void
    {
        $this->verifierAuthentification();
        
        if (!$this->estPost()) {
            $this->rediriger('/admin/commercants');
            return;
        }
        
        $dureeMois = (int)$this->getParam('duree_mois', 12);
        
        $response = $this->apiClient->post('/api/commercants/' . $id . '/renouveler', [
            'duree_mois' => $dureeMois
        ]);
        
        if ($response['code'] === 200) {
            $_SESSION['flash'] = [
                'type' => 'success', 
                'message' => 'Adhésion renouvelée avec succès pour ' . $dureeMois . ' mois'
            ];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors du renouvellement';
            $_SESSION['flash'] = [
                'type' => 'danger', 
                'message' => 'Erreur : ' . $erreur
            ];
        }
        
        $this->rediriger('/admin/commercants');
    }
}