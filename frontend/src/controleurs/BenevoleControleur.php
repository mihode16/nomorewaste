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

        $nom = $this->getParam('nom', '');
        $statut = $this->getParam('statut', '');
        $competenceId = (int)$this->getParam('competence_id', 0);

        // Récupérer les compétences pour le filtre
        $compResponse = $this->apiClient->get('/api/competences');
        $competences = ($compResponse['code'] === 200) ? $compResponse['data'] : [];

        $query = http_build_query(array_filter([
            'nom' => $nom,
            'statut' => $statut,
            'competence_id' => $competenceId > 0 ? $competenceId : null,
        ]));
        $endpoint = '/api/benevoles' . ($query ? '?' . $query : '');

        $response = $this->apiClient->get($endpoint);
        $benevoles = ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];

        $this->rendre('backoffice/benevoles/index', [
            'titre' => 'Gestion des bénévoles',
            'pageActive' => 'benevoles',
            'benevoles' => $benevoles,
            'filtre_nom' => $nom,
            'filtre_statut' => $statut,
            'filtre_competence_id' => $competenceId,
            'competences' => $competences,
        ]);
    }

    public function calendrier(): void
    {
        $this->verifierAuthentification();

        $semaine = $this->getParam('semaine', 'courante');
        if (!in_array($semaine, ['courante', 'prochaine'], true)) {
            $semaine = 'courante';
        }

        $response = $this->apiClient->get('/api/disponibilites');
        $disponibilites = ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];

        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

        $lundi = strtotime('monday this week');
        if ($semaine === 'prochaine') {
            $lundi = strtotime('+7 days', $lundi);
        }
        $datesJoursISO = [];
        $datesJours = [];
        foreach ($jours as $i => $jour) {
            $ts = strtotime("+{$i} days", $lundi);
            $datesJoursISO[$jour] = date('Y-m-d', $ts);
            $datesJours[$jour] = date('d/m/Y', $ts);
        }

        // On ne garde que les créneaux dont la date correspond exactement au jour de la
        // semaine affichée (courante ou prochaine), pour bien séparer les deux semaines.
        $parJour = array_fill_keys($jours, []);
        foreach ($disponibilites as $d) {
            $jour = $d['jour_semaine'] ?? '';
            if (isset($parJour[$jour]) && ($d['date'] ?? '') === $datesJoursISO[$jour]) {
                $parJour[$jour][] = $d;
            }
        }

        $this->rendre('backoffice/benevoles/calendrier', [
            'titre' => 'Calendrier des disponibilités',
            'pageActive' => 'benevoles',
            'jours' => $jours,
            'parJour' => $parJour,
            'datesJours' => $datesJours,
            'semaine' => $semaine,
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
        $competences = $this->apiClient->get('/api/competences');

        $planningsResponse = $this->apiClient->get('/api/benevoles/' . $id . '/planning');
        $plannings = ($planningsResponse['code'] === 200 && is_array($planningsResponse['data'])) ? $planningsResponse['data'] : [];

        $this->rendre('backoffice/benevoles/detail', [
            'titre' => 'Détail bénévole',
            'pageActive' => 'benevoles',
            'benevole' => $response['data'],
            'toutesCompetences' => ($competences['code'] === 200) ? $competences['data'] : [],
            'plannings' => $plannings,
        ]);
    }

    /** Génère (côté API Go) le planning Excel d'un bénévole sur une période et le rend disponible dans son espace. */
    public function genererPlanning(int $id): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/benevoles/' . $id);
            return;
        }

        $data = [
            'date_debut' => $this->getParam('date_debut'),
            'date_fin' => $this->getParam('date_fin'),
        ];
        $response = $this->apiClient->post('/api/benevoles/' . $id . '/planning', $data);
        if ($response['code'] === 201) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Planning généré et envoyé dans l\'espace du bénévole.'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la génération du planning.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/admin/benevoles/' . $id);
    }

    public function changerStatut(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $statut = $this->getParam('statut', 'Validé');
            $this->apiClient->post('/api/benevoles/' . $id . '/statut', ['statut' => $statut]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Statut mis à jour : ' . $statut];
        }
        // Redirection vers l'index
        $this->rediriger('/admin/benevoles');
    }

    public function supprimer(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $this->apiClient->delete('/api/benevoles/' . $id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Bénévole supprimé'];
        }
        $this->rediriger('/admin/benevoles');
    }

    public function ajouterCompetence(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $compId = (int)$this->getParam('competence_id');
            if ($compId > 0) {
                $response = $this->apiClient->post('/api/benevoles/' . $id . '/competences', ['competence_id' => $compId]);
                if ($response['code'] === 200) {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Compétence ajoutée avec succès'];
                } else {
                    $erreur = $response['data']['error'] ?? 'Erreur lors de l\'ajout';
                    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
                }
            }
        }
        $this->rediriger('/admin/benevoles/' . $id);
    }

    public function supprimerCompetence(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $compId = (int)$this->getParam('competence_id');
            if ($compId > 0) {
                $response = $this->apiClient->delete('/api/benevoles/' . $id . '/competences', ['competence_id' => $compId]);
                if ($response['code'] === 200) {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Compétence supprimée avec succès'];
                } else {
                    $erreur = $response['data']['error'] ?? 'Erreur lors de la suppression';
                    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
                }
            }
        }
        $this->rediriger('/admin/benevoles/' . $id);
    }
}