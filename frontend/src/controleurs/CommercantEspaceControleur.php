<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

/**
 * Espace self-service des commerçants adhérents : consultation de leurs collectes,
 * de leur adhésion (avec demande de renouvellement) et de leur profil.
 */
class CommercantEspaceControleur extends Controleur
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

    /** Récupère le commerçant courant, ou redirige s'il n'est plus trouvable. */
    private function monCommercant(): ?array
    {
        $response = $this->apiClient->get('/api/commercants/' . $this->monId());
        if ($response['code'] !== 200) {
            return null;
        }
        return $response['data'];
    }

    /** Une adhésion doit être validée par un admin ET non expirée pour planifier une collecte. */
    private function adhesionValide(array $commercant): bool
    {
        return adhesion_est_valide($commercant);
    }

    /** Récupère une collecte en vérifiant qu'elle appartient bien au commerçant connecté. */
    private function maCollecte(int $id): ?array
    {
        $response = $this->apiClient->get('/api/collectes/' . $id);
        if ($response['code'] !== 200 || (int)($response['data']['commercant_id'] ?? 0) !== $this->monId()) {
            return null;
        }
        return $response['data'];
    }

    public function dashboard(): void
    {
        $this->verifierAuthentification(['commercant']);

        $commercant = $this->monCommercant();
        if (!$commercant) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Impossible de charger votre compte.'];
            $this->rediriger('/deconnexion');
            return;
        }

        $collectesResponse = $this->apiClient->get('/api/collectes', ['commercant_id' => $this->monId()]);
        $collectes = ($collectesResponse['code'] === 200 && is_array($collectesResponse['data'])) ? $collectesResponse['data'] : [];

        $nbTotal = count($collectes);
        $nbTerminees = count(array_filter($collectes, fn($c) => ($c['statut'] ?? '') === 'Terminée'));
        $prochaine = null;
        foreach ($collectes as $c) {
            if (($c['statut'] ?? '') !== 'Terminée' && strtotime($c['date_heure_collecte']) >= time()) {
                if ($prochaine === null || strtotime($c['date_heure_collecte']) < strtotime($prochaine['date_heure_collecte'])) {
                    $prochaine = $c;
                }
            }
        }

        $this->rendre('frontoffice/commercant/dashboard', [
            'titre' => 'Tableau de bord',
            'pageActive' => 'dashboard',
            'commercant' => $commercant,
            'nbTotal' => $nbTotal,
            'nbTerminees' => $nbTerminees,
            'prochaine' => $prochaine,
            'dernieresCollectes' => array_slice($collectes, 0, 5),
        ], 'commercant');
    }

    public function collectes(): void
    {
        $this->verifierAuthentification(['commercant']);

        $statut = $this->getParam('statut', '');
        $params = ['commercant_id' => $this->monId()];
        if ($statut !== '') {
            $params['statut'] = $statut;
        }
        $response = $this->apiClient->get('/api/collectes', $params);
        $collectes = ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];

        $commercant = $this->monCommercant();

        $this->rendre('frontoffice/commercant/collectes', [
            'titre' => 'Mes collectes',
            'pageActive' => 'collectes',
            'collectes' => $collectes,
            'filtre_statut' => $statut,
            'peutDemander' => $commercant && $this->adhesionValide($commercant),
        ], 'commercant');
    }

    public function collecteDetail(int $id): void
    {
        $this->verifierAuthentification(['commercant']);

        $collecte = $this->maCollecte($id);
        if (!$collecte) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Collecte introuvable.'];
            $this->rediriger('/commercant/collectes');
            return;
        }

        $produitsResponse = $this->apiClient->get('/api/collectes/' . $id . '/produits');
        $produits = ($produitsResponse['code'] === 200 && is_array($produitsResponse['data'])) ? $produitsResponse['data'] : [];

        $this->rendre('frontoffice/commercant/collecte_detail', [
            'titre' => 'Collecte #' . $id,
            'pageActive' => 'collectes',
            'collecte' => $collecte,
            'produits' => $produits,
        ], 'commercant');
    }

    public function creerCollecte(): void
    {
        $this->verifierAuthentification(['commercant']);

        $commercant = $this->monCommercant();
        if (!$commercant || !$this->adhesionValide($commercant)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Votre adhésion doit être validée et à jour pour planifier une collecte.'];
            $this->rediriger('/commercant/adhesion');
            return;
        }

        $categoriesResponse = $this->apiClient->get('/api/produits/categories');
        $categories = ($categoriesResponse['code'] === 200 && is_array($categoriesResponse['data'])) ? $categoriesResponse['data'] : [];

        $this->rendre('frontoffice/commercant/collecte_form', [
            'titre' => 'Demander une collecte',
            'pageActive' => 'collectes',
            'mode' => 'creer',
            'collecte' => ['adresse_collecte' => $commercant['adresse'] ?? ''],
            'lignes' => [['nom' => '', 'categorie' => '', 'quantite' => 1, 'date_peremption' => '']],
            'categories' => $categories,
        ], 'commercant');
    }

    public function enregistrerCollecte(): void
    {
        $this->verifierAuthentification(['commercant']);
        if (!$this->estPost()) {
            $this->rediriger('/commercant/collectes/creer');
            return;
        }

        $commercant = $this->monCommercant();
        if (!$commercant || !$this->adhesionValide($commercant)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Votre adhésion doit être validée et à jour pour planifier une collecte.'];
            $this->rediriger('/commercant/adhesion');
            return;
        }

        $dt = $this->getParam('date_heure_collecte');
        $data = [
            'date_heure_collecte' => str_replace('T', ' ', $dt) . (strlen($dt) === 16 ? ':00' : ''),
            'adresse_collecte' => $this->getParam('adresse_collecte'),
            'commercant_id' => $this->monId(),
            'commentaire' => $this->getParam('commentaire', ''),
        ];

        $response = $this->apiClient->post('/api/collectes', $data);
        if ($response['code'] !== 201) {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la création de la demande.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
            $this->rediriger('/commercant/collectes/creer');
            return;
        }

        $collecteId = (int)$response['data']['id'];
        $this->enregistrerLignesProduits($collecteId);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre demande de collecte a été envoyée à l\'association pour validation.'];
        $this->rediriger('/commercant/collectes/' . $collecteId);
    }

    public function modifierCollecte(int $id): void
    {
        $this->verifierAuthentification(['commercant']);

        $collecte = $this->maCollecte($id);
        if (!$collecte || !empty($collecte['validee'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Cette demande ne peut plus être modifiée.'];
            $this->rediriger('/commercant/collectes/' . $id);
            return;
        }

        $commercant = $this->monCommercant();
        if (!$commercant || !$this->adhesionValide($commercant)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Votre adhésion doit être validée et à jour pour modifier une collecte.'];
            $this->rediriger('/commercant/adhesion');
            return;
        }

        $produitsResponse = $this->apiClient->get('/api/collectes/' . $id . '/produits');
        $produits = ($produitsResponse['code'] === 200 && is_array($produitsResponse['data'])) ? $produitsResponse['data'] : [];
        $lignes = array_map(fn($p) => [
            'nom' => $p['nom'],
            'categorie' => $p['categorie'] ?? '',
            'quantite' => $p['quantite'],
            'date_peremption' => date('Y-m-d', strtotime($p['date_peremption'])),
        ], $produits);
        if (empty($lignes)) {
            $lignes = [['nom' => '', 'categorie' => '', 'quantite' => 1, 'date_peremption' => '']];
        }

        $categoriesResponse = $this->apiClient->get('/api/produits/categories');
        $categories = ($categoriesResponse['code'] === 200 && is_array($categoriesResponse['data'])) ? $categoriesResponse['data'] : [];

        $this->rendre('frontoffice/commercant/collecte_form', [
            'titre' => 'Modifier ma demande de collecte',
            'pageActive' => 'collectes',
            'mode' => 'modifier',
            'collecte' => $collecte,
            'lignes' => $lignes,
            'categories' => $categories,
        ], 'commercant');
    }

    public function mettreAJourCollecte(int $id): void
    {
        $this->verifierAuthentification(['commercant']);
        if (!$this->estPost()) {
            $this->rediriger('/commercant/collectes/' . $id . '/modifier');
            return;
        }

        $collecte = $this->maCollecte($id);
        if (!$collecte || !empty($collecte['validee'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Cette demande ne peut plus être modifiée.'];
            $this->rediriger('/commercant/collectes/' . $id);
            return;
        }

        $commercant = $this->monCommercant();
        if (!$commercant || !$this->adhesionValide($commercant)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Votre adhésion doit être validée et à jour pour modifier une collecte.'];
            $this->rediriger('/commercant/adhesion');
            return;
        }

        $dt = $this->getParam('date_heure_collecte');
        $data = [
            'date_heure_collecte' => str_replace('T', ' ', $dt) . (strlen($dt) === 16 ? ':00' : ''),
            'adresse_collecte' => $this->getParam('adresse_collecte'),
            'commercant_id' => $this->monId(),
            'commentaire' => $this->getParam('commentaire', ''),
            'statut' => $collecte['statut'] ?? '',
        ];
        $response = $this->apiClient->put('/api/collectes/' . $id, $data);
        if ($response['code'] !== 200) {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la mise à jour de la demande.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
            $this->rediriger('/commercant/collectes/' . $id . '/modifier');
            return;
        }

        // Les lignes de produits sont remplacées : on retire les anciennes puis on
        // recrée celles soumises (même logique que pour les produits/bénévoles de tournée).
        $produitsResponse = $this->apiClient->get('/api/collectes/' . $id . '/produits');
        $anciensProduits = ($produitsResponse['code'] === 200 && is_array($produitsResponse['data'])) ? $produitsResponse['data'] : [];
        foreach ($anciensProduits as $p) {
            $this->apiClient->delete('/api/produits/' . $p['id']);
        }
        $this->enregistrerLignesProduits($id);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre demande de collecte a été mise à jour.'];
        $this->rediriger('/commercant/collectes/' . $id);
    }

    /** Enregistre les lignes de produits (nom[]/categorie[]/quantite[]/date_peremption[]) soumises pour une collecte. */
    private function enregistrerLignesProduits(int $collecteId): void
    {
        $noms = $this->getParam('nom', []);
        $categories = $this->getParam('categorie', []);
        $quantites = $this->getParam('quantite', []);
        $datesPeremption = $this->getParam('date_peremption', []);

        foreach ($noms as $i => $nom) {
            $nom = trim($nom);
            if ($nom === '') {
                continue;
            }
            $this->apiClient->post('/api/produits', [
                'nom' => $nom,
                'categorie' => $categories[$i] ?? '',
                'quantite' => (int)($quantites[$i] ?? 1),
                'date_peremption' => $datesPeremption[$i] ?? '',
                'collecte_id' => $collecteId,
            ]);
        }
    }

    public function messages(): void
    {
        $this->verifierAuthentification(['commercant']);

        $response = $this->apiClient->get('/api/conversations', ['utilisateur_id' => $this->monId()]);
        $conversations = ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];

        $this->rendre('frontoffice/commercant/messages', [
            'titre' => 'Mes messages',
            'pageActive' => 'contact',
            'conversations' => $conversations,
        ], 'commercant');
    }

    public function nouvelleConversation(): void
    {
        $this->verifierAuthentification(['commercant']);

        $collecteId = (int)$this->getParam('collecte_id', 0);
        $sujet = $this->getParam('sujet', '');
        if ($collecteId > 0 && $sujet === '') {
            $sujet = 'À propos de la collecte #' . $collecteId;
        }

        $this->rendre('frontoffice/commercant/contact', [
            'titre' => 'Contacter un admin',
            'pageActive' => 'contact',
            'collecteId' => $collecteId,
            'sujet' => $sujet,
        ], 'commercant');
    }

    public function creerConversation(): void
    {
        $this->verifierAuthentification(['commercant']);
        if (!$this->estPost()) {
            $this->rediriger('/commercant/messages/creer');
            return;
        }

        $data = [
            'type' => 'admin',
            'initiateur_id' => $this->monId(),
            'collecte_id' => (int)$this->getParam('collecte_id', 0),
            'sujet' => $this->getParam('sujet'),
            'contenu' => $this->getParam('contenu'),
        ];

        $response = $this->apiClient->post('/api/conversations', $data);
        if ($response['code'] === 201) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre message a bien été envoyé à l\'association.'];
            $this->rediriger('/commercant/messages/' . (int)$response['data']['id']);
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de l\'envoi du message.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
            $this->rediriger('/commercant/messages/creer');
        }
    }

    /** Récupère une conversation en vérifiant qu'elle appartient bien au commerçant connecté. */
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
        $this->verifierAuthentification(['commercant']);

        $conversation = $this->maConversation($id);
        if (!$conversation) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Conversation introuvable.'];
            $this->rediriger('/commercant/messages');
            return;
        }

        $this->apiClient->post('/api/conversations/' . $id . '/lu?role=utilisateur&utilisateur_id=' . $this->monId(), []);

        $this->rendre('frontoffice/commercant/message_detail', [
            'titre' => $conversation['sujet'],
            'pageActive' => 'contact',
            'conversation' => $conversation,
        ], 'commercant');
    }

    public function repondreConversation(int $id): void
    {
        $this->verifierAuthentification(['commercant']);
        if (!$this->estPost()) {
            $this->rediriger('/commercant/messages/' . $id);
            return;
        }

        $conversation = $this->maConversation($id);
        if (!$conversation) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Conversation introuvable.'];
            $this->rediriger('/commercant/messages');
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
        $this->rediriger('/commercant/messages/' . $id);
    }

    public function pdf(int $id): void
    {
        $this->verifierAuthentification(['commercant']);

        $collecte = $this->maCollecte($id);
        if (!$collecte) {
            http_response_code(404);
            echo 'PDF non disponible.';
            exit;
        }

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

    public function adhesion(): void
    {
        $this->verifierAuthentification(['commercant']);

        $commercant = $this->monCommercant();
        if (!$commercant) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Impossible de charger votre compte.'];
            $this->rediriger('/deconnexion');
            return;
        }

        $this->rendre('frontoffice/commercant/adhesion', [
            'titre' => 'Mon adhésion',
            'pageActive' => 'adhesion',
            'commercant' => $commercant,
        ], 'commercant');
    }

    public function demanderRenouvellement(): void
    {
        $this->verifierAuthentification(['commercant']);
        if (!$this->estPost()) {
            $this->rediriger('/commercant/adhesion');
            return;
        }

        $response = $this->apiClient->post('/api/commercants/' . $this->monId() . '/demander-renouvellement', []);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre demande de renouvellement a bien été envoyée à l\'association.'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la demande.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/commercant/adhesion');
    }

    public function profil(): void
    {
        $this->verifierAuthentification(['commercant']);

        $commercant = $this->monCommercant();
        if (!$commercant) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Impossible de charger votre compte.'];
            $this->rediriger('/deconnexion');
            return;
        }

        $this->rendre('frontoffice/commercant/profil', [
            'titre' => 'Mon profil',
            'pageActive' => 'profil',
            'commercant' => $commercant,
        ], 'commercant');
    }

    public function mettreAJourProfil(): void
    {
        $this->verifierAuthentification(['commercant']);
        if (!$this->estPost()) {
            $this->rediriger('/commercant/profil');
            return;
        }

        $commercant = $this->monCommercant();
        if (!$commercant) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Impossible de charger votre compte.'];
            $this->rediriger('/deconnexion');
            return;
        }

        $data = [
            'nom' => $this->getParam('nom'),
            'prenom' => $this->getParam('prenom'),
            'telephone' => $this->getParam('telephone', ''),
            'adresse' => $this->getParam('adresse', ''),
            'siret' => $commercant['siret'],
            'raison_sociale' => $this->getParam('raison_sociale'),
            'type_commerce' => $this->getParam('type_commerce', ''),
            'date_debut_adhesion' => $commercant['date_debut_adhesion'],
            'date_fin_adhesion' => $commercant['date_fin_adhesion'],
            'est_renouvele_automatiquement' => (bool)$this->getParam('est_renouvele_automatiquement', false),
        ];

        $response = $this->apiClient->put('/api/commercants/' . $this->monId(), $data);
        if ($response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profil mis à jour.'];
            $_SESSION['user']['nom'] = $data['nom'];
            $_SESSION['user']['prenom'] = $data['prenom'];
        } else {
            $erreur = $response['data']['error'] ?? 'Erreur lors de la mise à jour.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/commercant/profil');
    }
}
