<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

class DashboardControleur extends Controleur
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

        $commercants = $this->apiClient->get('/api/commercants');
        $collectes = $this->apiClient->get('/api/collectes');
        $produits = $this->apiClient->get('/api/produits');
        $benevoles = $this->apiClient->get('/api/benevoles');
        $tournees = $this->apiClient->get('/api/tournees');
        $plannings = $this->apiClient->get('/api/service-plannings');
        $adherents = $this->apiClient->get('/api/adherents');
        $expirantes = $this->apiClient->get('/api/commercants/adhesions/expirantes');
        $adherentsExpirants = $this->apiClient->get('/api/adherents/adhesions/expirantes');

        $produitsData = ($produits['code'] === 200 && is_array($produits['data'])) ? $produits['data'] : [];
        $benevolesData = ($benevoles['code'] === 200 && is_array($benevoles['data'])) ? $benevoles['data'] : [];
        $tourneesData = ($tournees['code'] === 200 && is_array($tournees['data'])) ? $tournees['data'] : [];
        $planningsData = ($plannings['code'] === 200 && is_array($plannings['data'])) ? $plannings['data'] : [];
        $adherentsData = ($adherents['code'] === 200 && is_array($adherents['data'])) ? $adherents['data'] : [];
        $adhesionsExpirantes = ($expirantes['code'] === 200 && is_array($expirantes['data'])) ? $expirantes['data'] : [];
        $adherentsExpirantsData = ($adherentsExpirants['code'] === 200 && is_array($adherentsExpirants['data'])) ? $adherentsExpirants['data'] : [];

        $nbCommercants = ($commercants['code'] === 200 && is_array($commercants['data'])) ? count($commercants['data']) : 0;
        $nbCollectes = ($collectes['code'] === 200 && is_array($collectes['data'])) ? count($collectes['data']) : 0;
        $nbProduits = count($produitsData);
        $nbBenevoles = count($benevolesData);
        $nbTournees = count($tourneesData);
        $nbAdherents = count($adherentsData);

        $this->rendre('backoffice/contenu/dashboard', [
            'titre' => 'Tableau de bord',
            'pageActive' => 'dashboard',
            'nbCommercants' => $nbCommercants,
            'nbCollectes' => $nbCollectes,
            'nbProduits' => $nbProduits,
            'nbBenevoles' => $nbBenevoles,
            'nbTournees' => $nbTournees,
            'nbAdherents' => $nbAdherents,
            'adhesionsExpirantes' => $adhesionsExpirantes,
            'notifications' => $this->construireNotifications($produitsData, $tourneesData, $planningsData, $benevolesData, $adhesionsExpirantes, $adherentsData, $adherentsExpirantsData),
            'produitsParStatut' => $this->grouperParChamp($produitsData, 'statut'),
            'benevolesParCandidature' => $this->grouperParChamp($benevolesData, 'statut_candidature'),
            'tourneesParStatut' => $this->grouperParChamp($tourneesData, 'statut'),
            'user' => $_SESSION['user'] ?? null,
        ]);
    }

    /** Déclenche immédiatement les tâches quotidiennes (rappels de renouvellement + plannings). */
    public function executerTachesQuotidiennes(): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/dashboard');
            return;
        }

        $response = $this->apiClient->post('/api/taches/executer-quotidien', []);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Tâches quotidiennes exécutées (rappels de renouvellement + plannings bénévoles).'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur lors de l\'exécution des tâches quotidiennes.'];
        }
        $this->rediriger('/admin/dashboard');
    }

    private function grouperParChamp(array $items, string $champ): array
    {
        $compte = [];
        foreach ($items as $item) {
            $valeur = $item[$champ] ?? '';
            $valeur = ($valeur === '') ? 'Non défini' : $valeur;
            $compte[$valeur] = ($compte[$valeur] ?? 0) + 1;
        }
        return $compte;
    }

    /**
     * Construit la liste des notifications du tableau de bord à partir des données déjà
     * chargées (aucun appel API supplémentaire) : tournées imminentes, plannings tout
     * juste terminés, arrivages de produits, péremptions proches, adhésions et
     * candidatures à traiter, plannings complets.
     */
    private function construireNotifications(array $produits, array $tournees, array $plannings, array $benevoles, array $adhesionsExpirantes, array $adherents, array $adherentsExpirants): array
    {
        $notifications = [];
        $maintenant = new DateTime();

        // Tournées prévues dans les 48 prochaines heures
        $dans48h = (clone $maintenant)->modify('+48 hours');
        foreach ($tournees as $t) {
            if (($t['statut'] ?? '') !== 'Prévue' || empty($t['date_heure_depart'])) {
                continue;
            }
            $depart = new DateTime($t['date_heure_depart']);
            if ($depart >= $maintenant && $depart <= $dans48h) {
                $notifications[] = [
                    'type' => 'info',
                    'icone' => 'bi-truck',
                    'message' => 'Tournée #' . $t['id'] . ' prévue le ' . $depart->format('d/m à H:i'),
                    'lien' => url('/admin/tournees/' . $t['id']),
                    'date' => $depart,
                ];
            }
        }

        // Plannings de service terminés dans les dernières 24h
        $hier = (clone $maintenant)->modify('-24 hours');
        foreach ($plannings as $p) {
            if (empty($p['date_heure_fin'])) {
                continue;
            }
            $fin = new DateTime($p['date_heure_fin']);
            if ($fin <= $maintenant && $fin >= $hier) {
                $nomService = $p['service']['nom'] ?? ('Service #' . ($p['service_id'] ?? ''));
                $notifications[] = [
                    'type' => 'secondary',
                    'icone' => 'bi-calendar-check',
                    'message' => 'Le planning "' . $nomService . '" vient de se terminer',
                    'lien' => url('/admin/services/plannings/' . $p['id']),
                    'date' => $fin,
                ];
            }
            // Plannings complets
            $capacite = (int)($p['capacite_max'] ?? 0);
            $inscrits = (int)($p['nb_inscrits'] ?? 0);
            if ($capacite > 0 && $inscrits >= $capacite && ($p['statut'] ?? '') === 'Ouvert') {
                $nomService = $p['service']['nom'] ?? ('Service #' . ($p['service_id'] ?? ''));
                $notifications[] = [
                    'type' => 'info',
                    'icone' => 'bi-people-fill',
                    'message' => 'Le planning "' . $nomService . '" est complet (' . $inscrits . '/' . $capacite . ')',
                    'lien' => url('/admin/services/plannings/' . $p['id']),
                    'date' => $maintenant,
                ];
            }
        }

        // Produits passés en "Stocké" aujourd'hui (arrivage notable)
        $stockesAujourdhui = 0;
        foreach ($produits as $p) {
            if (($p['statut'] ?? '') === 'Stocké' && !empty($p['date_entree_stock'])) {
                $entree = new DateTime($p['date_entree_stock']);
                if ($entree->format('Y-m-d') === $maintenant->format('Y-m-d')) {
                    $stockesAujourdhui++;
                }
            }
        }
        if ($stockesAujourdhui >= 3) {
            $notifications[] = [
                'type' => 'success',
                'icone' => 'bi-box-seam',
                'message' => $stockesAujourdhui . ' produits sont passés en stock aujourd\'hui',
                'lien' => url('/admin/produits?statut=Stocké'),
                'date' => $maintenant,
            ];
        }

        // Produits proches de la péremption (3 jours), pas encore distribués
        $dans3j = (clone $maintenant)->modify('+3 days');
        $perimentBientot = 0;
        foreach ($produits as $p) {
            if (in_array($p['statut'] ?? '', ['Distribué'], true) || empty($p['date_peremption'])) {
                continue;
            }
            $peremption = new DateTime($p['date_peremption']);
            if ($peremption >= $maintenant && $peremption <= $dans3j) {
                $perimentBientot++;
            }
        }
        if ($perimentBientot > 0) {
            $notifications[] = [
                'type' => 'danger',
                'icone' => 'bi-exclamation-triangle',
                'message' => $perimentBientot . ' produit(s) arrivent à péremption dans moins de 3 jours',
                'lien' => url('/admin/produits'),
                'date' => $maintenant,
            ];
        }

        // Candidatures bénévoles en attente
        $enAttente = 0;
        foreach ($benevoles as $b) {
            if (($b['statut_candidature'] ?? '') === 'En attente') {
                $enAttente++;
            }
        }
        if ($enAttente > 0) {
            $notifications[] = [
                'type' => 'warning',
                'icone' => 'bi-person-lines-fill',
                'message' => $enAttente . ' candidature(s) bénévole en attente de validation',
                'lien' => url('/admin/benevoles'),
                'date' => $maintenant,
            ];
        }

        // Adhésions commerçants expirant bientôt
        foreach ($adhesionsExpirantes as $c) {
            $notifications[] = [
                'type' => 'warning',
                'icone' => 'bi-shop',
                'message' => 'Adhésion "' . ($c['raison_sociale'] ?? '') . '" expire le ' . format_date($c['date_fin_adhesion']),
                'lien' => url('/admin/commercants/' . $c['id'] . '/modifier'),
                'date' => new DateTime($c['date_fin_adhesion']),
            ];
        }

        // Demandes d'adhésion adhérent en attente
        $adherentsEnAttente = 0;
        foreach ($adherents as $ad) {
            if (($ad['statut_adhesion'] ?? '') === 'en_attente') {
                $adherentsEnAttente++;
            }
        }
        if ($adherentsEnAttente > 0) {
            $notifications[] = [
                'type' => 'warning',
                'icone' => 'bi-person-badge',
                'message' => $adherentsEnAttente . ' demande(s) d\'adhésion en attente de validation',
                'lien' => url('/admin/adherents?statut=en_attente'),
                'date' => $maintenant,
            ];
        }

        // Adhésions adhérents expirant bientôt
        foreach ($adherentsExpirants as $ad) {
            $notifications[] = [
                'type' => 'warning',
                'icone' => 'bi-person-badge',
                'message' => 'Expire bientôt : adhésion de ' . ($ad['prenom'] ?? '') . ' ' . ($ad['nom'] ?? '') . ' (le ' . format_date($ad['date_fin_adhesion']) . ')',
                'lien' => url('/admin/adherents/' . $ad['id']),
                'date' => new DateTime($ad['date_fin_adhesion']),
            ];
        }

        usort($notifications, fn($a, $b) => $a['date'] <=> $b['date']);

        return array_slice($notifications, 0, 10);
    }
}