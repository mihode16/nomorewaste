<?php

require_once __DIR__ . '/../noyau/Controleur.php';
require_once __DIR__ . '/../noyau/ClientApi.php';

/** Formulaire public d'inscription commerçant (accessible sans connexion). */
class CommercantInscriptionControleur extends Controleur
{
    private ClientApi $apiClient;

    public function __construct()
    {
        parent::__construct();
        $this->apiClient = new ClientApi();
    }

    public function index(): void
    {
        $this->rendre('frontoffice/contenu/devenir_commercant', [
            'titre' => 'Donner mes invendus',
        ], 'front');
    }

    public function enregistrer(): void
    {
        if (!$this->estPost()) {
            $this->rediriger('/devenir-commercant');
            return;
        }

        $data = [
            'email' => $this->getParam('email'),
            'mot_de_passe' => $this->getParam('mot_de_passe'),
            'nom' => $this->getParam('nom'),
            'prenom' => $this->getParam('prenom'),
            'telephone' => $this->getParam('telephone', ''),
            'adresse' => $this->getParam('adresse', ''),
            'siret' => $this->getParam('siret'),
            'raison_sociale' => $this->getParam('raison_sociale'),
            'type_commerce' => $this->getParam('type_commerce', ''),
            'date_debut_adhesion' => date('Y-m-d'),
            'date_fin_adhesion' => date('Y-m-d', strtotime('+1 year')),
        ];

        $response = $this->apiClient->post('/api/commercants', $data);
        if ($response['code'] === 201 || $response['code'] === 200) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Votre demande d\'inscription commerçant a bien été envoyée ! Elle sera étudiée par nos administrateurs avant validation de votre compte.'];
            $this->rediriger('/');
            return;
        }

        $erreur = $response['data']['error'] ?? 'Erreur lors de l\'envoi de votre demande.';
        $_SESSION['flash'] = ['type' => 'danger', 'message' => $erreur];
        $this->rediriger('/devenir-commercant');
    }
}
