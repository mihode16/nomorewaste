<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

class AdherentControleur extends Controleur
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
        $statut = $this->getParam('statut', '');

        $query = http_build_query(array_filter([
            'recherche' => $recherche,
            'statut' => $statut,
        ]));
        $endpoint = '/api/adherents' . ($query ? '?' . $query : '');

        $response = $this->apiClient->get($endpoint);
        $adherents = ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];

        $prixResponse = $this->apiClient->get('/api/parametres/prix-adhesion');
        $prixAdhesion = ($prixResponse['code'] === 200 && isset($prixResponse['data']['prix'])) ? (float)$prixResponse['data']['prix'] : 7.99;

        $this->rendre('backoffice/adherents/index', [
            'titre' => 'Gestion des adhérents',
            'pageActive' => 'adherents',
            'adherents' => $adherents,
            'filtre_recherche' => $recherche,
            'filtre_statut' => $statut,
            'prixAdhesion' => $prixAdhesion,
        ]);
    }

    public function modifierPrixAdhesion(): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/adherents');
            return;
        }
        $prix = (float)str_replace(',', '.', $this->getParam('prix', ''));
        $response = $this->apiClient->put('/api/parametres/prix-adhesion', ['prix' => $prix]);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Prix de l\'adhésion mis à jour'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la mise à jour du prix';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/admin/adherents');
    }

    public function creer(): void
    {
        $this->verifierAuthentification();
        $this->rendre('backoffice/adherents/creer', [
            'titre' => 'Nouvel adhérent',
            'pageActive' => 'adherents',
        ]);
    }

    public function enregistrer(): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/adherents/creer');
            return;
        }

        $data = [
            'email' => $this->getParam('email'),
            'mot_de_passe' => $this->getParam('mot_de_passe'),
            'nom' => $this->getParam('nom'),
            'prenom' => $this->getParam('prenom'),
            'telephone' => $this->getParam('telephone', ''),
            'adresse' => $this->getParam('adresse', ''),
            'date_debut_adhesion' => $this->getParam('date_debut_adhesion', date('Y-m-d')),
            'date_fin_adhesion' => $this->getParam('date_fin_adhesion', date('Y-m-d', strtotime('+1 year'))),
            'est_renouvele_automatiquement' => (bool)$this->getParam('est_renouvele_automatiquement', false),
        ];

        $response = $this->apiClient->post('/api/adherents', $data);
        if ($response['code'] === 201 || $response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Adhérent créé avec succès'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la création';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/admin/adherents');
    }

    public function detail(int $id): void
    {
        $this->verifierAuthentification();

        $adherent = $this->apiClient->get('/api/adherents/' . $id);
        if ($adherent['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Adhérent non trouvé'];
            $this->rediriger('/admin/adherents');
            return;
        }

        $inscriptions = $this->apiClient->get('/api/adherents/' . $id . '/inscriptions');
        $plannings = $this->apiClient->get('/api/service-plannings');
        $planningsData = ($plannings['code'] === 200 && is_array($plannings['data'])) ? $plannings['data'] : [];

        $inscritIds = array_column(($inscriptions['code'] === 200 && is_array($inscriptions['data'])) ? $inscriptions['data'] : [], 'id');

        // Plannings ouverts avec de la place, non déjà souscrits
        $planningsDisponibles = array_filter($planningsData, function ($p) use ($inscritIds) {
            $ouvert = ($p['statut'] ?? '') === 'Ouvert';
            $place = (int)($p['nb_inscrits'] ?? 0) < (int)($p['capacite_max'] ?? 0);
            return $ouvert && $place && !in_array($p['id'], $inscritIds);
        });

        $this->rendre('backoffice/adherents/detail', [
            'titre' => 'Adhérent : ' . ($adherent['data']['prenom'] ?? '') . ' ' . ($adherent['data']['nom'] ?? ''),
            'pageActive' => 'adherents',
            'adherent' => $adherent['data'],
            'inscriptions' => ($inscriptions['code'] === 200 && is_array($inscriptions['data'])) ? $inscriptions['data'] : [],
            'planningsDisponibles' => $planningsDisponibles,
        ]);
    }

    public function modifier(int $id): void
    {
        $this->verifierAuthentification();

        $response = $this->apiClient->get('/api/adherents/' . $id);
        if ($response['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Adhérent non trouvé'];
            $this->rediriger('/admin/adherents');
            return;
        }

        $this->rendre('backoffice/adherents/modifier', [
            'titre' => 'Modifier un adhérent',
            'pageActive' => 'adherents',
            'adherent' => $response['data'],
        ]);
    }

    public function mettreAJour(int $id): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/adherents/' . $id . '/modifier');
            return;
        }

        $data = [
            'nom' => $this->getParam('nom'),
            'prenom' => $this->getParam('prenom'),
            'telephone' => $this->getParam('telephone', ''),
            'adresse' => $this->getParam('adresse', ''),
            'date_debut_adhesion' => $this->getParam('date_debut_adhesion'),
            'date_fin_adhesion' => $this->getParam('date_fin_adhesion'),
            'est_renouvele_automatiquement' => (bool)$this->getParam('est_renouvele_automatiquement', false),
        ];

        $response = $this->apiClient->put('/api/adherents/' . $id, $data);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Adhérent mis à jour avec succès'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la mise à jour';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/admin/adherents');
    }

    public function supprimer(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $response = $this->apiClient->delete('/api/adherents/' . $id);
            if ($response['code'] === 200) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Adhérent supprimé avec succès'];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur lors de la suppression'];
            }
        }
        $this->rediriger('/admin/adherents');
    }

    public function valider(int $id): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/adherents');
            return;
        }
        $response = $this->apiClient->post('/api/adherents/' . $id . '/valider', []);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Adhésion validée avec succès'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la validation';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/admin/adherents');
    }

    public function renouveler(int $id): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/adherents');
            return;
        }
        $dureeMois = (int)$this->getParam('duree_mois', 12);
        $response = $this->apiClient->post('/api/adherents/' . $id . '/renouveler', ['duree_mois' => $dureeMois]);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Adhésion renouvelée avec succès pour ' . $dureeMois . ' mois'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors du renouvellement';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/admin/adherents');
    }

    public function ajouterAbonnement(int $id): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/adherents/' . $id);
            return;
        }
        $planningId = (int)$this->getParam('planning_id');
        if ($planningId <= 0) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Planning invalide'];
            $this->rediriger('/admin/adherents/' . $id);
            return;
        }

        $adherent = $this->apiClient->get('/api/adherents/' . $id);
        $statutOk = $adherent['code'] === 200 && adhesion_est_valide($adherent['data']);
        if (!$statutOk) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Impossible d\'inscrire cet adhérent : son adhésion n\'est pas validée ou est expirée (renouvellement requis).'];
            $this->rediriger('/admin/adherents/' . $id);
            return;
        }

        $response = $this->apiClient->post('/api/service-plannings/' . $planningId . '/inscrire', ['adherent_id' => $id]);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Abonnement au service ajouté'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de l\'inscription';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/admin/adherents/' . $id);
    }

    public function supprimerAbonnement(int $id, int $planningId): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $this->apiClient->delete('/api/adherents/' . $id . '/inscriptions/' . $planningId);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Abonnement retiré'];
        }
        $this->rediriger('/admin/adherents/' . $id);
    }
}
