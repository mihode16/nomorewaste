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

        $commercant = $this->getParam('commercant', '');
        $statut = $this->getParam('statut', '');
        $dateDebut = $this->getParam('date_debut', '');
        $dateFin = $this->getParam('date_fin', '');

        $query = http_build_query(array_filter([
            'commercant' => $commercant,
            'statut' => $statut,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
        ]));
        $endpoint = '/api/collectes' . ($query ? '?' . $query : '');

        $response = $this->apiClient->get($endpoint);
        $collectes = ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];

        $this->rendre('backoffice/collectes/index', [
            'titre' => 'Collectes',
            'pageActive' => 'collectes',
            'collectes' => $collectes,
            'filtre_commercant' => $commercant,
            'filtre_statut' => $statut,
            'filtre_date_debut' => $dateDebut,
            'filtre_date_fin' => $dateFin,
        ]);
    }

    /** Une adhésion commerçant doit être validée par un admin ET non expirée pour recevoir une collecte. */
    private function adhesionCommercantValide(?array $commercant): bool
    {
        return adhesion_est_valide($commercant);
    }

    public function creer(): void
    {
        $this->verifierAuthentification();
        $commercants = $this->apiClient->get('/api/commercants');
        $commercantsData = ($commercants['code'] === 200 && is_array($commercants['data'])) ? $commercants['data'] : [];
        $commercantsData = array_values(array_filter($commercantsData, fn($c) => $this->adhesionCommercantValide($c)));

        $this->rendre('backoffice/collectes/creer', [
            'titre' => 'Planifier une collecte',
            'pageActive' => 'collectes',
            'commercants' => $commercantsData,
        ]);
    }

    public function enregistrer(): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/collectes/creer');
        }

        $commercantId = (int)$this->getParam('commercant_id');
        $commercant = $this->apiClient->get('/api/commercants/' . $commercantId);
        if (!$this->adhesionCommercantValide($commercant['code'] === 200 ? $commercant['data'] : null)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Impossible de planifier une collecte pour ce commerçant : son adhésion n\'est pas validée ou est expirée (renouvellement requis).'];
            $this->rediriger('/admin/collectes/creer');
            return;
        }

        $dt = $this->getParam('date_heure_collecte');
        $data = [
            'date_heure_collecte' => str_replace('T', ' ', $dt) . (strlen($dt) === 16 ? ':00' : ''),
            'adresse_collecte' => $this->getParam('adresse_collecte'),
            'commercant_id' => $commercantId,
            'commentaire' => $this->getParam('commentaire', ''),
        ];
        $response = $this->apiClient->post('/api/collectes', $data);
        $_SESSION['flash'] = [
            'type' => ($response['code'] === 201 || $response['code'] === 200) ? 'success' : 'danger',
            'message' => ($response['code'] === 201 || $response['code'] === 200) ? 'Collecte créée' : ('Erreur : ' . ($response['data']['error'] ?? 'échec')),
        ];
        $this->rediriger('/admin/collectes');
    }

    public function modifier(int $id): void
    {
        $this->verifierAuthentification();

        $collecte = $this->apiClient->get('/api/collectes/' . $id);
        if ($collecte['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Collecte non trouvée ou erreur API (code '.$collecte['code'].')'];
            $this->rediriger('/admin/collectes');
            return;
        }

        if (!is_array($collecte['data'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Données de collecte invalides'];
            $this->rediriger('/admin/collectes');
            return;
        }

        $commercants = $this->apiClient->get('/api/commercants');
        $commercantsData = ($commercants['code'] === 200 && is_array($commercants['data'])) ? $commercants['data'] : [];

        $this->rendre('backoffice/collectes/modifier', [
            'titre' => 'Modifier la collecte #' . $id,
            'pageActive' => 'collectes',
            'collecte' => $collecte['data'],
            'commercants' => $commercantsData,
        ]);
    }

    public function mettreAJour(int $id): void
    {
        $this->verifierAuthentification();

        if (!$this->estPost()) {
            $this->rediriger('/admin/collectes/' . $id . '/modifier');
            return;
        }

        $commercantId = (int)$this->getParam('commercant_id');
        $commercant = $this->apiClient->get('/api/commercants/' . $commercantId);
        if (!$this->adhesionCommercantValide($commercant['code'] === 200 ? $commercant['data'] : null)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Impossible d\'assigner cette collecte à ce commerçant : son adhésion n\'est pas validée ou est expirée (renouvellement requis).'];
            $this->rediriger('/admin/collectes/' . $id . '/modifier');
            return;
        }

        $dt = $this->getParam('date_heure_collecte');
        $data = [
            'date_heure_collecte' => str_replace('T', ' ', $dt),
            'adresse_collecte' => $this->getParam('adresse_collecte'),
            'commercant_id' => $commercantId,
            'commentaire' => $this->getParam('commentaire', ''),
            'statut' => $this->getParam('statut', 'Planifiée'),
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

    public function terminer(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $this->apiClient->post('/api/collectes/' . $id . '/terminer', []);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Collecte terminée'];
        }
        $this->rediriger('/admin/collectes');
    }

    public function pdf(int $id): void
    {
        $this->verifierAuthentification();
        $response = $this->apiClient->getRaw('/api/collectes/' . $id . '/pdf');
        if ($response['code'] !== 200) {
            http_response_code(404);
            echo 'PDF non disponible pour cette collecte.';
            exit;
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="collecte-' . $id . '.pdf"');
        header('Content-Length: ' . strlen($response['body']));
        echo $response['body'];
        exit;
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

    public function valider(int $id): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/collectes');
            return;
        }
        $response = $this->apiClient->post('/api/collectes/' . $id . '/valider', []);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Collecte validée, statut : Planifiée'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la validation';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/admin/collectes');
    }

    public function detail(int $id): void
    {
        $this->verifierAuthentification();
        $collecte = $this->apiClient->get('/api/collectes/' . $id);
        if ($collecte['code'] !== 200) {
            $this->rediriger('/admin/collectes');
            return;
        }
        $produits = $this->apiClient->get('/api/collectes/' . $id . '/produits');

        $benevolesList = $this->apiClient->get('/api/benevoles/valides');
        $benevolesMap = [];
        if ($benevolesList['code'] === 200) {
            foreach ($benevolesList['data'] as $b) {
                $idB = (int)$b['id'];
                $competences = [];
                if (isset($b['competences']) && is_array($b['competences'])) {
                    $competences = array_column($b['competences'], 'nom');
                }
                $benevolesMap[$idB] = [
                    'nom' => $b['prenom'] . ' ' . $b['nom'],
                    'competences' => $competences,
                ];
            }
        }

        $this->rendre('backoffice/collectes/detail', [
            'titre' => 'Détail de la collecte #' . $id,
            'pageActive' => 'collectes',
            'collecte' => $collecte['data'],
            'produits' => ($produits['code'] === 200) ? $produits['data'] : [],
            'benevolesMap' => $benevolesMap,
        ]);
    }

    public function gererBenevoles(int $id): void
    {
        $this->verifierAuthentification();
        $collecte = $this->apiClient->get('/api/collectes/' . $id);
        if ($collecte['code'] !== 200) {
            $this->rediriger('/admin/collectes');
        }
        $benevolesResponse = $this->apiClient->get('/api/benevoles/valides');
        $benevolesValides = ($benevolesResponse['code'] === 200) ? $benevolesResponse['data'] : [];

        $benevolesMap = [];
        foreach ($benevolesValides as $b) {
            $competences = [];
            if (!empty($b['competences'])) {
                foreach ($b['competences'] as $comp) {
                    $competences[] = $comp['nom'];
                }
            }
            $benevolesMap[$b['id']] = [
                'nom' => $b['prenom'] . ' ' . $b['nom'],
                'competences' => implode(', ', $competences),
            ];
        }

        $this->rendre('backoffice/collectes/benevoles', [
            'titre' => 'Gérer les bénévoles - Collecte #' . $id,
            'pageActive' => 'collectes',
            'collecte' => $collecte['data'],
            'benevoles' => $benevolesValides,
            'benevolesMap' => $benevolesMap,
        ]);
    }

    public function ajouterBenevole(int $id): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/collectes/' . $id . '/benevoles');
            return;
        }
        $benevoleID = (int)$this->getParam('benevole_id');
        if ($benevoleID <= 0) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Bénévole invalide'];
            $this->rediriger('/admin/collectes/' . $id . '/benevoles');
            return;
        }
        $response = $this->apiClient->post('/api/collectes/' . $id . '/benevoles', ['benevole_id' => $benevoleID]);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Bénévole ajouté à la collecte'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de l\'ajout';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/admin/collectes/' . $id . '/benevoles');
    }

    public function supprimerBenevole(int $id, int $benevoleId): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $response = $this->apiClient->delete('/api/collectes/' . $id . '/benevoles/' . $benevoleId);
            if ($response['code'] === 200) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Bénévole retiré de la collecte'];
            } else {
                $erreur = $response['data']['error'] ?? 'Erreur lors du retrait';
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
            }
        }
        $this->rediriger('/admin/collectes/' . $id);
    }
}