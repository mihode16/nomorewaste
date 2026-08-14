<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

class TourneeControleur extends Controleur
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

        $statut = $this->getParam('statut', '');
        $destination = $this->getParam('destination', '');
        $date = $this->getParam('date', '');

        $query = http_build_query(array_filter([
            'statut' => $statut,
            'destination' => $destination,
            'date' => $date,
        ]));
        $endpoint = '/api/tournees' . ($query ? '?' . $query : '');

        $response = $this->apiClient->get($endpoint);
        $this->rendre('backoffice/tournees/index', [
            'titre' => 'Tournées de distribution',
            'pageActive' => 'tournees',
            'tournees' => ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [],
            'filtre_statut' => $statut,
            'filtre_destination' => $destination,
            'filtre_date' => $date,
        ]);
    }

    public function creer(): void
    {
        $this->verifierAuthentification();
        $benevoles = $this->apiClient->get('/api/benevoles/valides');
        $lieux = $this->apiClient->get('/api/lieux-distribution');
        $produits = $this->apiClient->get('/api/produits?statut=Stocké');

        $this->rendre('backoffice/tournees/creer', [
            'titre' => 'Planifier une tournée',
            'pageActive' => 'tournees',
            'benevoles' => ($benevoles['code'] === 200) ? $benevoles['data'] : [],
            'lieux' => ($lieux['code'] === 200) ? $lieux['data'] : [],
            'produits' => ($produits['code'] === 200) ? $produits['data'] : [],
        ]);
    }

    public function enregistrer(): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/tournees/creer');
        }

        $produits = $this->getParam('produits_ids', []);
        if (!is_array($produits)) {
            $produits = [$produits];
        }
        $produits = array_map('intval', array_filter($produits));

        $benevolesSupp = $this->getParam('benevoles_supp', []);
        if (!is_array($benevolesSupp)) {
            $benevolesSupp = [];
        }
        $benevolesSupp = array_map('intval', array_filter($benevolesSupp));

        $dt = $this->getParam('date_heure_depart');
        $data = [
            'date_heure_depart' => str_replace('T', ' ', $dt) . (strlen($dt) === 16 ? ':00' : ''),
            'adresse_depart' => $this->getParam('adresse_depart'),
            'benevole_id' => (int)$this->getParam('benevole_id'),
            'lieu_distribution_id' => (int)$this->getParam('lieu_distribution_id'),
            'produits_ids' => $produits,
            'benevoles_ids' => $benevolesSupp,
        ];

        $response = $this->apiClient->post('/api/tournees', $data);
        $_SESSION['flash'] = [
            'type' => ($response['code'] === 201) ? 'success' : 'danger',
            'message' => ($response['code'] === 201) ? 'Tournée créée' : 'Erreur lors de la création',
        ];
        $this->rediriger('/admin/tournees');
    }

    public function detail(int $id): void
    {
        $this->verifierAuthentification();
        $response = $this->apiClient->get('/api/tournees/' . $id);
        if ($response['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Tournée non trouvée'];
            $this->rediriger('/admin/tournees');
        }

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

        $this->rendre('backoffice/tournees/detail', [
            'titre' => 'Détail tournée #' . $id,
            'pageActive' => 'tournees',
            'tournee' => $response['data'],
            'benevolesMap' => $benevolesMap,
        ]);
    }

    public function terminer(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $this->apiClient->post('/api/tournees/' . $id . '/terminer', []);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Tournée terminée'];
        }
        $this->rediriger('/admin/tournees/' . $id);
    }

    public function pdf(int $id): void
    {
        $this->verifierAuthentification();
        $response = $this->apiClient->getRaw('/api/tournees/' . $id . '/pdf');
        if ($response['code'] !== 200) {
            http_response_code(404);
            echo 'PDF non disponible pour cette tournée.';
            exit;
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="tournee-' . $id . '.pdf"');
        header('Content-Length: ' . strlen($response['body']));
        echo $response['body'];
        exit;
    }

    public function modifier(int $id): void
    {
        $this->verifierAuthentification();
        
        $tourneeResponse = $this->apiClient->get('/api/tournees/' . $id);
        if ($tourneeResponse['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Tournée non trouvée'];
            $this->rediriger('/admin/tournees');
            return;
        }
        
        // Essayer d'abord la route /valides, sinon fallback sur /benevoles
        $benevolesResponse = $this->apiClient->get('/api/benevoles/valides');
        if ($benevolesResponse['code'] !== 200) {
            $benevolesResponse = $this->apiClient->get('/api/benevoles');
        }
        
        $tousBenevoles = ($benevolesResponse['code'] === 200) ? $benevolesResponse['data'] : [];
        
        // Filtrer les bénévoles avec statut "Validé"
        $tousBenevoles = array_filter($tousBenevoles, function($b) {
            return ($b['statut_candidature'] ?? '') === 'Validé';
        });
        
        // Filtrer les chauffeurs (compétence "Chauffeur")
        $chauffeurs = [];
        $chauffeurIds = [];
        foreach ($tousBenevoles as $b) {
            $estChauffeur = false;
            foreach ($b['competences'] ?? [] as $comp) {
                if (stripos($comp['nom'], 'chauffeur') !== false) {
                    $estChauffeur = true;
                    break;
                }
            }
            if ($estChauffeur) {
                $chauffeurs[] = $b;
                $chauffeurIds[] = $b['id'];
            }
        }
        
        // Si aucun chauffeur trouvé, prendre tous les bénévoles
        if (empty($chauffeurs)) {
            $chauffeurs = $tousBenevoles;
            $chauffeurIds = array_column($tousBenevoles, 'id');
        }
        
        // Bénévoles supplémentaires = tous sauf les chauffeurs
        $benevolesNonChauffeurs = array_filter($tousBenevoles, function($b) use ($chauffeurIds) {
            return !in_array($b['id'], $chauffeurIds);
        });
        
        if (empty($benevolesNonChauffeurs)) {
            $benevolesNonChauffeurs = $tousBenevoles;
        }
        
        $lieux = $this->apiClient->get('/api/lieux-distribution');
        $produits = $this->apiClient->get('/api/produits?statut=Stocké');
        
        $this->rendre('backoffice/tournees/modifier', [
            'titre' => 'Modifier la tournée #' . $id,
            'pageActive' => 'tournees',
            'tournee' => $tourneeResponse['data'],
            'chauffeurs' => $chauffeurs,
            'benevoles' => $benevolesNonChauffeurs,
            'lieux' => ($lieux['code'] === 200) ? $lieux['data'] : [],
            'produits' => ($produits['code'] === 200) ? $produits['data'] : [],
        ]);
    }

    public function mettreAJour(int $id): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/tournees/' . $id . '/modifier');
            return;
        }
        
        $produits = $this->getParam('produits_ids', []);
        if (!is_array($produits)) {
            $produits = [$produits];
        }
        $produits = array_map('intval', array_filter($produits));
        
        $benevolesSupp = $this->getParam('benevoles_supp', []);
        if (!is_array($benevolesSupp)) {
            $benevolesSupp = [];
        }
        $benevolesSupp = array_map('intval', array_filter($benevolesSupp));
        
        $dt = $this->getParam('date_heure_depart');
        $data = [
            'date_heure_depart' => str_replace('T', ' ', $dt) . (strlen($dt) === 16 ? ':00' : ''),
            'adresse_depart' => $this->getParam('adresse_depart'),
            'benevole_id' => (int)$this->getParam('benevole_id'),
            'lieu_distribution_id' => (int)$this->getParam('lieu_distribution_id'),
            'produits_ids' => $produits,
            'benevoles_ids' => $benevolesSupp,
        ];
        
        $response = $this->apiClient->put('/api/tournees/' . $id, $data);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Tournée mise à jour'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur lors de la mise à jour'];
        }
        $this->rediriger('/admin/tournees');
    }

    public function supprimer(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $this->apiClient->delete('/api/tournees/' . $id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Tournée supprimée'];
        }
        $this->rediriger('/admin/tournees');
    }
}