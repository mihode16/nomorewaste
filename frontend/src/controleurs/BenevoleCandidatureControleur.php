<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

/** Formulaire public de candidature bénévole (accessible sans connexion). */
class BenevoleCandidatureControleur extends Controleur
{
    private ClientApi $apiClient;

    public function __construct()
    {
        parent::__construct();
        $this->apiClient = new ClientApi();
    }

    public function index(): void
    {
        $response = $this->apiClient->get('/api/competences');
        $competences = ($response['code'] === 200 && is_array($response['data'])) ? $response['data'] : [];

        $this->rendre('frontoffice/contenu/devenir_benevole', [
            'titre' => 'Devenir bénévole',
            'competences' => $competences,
        ], 'front');
    }

    public function enregistrer(): void
    {
        if (!$this->estPost()) {
            $this->rediriger('/devenir-benevole');
            return;
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
        if ($response['code'] === 201 || $response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre candidature bénévole a bien été envoyée ! Elle sera étudiée par nos administrateurs avant validation de votre compte.'];
            $this->rediriger('/');
            return;
        }

        $erreur = $response['data']['error'] ?? 'Erreur lors de l\'envoi de votre candidature.';
        $_SESSION['flash'] = ['type' => 'danger', 'message' => $erreur];
        $this->rediriger('/devenir-benevole');
    }
}
