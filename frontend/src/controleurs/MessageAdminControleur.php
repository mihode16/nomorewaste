<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

class MessageAdminControleur extends Controleur
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

        $filtre = $this->getParam('filtre', '');
        $endpoint = '/api/conversations' . ($filtre !== '' ? '?filtre=' . urlencode($filtre) : '');
        $response = $this->apiClient->get($endpoint);
        $conversations = ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];

        $this->rendre('backoffice/messages/index', [
            'titre' => 'Messages',
            'pageActive' => 'messages',
            'conversations' => $conversations,
            'filtre' => $filtre,
        ]);
    }

    public function detail(int $id): void
    {
        $this->verifierAuthentification();

        $response = $this->apiClient->get('/api/conversations/' . $id);
        if ($response['code'] !== 200) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Conversation introuvable.'];
            $this->rediriger('/admin/messages');
            return;
        }

        $this->apiClient->post('/api/conversations/' . $id . '/lu?role=admin', []);

        $this->rendre('backoffice/messages/detail', [
            'titre' => 'Conversation : ' . ($response['data']['sujet'] ?? ''),
            'pageActive' => 'messages',
            'conversation' => $response['data'],
        ]);
    }

    public function repondre(int $id): void
    {
        $this->verifierAuthentification();
        if (!$this->estPost()) {
            $this->rediriger('/admin/messages/' . $id);
            return;
        }

        $data = [
            'expediteur_id' => (int)($_SESSION['user']['id'] ?? 0),
            'contenu' => $this->getParam('contenu'),
        ];

        $response = $this->apiClient->post('/api/conversations/' . $id . '/messages', $data);
        if ($response['code'] !== 201) {
            $erreur = $response['data']['error'] ?? 'Erreur lors de l\'envoi de la réponse.';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur : ' . $erreur];
        }
        $this->rediriger('/admin/messages/' . $id);
    }

    public function cloturer(int $id): void
    {
        $this->verifierAuthentification();
        if ($this->estPost()) {
            $this->apiClient->post('/api/conversations/' . $id . '/cloturer', []);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Conversation clôturée.'];
        }
        $this->rediriger('/admin/messages/' . $id);
    }
}
