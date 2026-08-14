<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

/**
 * Espace self-service des adhérents : consultation/renouvellement (simulé) de leur adhésion,
 * inscription aux services proposés (ateliers...), messagerie avec l'association et entre
 * adhérents, et profil.
 */
class AdherentEspaceControleur extends Controleur
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

    /** Récupère l'adhérent courant, ou redirige s'il n'est plus trouvable. */
    private function monAdherent(): ?array
    {
        $response = $this->apiClient->get('/api/adherents/' . $this->monId());
        if ($response['code'] !== 200) {
            return null;
        }
        return $response['data'];
    }

    private function mesInscriptions(): array
    {
        $response = $this->apiClient->get('/api/adherents/' . $this->monId() . '/inscriptions');
        return ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];
    }

    /** Une adhésion doit être validée par un admin ET non expirée pour s'inscrire à un service. */
    private function adhesionValide(array $adherent): bool
    {
        return adhesion_est_valide($adherent);
    }

    public function dashboard(): void
    {
        $this->verifierAuthentification(['adherent']);

        $adherent = $this->monAdherent();
        if (!$adherent) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Impossible de charger votre compte.'];
            $this->rediriger('/deconnexion');
            return;
        }

        $inscriptions = $this->mesInscriptions();
        $prochaines = array_filter($inscriptions, fn($p) => strtotime($p['date_heure_debut']) >= time());
        usort($prochaines, fn($a, $b) => strtotime($a['date_heure_debut']) <=> strtotime($b['date_heure_debut']));

        $conversationsResponse = $this->apiClient->get('/api/conversations', ['utilisateur_id' => $this->monId()]);
        $conversations = ($conversationsResponse['code'] === 200 && is_array($conversationsResponse['data'])) ? $conversationsResponse['data'] : [];
        $nbNonLus = array_sum(array_column($conversations, 'nb_non_lus'));

        $this->rendre('frontoffice/adherent/dashboard', [
            'titre' => 'Tableau de bord',
            'pageActive' => 'dashboard',
            'adherent' => $adherent,
            'nbInscriptions' => count($inscriptions),
            'prochainesSeances' => array_slice($prochaines, 0, 5),
            'nbMessagesNonLus' => $nbNonLus,
        ], 'adherent');
    }

    public function services(): void
    {
        $this->verifierAuthentification(['adherent']);

        $servicesResponse = $this->apiClient->get('/api/services');
        $services = ($servicesResponse['code'] === 200 && is_array($servicesResponse['data'])) ? $servicesResponse['data'] : [];

        $planningsResponse = $this->apiClient->get('/api/service-plannings');
        $plannings = ($planningsResponse['code'] === 200 && is_array($planningsResponse['data'])) ? $planningsResponse['data'] : [];

        $inscriptions = $this->mesInscriptions();
        $inscritIds = array_column($inscriptions, 'id');

        // Séances à venir uniquement, triées par date.
        $plannings = array_filter($plannings, fn($p) => strtotime($p['date_heure_debut']) >= time());
        usort($plannings, fn($a, $b) => strtotime($a['date_heure_debut']) <=> strtotime($b['date_heure_debut']));

        $adherent = $this->monAdherent();

        $this->rendre('frontoffice/adherent/services', [
            'titre' => 'Services & ateliers',
            'pageActive' => 'services',
            'services' => $services,
            'plannings' => $plannings,
            'inscritIds' => $inscritIds,
            'peutInscrire' => $adherent && $this->adhesionValide($adherent),
        ], 'adherent');
    }

    public function inscrire(int $planningId): void
    {
        $this->verifierAuthentification(['adherent']);
        if ($this->estPost()) {
            $adherent = $this->monAdherent();
            if (!$adherent || !$this->adhesionValide($adherent)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Votre adhésion doit être validée et à jour pour vous inscrire à un service.'];
                $this->rediriger('/adherent/adhesion');
                return;
            }

            $response = $this->apiClient->post('/api/service-plannings/' . $planningId . '/inscrire', ['adherent_id' => $this->monId()]);
            if ($response['code'] === 200) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Inscription confirmée.'];
            } else {
                $erreur = $response['data']['error'] ?? 'Erreur lors de l\'inscription.';
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
            }
        }
        $this->rediriger('/adherent/services');
    }

    public function desinscrire(int $planningId): void
    {
        $this->verifierAuthentification(['adherent']);
        if ($this->estPost()) {
            $this->apiClient->delete('/api/adherents/' . $this->monId() . '/inscriptions/' . $planningId);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Désinscription effectuée.'];
        }
        $this->rediriger('/adherent/services');
    }

    public function adhesion(): void
    {
        $this->verifierAuthentification(['adherent']);

        $adherent = $this->monAdherent();
        if (!$adherent) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Impossible de charger votre compte.'];
            $this->rediriger('/deconnexion');
            return;
        }

        $prixResponse = $this->apiClient->get('/api/parametres/prix-adhesion');
        $prixMensuel = ($prixResponse['code'] === 200 && isset($prixResponse['data']['prix'])) ? (float)$prixResponse['data']['prix'] : 7.99;

        $this->rendre('frontoffice/adherent/adhesion', [
            'titre' => 'Mon adhésion',
            'pageActive' => 'adhesion',
            'adherent' => $adherent,
            'prixMensuel' => $prixMensuel,
        ], 'adherent');
    }

    /**
     * Simule le paiement d'un renouvellement d'adhésion (aucun prestataire de paiement n'est
     * réellement intégré) : la demande est enregistrée côté association, qui la confirme
     * ensuite en validant réellement le renouvellement depuis le backoffice.
     */
    public function demanderRenouvellement(): void
    {
        $this->verifierAuthentification(['adherent']);
        if (!$this->estPost()) {
            $this->rediriger('/adherent/adhesion');
            return;
        }

        $response = $this->apiClient->post('/api/adherents/' . $this->monId() . '/demander-renouvellement', []);
        if ($response['code'] === 200) {
            $dureeMois = (int)$this->getParam('duree_mois', 12);
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Paiement simulé accepté ! Votre renouvellement de ' . $dureeMois . ' mois est en attente de confirmation par un administrateur.',
            ];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la demande.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/adherent/adhesion');
    }

    public function messages(): void
    {
        $this->verifierAuthentification(['adherent']);

        $response = $this->apiClient->get('/api/conversations', ['utilisateur_id' => $this->monId()]);
        $conversations = ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];

        $avecAssociation = array_values(array_filter($conversations, fn($c) => $c['type'] === 'admin'));
        $entreAdherents = array_values(array_filter($conversations, fn($c) => $c['type'] === 'pair'));

        $this->rendre('frontoffice/adherent/messages', [
            'titre' => 'Mes messages',
            'pageActive' => 'messages',
            'avecAssociation' => $avecAssociation,
            'entreAdherents' => $entreAdherents,
            'monId' => $this->monId(),
        ], 'adherent');
    }

    public function nouvelleConversation(): void
    {
        $this->verifierAuthentification(['adherent']);

        $autresAdherentsResponse = $this->apiClient->get('/api/adherents', ['statut' => 'valide']);
        $autresAdherents = ($autresAdherentsResponse['code'] === 200 && is_array($autresAdherentsResponse['data'])) ? $autresAdherentsResponse['data'] : [];
        $autresAdherents = array_values(array_filter($autresAdherents, fn($a) => (int)$a['id'] !== $this->monId()));

        $this->rendre('frontoffice/adherent/message_nouveau', [
            'titre' => 'Nouveau message',
            'pageActive' => 'messages',
            'autresAdherents' => $autresAdherents,
        ], 'adherent');
    }

    public function creerConversation(): void
    {
        $this->verifierAuthentification(['adherent']);
        if (!$this->estPost()) {
            $this->rediriger('/adherent/messages/creer');
            return;
        }

        $destinataireId = (int)$this->getParam('destinataire_id', 0);
        $data = [
            'type' => $destinataireId > 0 ? 'pair' : 'admin',
            'initiateur_id' => $this->monId(),
            'destinataire_id' => $destinataireId,
            'sujet' => $this->getParam('sujet'),
            'contenu' => $this->getParam('contenu'),
        ];

        $response = $this->apiClient->post('/api/conversations', $data);
        if ($response['code'] === 201) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Message envoyé.'];
            $this->rediriger('/adherent/messages/' . (int)$response['data']['id']);
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de l\'envoi du message.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
            $this->rediriger('/adherent/messages/creer');
        }
    }

    /** Récupère une conversation en vérifiant que l'adhérent connecté y participe. */
    private function maConversation(int $id): ?array
    {
        $response = $this->apiClient->get('/api/conversations/' . $id);
        if ($response['code'] !== 200) {
            return null;
        }
        $c = $response['data'];
        $participe = (int)($c['initiateur_id'] ?? 0) === $this->monId() || (int)($c['destinataire_id'] ?? 0) === $this->monId();
        return $participe ? $c : null;
    }

    public function conversationDetail(int $id): void
    {
        $this->verifierAuthentification(['adherent']);

        $conversation = $this->maConversation($id);
        if (!$conversation) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Conversation introuvable.'];
            $this->rediriger('/adherent/messages');
            return;
        }

        $this->apiClient->post('/api/conversations/' . $id . '/lu?role=utilisateur&utilisateur_id=' . $this->monId(), []);

        $this->rendre('frontoffice/adherent/message_detail', [
            'titre' => $conversation['sujet'],
            'pageActive' => 'messages',
            'conversation' => $conversation,
            'monId' => $this->monId(),
        ], 'adherent');
    }

    public function repondreConversation(int $id): void
    {
        $this->verifierAuthentification(['adherent']);
        if (!$this->estPost()) {
            $this->rediriger('/adherent/messages/' . $id);
            return;
        }

        $conversation = $this->maConversation($id);
        if (!$conversation) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Conversation introuvable.'];
            $this->rediriger('/adherent/messages');
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
        $this->rediriger('/adherent/messages/' . $id);
    }

    public function profil(): void
    {
        $this->verifierAuthentification(['adherent']);

        $adherent = $this->monAdherent();
        if (!$adherent) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Impossible de charger votre compte.'];
            $this->rediriger('/deconnexion');
            return;
        }

        $this->rendre('frontoffice/adherent/profil', [
            'titre' => 'Mon profil',
            'pageActive' => 'profil',
            'adherent' => $adherent,
        ], 'adherent');
    }

    public function mettreAJourProfil(): void
    {
        $this->verifierAuthentification(['adherent']);
        if (!$this->estPost()) {
            $this->rediriger('/adherent/profil');
            return;
        }

        $adherent = $this->monAdherent();
        if (!$adherent) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Impossible de charger votre compte.'];
            $this->rediriger('/deconnexion');
            return;
        }

        $data = [
            'nom' => $this->getParam('nom'),
            'prenom' => $this->getParam('prenom'),
            'telephone' => $this->getParam('telephone', ''),
            'adresse' => $this->getParam('adresse', ''),
            'date_debut_adhesion' => date('Y-m-d', strtotime($adherent['date_debut_adhesion'])),
            'date_fin_adhesion' => date('Y-m-d', strtotime($adherent['date_fin_adhesion'])),
            'est_renouvele_automatiquement' => (bool)$this->getParam('est_renouvele_automatiquement', false),
        ];

        $response = $this->apiClient->put('/api/adherents/' . $this->monId(), $data);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profil mis à jour.'];
            $_SESSION['user']['nom'] = $data['nom'];
            $_SESSION['user']['prenom'] = $data['prenom'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la mise à jour.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/adherent/profil');
    }
}
