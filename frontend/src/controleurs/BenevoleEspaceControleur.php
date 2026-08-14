<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

/**
 * Espace self-service des bénévoles : gestion de leurs disponibilités, consultation de leurs
 * affectations (collectes, tournées, services) avec possibilité de les marquer comme
 * terminées, téléchargement du planning Excel généré par l'association, et profil.
 */
class BenevoleEspaceControleur extends Controleur
{
    private ClientApi $apiClient;

    public function __construct()
    {
        parent::__construct();
        $this->apiClient = new ClientApi();
    }

    private function monId(): int
    {
        return (int)($_SESSION['user']['id'] ?? 0);
    }

    private function monBenevole(): ?array
    {
        $response = $this->apiClient->get('/api/benevoles/' . $this->monId());
        if ($response['code'] !== 200) {
            return null;
        }
        return $response['data'];
    }

    /** Collectes où je suis inscrit·e comme bénévole. */
    private function mesCollectes(): array
    {
        $response = $this->apiClient->get('/api/collectes');
        if ($response['code'] !== 200 || !is_array($response['data'])) {
            return [];
        }
        $monId = $this->monId();
        return array_values(array_filter($response['data'], function ($c) use ($monId) {
            foreach (($c['benevoles'] ?? []) as $cb) {
                if ((int)($cb['benevole_id'] ?? 0) === $monId) {
                    return true;
                }
            }
            return false;
        }));
    }

    /** Tournées où je suis chauffeur ou bénévole supplémentaire. */
    private function mesTournees(): array
    {
        $response = $this->apiClient->get('/api/tournees');
        if ($response['code'] !== 200 || !is_array($response['data'])) {
            return [];
        }
        $monId = $this->monId();
        return array_values(array_filter($response['data'], function ($t) use ($monId) {
            if ((int)($t['benevole_id'] ?? 0) === $monId) {
                return true;
            }
            foreach (($t['benevoles'] ?? []) as $b) {
                if ((int)($b['id'] ?? 0) === $monId) {
                    return true;
                }
            }
            return false;
        }));
    }

    /** Séances de service dont je suis l'intervenant. */
    private function mesServices(): array
    {
        $response = $this->apiClient->get('/api/service-plannings');
        if ($response['code'] !== 200 || !is_array($response['data'])) {
            return [];
        }
        $monId = $this->monId();
        return array_values(array_filter($response['data'], fn($p) => (int)($p['benevole_id'] ?? 0) === $monId));
    }

    public function dashboard(): void
    {
        $this->verifierAuthentification(['benevole']);

        $benevole = $this->monBenevole();
        if (!$benevole) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Impossible de charger votre compte.'];
            $this->rediriger('/deconnexion');
            return;
        }

        $collectes = $this->mesCollectes();
        $tournees = $this->mesTournees();
        $services = $this->mesServices();

        $prochaines = [];
        foreach ($collectes as $c) {
            if (($c['statut'] ?? '') !== 'Terminée') {
                $prochaines[] = ['type' => 'Collecte', 'date' => $c['date_heure_collecte'], 'detail' => $c['adresse_collecte'], 'lien' => '/benevole/collectes/' . $c['id']];
            }
        }
        foreach ($tournees as $t) {
            if (($t['statut'] ?? '') !== 'Terminée') {
                $prochaines[] = ['type' => 'Tournée', 'date' => $t['date_heure_depart'], 'detail' => $t['adresse_depart'], 'lien' => '/benevole/tournees/' . $t['id']];
            }
        }
        foreach ($services as $p) {
            if (strtotime($p['date_heure_debut']) >= time()) {
                $prochaines[] = ['type' => 'Service', 'date' => $p['date_heure_debut'], 'detail' => $p['service']['nom'] ?? 'Service', 'lien' => '/benevole/affectations'];
            }
        }
        usort($prochaines, fn($a, $b) => strtotime($a['date']) <=> strtotime($b['date']));
        $prochaines = array_filter($prochaines, fn($p) => strtotime($p['date']) >= strtotime('-1 day'));

        $this->rendre('frontoffice/benevole/dashboard', [
            'titre' => 'Tableau de bord',
            'pageActive' => 'dashboard',
            'benevole' => $benevole,
            'nbCollectes' => count($collectes),
            'nbTournees' => count($tournees),
            'nbServices' => count($services),
            'prochaines' => array_slice($prochaines, 0, 6),
        ], 'benevole');
    }

    public function disponibilites(): void
    {
        $this->verifierAuthentification(['benevole']);

        $response = $this->apiClient->get('/api/benevoles/' . $this->monId() . '/disponibilites');
        $disponibilites = ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];

        $this->rendre('frontoffice/benevole/disponibilites', [
            'titre' => 'Mes disponibilités',
            'pageActive' => 'disponibilites',
            'disponibilites' => $disponibilites,
        ], 'benevole');
    }

    /** Convertit une sélection "semaine courante/prochaine" + jour en date précise (Y-m-d). */
    private function dateDepuisSemaineJour(string $semaine, string $jourSemaine): ?string
    {
        $jours = ['Lundi' => 0, 'Mardi' => 1, 'Mercredi' => 2, 'Jeudi' => 3, 'Vendredi' => 4, 'Samedi' => 5, 'Dimanche' => 6];
        if (!isset($jours[$jourSemaine])) {
            return null;
        }
        $lundi = strtotime('monday this week');
        if ($semaine === 'prochaine') {
            $lundi = strtotime('+7 days', $lundi);
        }
        return date('Y-m-d', strtotime('+' . $jours[$jourSemaine] . ' days', $lundi));
    }

    public function ajouterDisponibilite(): void
    {
        $this->verifierAuthentification(['benevole']);
        if (!$this->estPost()) {
            $this->rediriger('/benevole/disponibilites');
            return;
        }

        $jourSemaine = $this->getParam('jour_semaine');
        $semaine = $this->getParam('semaine', 'courante');
        $date = $this->dateDepuisSemaineJour($semaine, $jourSemaine);
        if ($date === null) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Jour invalide.'];
            $this->rediriger('/benevole/disponibilites');
            return;
        }

        $data = [
            'jour_semaine' => $jourSemaine,
            'date' => $date,
            'heure_debut' => $this->getParam('heure_debut'),
            'heure_fin' => $this->getParam('heure_fin'),
        ];
        $response = $this->apiClient->post('/api/benevoles/' . $this->monId() . '/disponibilites', $data);
        if ($response['code'] === 201) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Disponibilité ajoutée.'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de l\'ajout.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/benevole/disponibilites');
    }

    public function supprimerDisponibilite(int $id): void
    {
        $this->verifierAuthentification(['benevole']);
        if ($this->estPost()) {
            $this->apiClient->delete('/api/benevoles/' . $this->monId() . '/disponibilites/' . $id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Disponibilité supprimée.'];
        }
        $this->rediriger('/benevole/disponibilites');
    }

    public function affectations(): void
    {
        $this->verifierAuthentification(['benevole']);

        $this->rendre('frontoffice/benevole/affectations', [
            'titre' => 'Mes affectations',
            'pageActive' => 'affectations',
            'collectes' => $this->mesCollectes(),
            'tournees' => $this->mesTournees(),
            'services' => $this->mesServices(),
        ], 'benevole');
    }

    public function collecteDetail(int $id): void
    {
        $this->verifierAuthentification(['benevole']);

        $response = $this->apiClient->get('/api/collectes/' . $id);
        if ($response['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Collecte introuvable.'];
            $this->rediriger('/benevole/affectations');
            return;
        }
        $collecte = $response['data'];

        $monId = $this->monId();
        $maLigne = null;
        foreach (($collecte['benevoles'] ?? []) as $cb) {
            if ((int)($cb['benevole_id'] ?? 0) === $monId) {
                $maLigne = $cb;
                break;
            }
        }
        if ($maLigne === null) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Vous n\'êtes pas affecté·e à cette collecte.'];
            $this->rediriger('/benevole/affectations');
            return;
        }

        $produitsResponse = $this->apiClient->get('/api/collectes/' . $id . '/produits');

        $heurePasse = strtotime($collecte['date_heure_collecte']) <= time();
        $peutTerminer = $heurePasse && empty($maLigne['confirme']) && ($collecte['statut'] ?? '') === 'Planifiée';

        $this->rendre('frontoffice/benevole/collecte_detail', [
            'titre' => 'Collecte #' . $id,
            'pageActive' => 'affectations',
            'collecte' => $collecte,
            'produits' => ($produitsResponse['code'] === 200 && is_array($produitsResponse['data'])) ? $produitsResponse['data'] : [],
            'maLigne' => $maLigne,
            'peutTerminer' => $peutTerminer,
            'heurePasse' => $heurePasse,
        ], 'benevole');
    }

    public function terminerCollecte(int $id): void
    {
        $this->verifierAuthentification(['benevole']);
        if (!$this->estPost()) {
            $this->rediriger('/benevole/collectes/' . $id);
            return;
        }

        $collecte = $this->apiClient->get('/api/collectes/' . $id);
        if ($collecte['code'] !== 200 || strtotime($collecte['data']['date_heure_collecte']) > time()) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'L\'heure de la collecte n\'est pas encore passée.'];
            $this->rediriger('/benevole/collectes/' . $id);
            return;
        }

        $response = $this->apiClient->post('/api/collectes/' . $id . '/confirmer', ['benevole_id' => $this->monId()]);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Collecte marquée comme terminée de votre côté. Dès que tous les bénévoles auront confirmé, elle passera automatiquement en statut "Terminée".'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la confirmation.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/benevole/collectes/' . $id);
    }

    public function tourneeDetail(int $id): void
    {
        $this->verifierAuthentification(['benevole']);

        $response = $this->apiClient->get('/api/tournees/' . $id);
        if ($response['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Tournée introuvable.'];
            $this->rediriger('/benevole/affectations');
            return;
        }
        $tournee = $response['data'];

        $monId = $this->monId();
        $estChauffeur = (int)($tournee['benevole_id'] ?? 0) === $monId;
        $confirme = $estChauffeur ? !empty($tournee['chauffeur_confirme']) : false;
        if (!$estChauffeur) {
            $estAffecte = false;
            foreach (($tournee['benevoles_confirmation'] ?? []) as $bc) {
                if ((int)($bc['benevole_id'] ?? 0) === $monId) {
                    $estAffecte = true;
                    $confirme = !empty($bc['confirme']);
                    break;
                }
            }
            if (!$estAffecte) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Vous n\'êtes pas affecté·e à cette tournée.'];
                $this->rediriger('/benevole/affectations');
                return;
            }
        }

        $dateReference = $tournee['date_heure_fin'] ?? $tournee['date_heure_depart'];
        $heurePasse = strtotime($dateReference) <= time();
        $peutTerminer = $heurePasse && !$confirme && ($tournee['statut'] ?? '') !== 'Terminée';

        $this->rendre('frontoffice/benevole/tournee_detail', [
            'titre' => 'Tournée #' . $id,
            'pageActive' => 'affectations',
            'tournee' => $tournee,
            'estChauffeur' => $estChauffeur,
            'confirme' => $confirme,
            'peutTerminer' => $peutTerminer,
            'heurePasse' => $heurePasse,
        ], 'benevole');
    }

    public function terminerTournee(int $id): void
    {
        $this->verifierAuthentification(['benevole']);
        if (!$this->estPost()) {
            $this->rediriger('/benevole/tournees/' . $id);
            return;
        }

        $tournee = $this->apiClient->get('/api/tournees/' . $id);
        if ($tournee['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Tournée introuvable.'];
            $this->rediriger('/benevole/affectations');
            return;
        }
        $dateReference = $tournee['data']['date_heure_fin'] ?? $tournee['data']['date_heure_depart'];
        if (strtotime($dateReference) > time()) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'L\'heure de la tournée n\'est pas encore passée.'];
            $this->rediriger('/benevole/tournees/' . $id);
            return;
        }

        $response = $this->apiClient->post('/api/tournees/' . $id . '/confirmer', ['benevole_id' => $this->monId()]);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Tournée marquée comme terminée de votre côté. Dès que tous les bénévoles auront confirmé, elle passera automatiquement en statut "Terminée".'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la confirmation.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/benevole/tournees/' . $id);
    }

    public function planning(): void
    {
        $this->verifierAuthentification(['benevole']);

        $response = $this->apiClient->get('/api/benevoles/' . $this->monId() . '/planning');
        $plannings = ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];

        $this->rendre('frontoffice/benevole/planning', [
            'titre' => 'Mon planning',
            'pageActive' => 'planning',
            'plannings' => $plannings,
        ], 'benevole');
    }

    public function telechargerPlanning(int $id): void
    {
        $this->verifierAuthentification(['benevole']);

        $response = $this->apiClient->getRaw('/api/benevoles/' . $this->monId() . '/planning/' . $id . '/fichier');
        if ($response['code'] !== 200) {
            http_response_code(404);
            echo 'Planning non disponible.';
            exit;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="planning-' . $id . '.xlsx"');
        header('Content-Length: ' . strlen($response['body']));
        echo $response['body'];
        exit;
    }

    public function profil(): void
    {
        $this->verifierAuthentification(['benevole']);

        $benevole = $this->monBenevole();
        if (!$benevole) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Impossible de charger votre compte.'];
            $this->rediriger('/deconnexion');
            return;
        }

        $this->rendre('frontoffice/benevole/profil', [
            'titre' => 'Mon profil',
            'pageActive' => 'profil',
            'benevole' => $benevole,
        ], 'benevole');
    }

    public function mettreAJourProfil(): void
    {
        $this->verifierAuthentification(['benevole']);
        if (!$this->estPost()) {
            $this->rediriger('/benevole/profil');
            return;
        }

        $data = [
            'nom' => $this->getParam('nom'),
            'prenom' => $this->getParam('prenom'),
            'telephone' => $this->getParam('telephone', ''),
            'adresse' => $this->getParam('adresse', ''),
        ];

        $response = $this->apiClient->put('/api/benevoles/' . $this->monId(), $data);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profil mis à jour.'];
            $_SESSION['user']['nom'] = $data['nom'];
            $_SESSION['user']['prenom'] = $data['prenom'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la mise à jour.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/benevole/profil');
    }

    public function messages(): void
    {
        $this->verifierAuthentification(['benevole']);

        $response = $this->apiClient->get('/api/conversations', ['utilisateur_id' => $this->monId()]);
        $conversations = ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];

        $this->rendre('frontoffice/benevole/messages', [
            'titre' => 'Mes messages',
            'pageActive' => 'messages',
            'conversations' => $conversations,
        ], 'benevole');
    }

    public function nouvelleConversation(): void
    {
        $this->verifierAuthentification(['benevole']);

        $this->rendre('frontoffice/benevole/contact', [
            'titre' => 'Contacter l\'association',
            'pageActive' => 'messages',
        ], 'benevole');
    }

    public function creerConversation(): void
    {
        $this->verifierAuthentification(['benevole']);
        if (!$this->estPost()) {
            $this->rediriger('/benevole/messages/creer');
            return;
        }

        $data = [
            'type' => 'admin',
            'initiateur_id' => $this->monId(),
            'sujet' => $this->getParam('sujet'),
            'contenu' => $this->getParam('contenu'),
        ];

        $response = $this->apiClient->post('/api/conversations', $data);
        if ($response['code'] === 201) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre message a bien été envoyé à l\'association.'];
            $this->rediriger('/benevole/messages/' . (int)$response['data']['id']);
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de l\'envoi du message.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
            $this->rediriger('/benevole/messages/creer');
        }
    }

    /** Récupère une conversation en vérifiant qu'elle appartient bien au bénévole connecté. */
    private function maConversation(int $id): ?array
    {
        $response = $this->apiClient->get('/api/conversations/' . $id);
        if ($response['code'] !== 200 || (int)($response['data']['initiateur_id'] ?? 0) !== $this->monId()) {
            return null;
        }
        return $response['data'];
    }

    public function conversationDetail(int $id): void
    {
        $this->verifierAuthentification(['benevole']);

        $conversation = $this->maConversation($id);
        if (!$conversation) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Conversation introuvable.'];
            $this->rediriger('/benevole/messages');
            return;
        }

        $this->apiClient->post('/api/conversations/' . $id . '/lu?role=utilisateur&utilisateur_id=' . $this->monId(), []);

        $this->rendre('frontoffice/benevole/message_detail', [
            'titre' => $conversation['sujet'],
            'pageActive' => 'messages',
            'conversation' => $conversation,
        ], 'benevole');
    }

    public function repondreConversation(int $id): void
    {
        $this->verifierAuthentification(['benevole']);
        if (!$this->estPost()) {
            $this->rediriger('/benevole/messages/' . $id);
            return;
        }

        $conversation = $this->maConversation($id);
        if (!$conversation) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Conversation introuvable.'];
            $this->rediriger('/benevole/messages');
            return;
        }

        $data = [
            'expediteur_id' => $this->monId(),
            'contenu' => $this->getParam('contenu'),
        ];

        $response = $this->apiClient->post('/api/conversations/' . $id . '/messages', $data);
        if ($response['code'] !== 201) {
            $erreur = $response['data']['error'] ?? 'Erreur lors de l\'envoi de la réponse.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/benevole/messages/' . $id);
    }
}
