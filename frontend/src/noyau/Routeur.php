<?php

class Routeur
{
    private array $routes = [];
    private string $basePath;

    public function __construct()
    {
        $this->basePath = '/nomorewaste';
        $this->initialiserRoutes();
    }

    private function initialiserRoutes(): void
    {
        // Front office
        $this->ajouterRoute('GET', '/', 'AccueilControleur', 'index');
        $this->ajouterRoute('GET', '/services', 'ServicesPublicControleur', 'index');
        $this->ajouterRoute('GET', '/adherer', 'AdhesionControleur', 'index');
        $this->ajouterRoute('POST', '/adherer', 'AdhesionControleur', 'enregistrer');
        $this->ajouterRoute('GET', '/devenir-benevole', 'BenevoleCandidatureControleur', 'index');
        $this->ajouterRoute('POST', '/devenir-benevole', 'BenevoleCandidatureControleur', 'enregistrer');
        $this->ajouterRoute('GET', '/devenir-commercant', 'CommercantInscriptionControleur', 'index');
        $this->ajouterRoute('POST', '/devenir-commercant', 'CommercantInscriptionControleur', 'enregistrer');
        $this->ajouterRoute('GET', '/mentions-legales', 'InfoControleur', 'mentionsLegales');
        $this->ajouterRoute('GET', '/equipe', 'InfoControleur', 'equipe');

        // Authentification (commune à tous les profils, hors de l'espace /admin)
        $this->ajouterRoute('GET', '/connexion', 'AuthControleur', 'login');
        $this->ajouterRoute('POST', '/connexion', 'AuthControleur', 'authentifier');
        $this->ajouterRoute('GET', '/deconnexion', 'AuthControleur', 'deconnecter');

        // Espace commerçant
        $this->ajouterRoute('GET', '/commercant/dashboard', 'CommercantEspaceControleur', 'dashboard');
        $this->ajouterRoute('GET', '/commercant/collectes', 'CommercantEspaceControleur', 'collectes');
        $this->ajouterRoute('GET', '/commercant/collectes/creer', 'CommercantEspaceControleur', 'creerCollecte');
        $this->ajouterRoute('POST', '/commercant/collectes', 'CommercantEspaceControleur', 'enregistrerCollecte');
        $this->ajouterRoute('GET', '/commercant/collectes/{id}/pdf', 'CommercantEspaceControleur', 'pdf');
        $this->ajouterRoute('GET', '/commercant/collectes/{id}/modifier', 'CommercantEspaceControleur', 'modifierCollecte');
        $this->ajouterRoute('POST', '/commercant/collectes/{id}', 'CommercantEspaceControleur', 'mettreAJourCollecte');
        $this->ajouterRoute('GET', '/commercant/collectes/{id}', 'CommercantEspaceControleur', 'collecteDetail');
        $this->ajouterRoute('GET', '/commercant/adhesion', 'CommercantEspaceControleur', 'adhesion');
        $this->ajouterRoute('POST', '/commercant/adhesion/demander-renouvellement', 'CommercantEspaceControleur', 'demanderRenouvellement');
        $this->ajouterRoute('GET', '/commercant/profil', 'CommercantEspaceControleur', 'profil');
        $this->ajouterRoute('POST', '/commercant/profil', 'CommercantEspaceControleur', 'mettreAJourProfil');
        $this->ajouterRoute('GET', '/commercant/messages', 'CommercantEspaceControleur', 'messages');
        $this->ajouterRoute('GET', '/commercant/messages/creer', 'CommercantEspaceControleur', 'nouvelleConversation');
        $this->ajouterRoute('POST', '/commercant/messages', 'CommercantEspaceControleur', 'creerConversation');
        $this->ajouterRoute('POST', '/commercant/messages/{id}/repondre', 'CommercantEspaceControleur', 'repondreConversation');
        $this->ajouterRoute('GET', '/commercant/messages/{id}', 'CommercantEspaceControleur', 'conversationDetail');

        // Espace adhérent
        $this->ajouterRoute('GET', '/adherent/dashboard', 'AdherentEspaceControleur', 'dashboard');
        $this->ajouterRoute('GET', '/adherent/services', 'AdherentEspaceControleur', 'services');
        $this->ajouterRoute('POST', '/adherent/services/{planningid}/inscrire', 'AdherentEspaceControleur', 'inscrire');
        $this->ajouterRoute('POST', '/adherent/services/{planningid}/desinscrire', 'AdherentEspaceControleur', 'desinscrire');
        $this->ajouterRoute('GET', '/adherent/adhesion', 'AdherentEspaceControleur', 'adhesion');
        $this->ajouterRoute('POST', '/adherent/adhesion/demander-renouvellement', 'AdherentEspaceControleur', 'demanderRenouvellement');
        $this->ajouterRoute('GET', '/adherent/profil', 'AdherentEspaceControleur', 'profil');
        $this->ajouterRoute('POST', '/adherent/profil', 'AdherentEspaceControleur', 'mettreAJourProfil');
        $this->ajouterRoute('GET', '/adherent/messages', 'AdherentEspaceControleur', 'messages');
        $this->ajouterRoute('GET', '/adherent/messages/creer', 'AdherentEspaceControleur', 'nouvelleConversation');
        $this->ajouterRoute('POST', '/adherent/messages', 'AdherentEspaceControleur', 'creerConversation');
        $this->ajouterRoute('POST', '/adherent/messages/{id}/repondre', 'AdherentEspaceControleur', 'repondreConversation');
        $this->ajouterRoute('GET', '/adherent/messages/{id}', 'AdherentEspaceControleur', 'conversationDetail');

        // Espace bénévole
        $this->ajouterRoute('GET', '/benevole/dashboard', 'BenevoleEspaceControleur', 'dashboard');
        $this->ajouterRoute('GET', '/benevole/disponibilites', 'BenevoleEspaceControleur', 'disponibilites');
        $this->ajouterRoute('POST', '/benevole/disponibilites', 'BenevoleEspaceControleur', 'ajouterDisponibilite');
        $this->ajouterRoute('POST', '/benevole/disponibilites/{id}/supprimer', 'BenevoleEspaceControleur', 'supprimerDisponibilite');
        $this->ajouterRoute('GET', '/benevole/affectations', 'BenevoleEspaceControleur', 'affectations');
        $this->ajouterRoute('GET', '/benevole/collectes/{id}', 'BenevoleEspaceControleur', 'collecteDetail');
        $this->ajouterRoute('POST', '/benevole/collectes/{id}/terminer', 'BenevoleEspaceControleur', 'terminerCollecte');
        $this->ajouterRoute('GET', '/benevole/tournees/{id}', 'BenevoleEspaceControleur', 'tourneeDetail');
        $this->ajouterRoute('POST', '/benevole/tournees/{id}/terminer', 'BenevoleEspaceControleur', 'terminerTournee');
        $this->ajouterRoute('GET', '/benevole/planning', 'BenevoleEspaceControleur', 'planning');
        $this->ajouterRoute('GET', '/benevole/planning/{id}/telecharger', 'BenevoleEspaceControleur', 'telechargerPlanning');
        $this->ajouterRoute('GET', '/benevole/profil', 'BenevoleEspaceControleur', 'profil');
        $this->ajouterRoute('POST', '/benevole/profil', 'BenevoleEspaceControleur', 'mettreAJourProfil');
        $this->ajouterRoute('GET', '/benevole/messages', 'BenevoleEspaceControleur', 'messages');
        $this->ajouterRoute('GET', '/benevole/messages/creer', 'BenevoleEspaceControleur', 'nouvelleConversation');
        $this->ajouterRoute('POST', '/benevole/messages', 'BenevoleEspaceControleur', 'creerConversation');
        $this->ajouterRoute('POST', '/benevole/messages/{id}/repondre', 'BenevoleEspaceControleur', 'repondreConversation');
        $this->ajouterRoute('GET', '/benevole/messages/{id}', 'BenevoleEspaceControleur', 'conversationDetail');

        // Backoffice
        $this->ajouterRoute('GET', '/admin/dashboard', 'DashboardControleur', 'index');

        // Commerçants
        $this->ajouterRoute('GET', '/admin/commercants', 'CommercantControleur', 'index');
        $this->ajouterRoute('GET', '/admin/commercants/creer', 'CommercantControleur', 'creer');
        $this->ajouterRoute('POST', '/admin/commercants', 'CommercantControleur', 'enregistrer');
        $this->ajouterRoute('GET', '/admin/commercants/{id}/modifier', 'CommercantControleur', 'modifier');
        $this->ajouterRoute('POST', '/admin/commercants/{id}', 'CommercantControleur', 'mettreAJour');
        $this->ajouterRoute('POST', '/admin/commercants/{id}/supprimer', 'CommercantControleur', 'supprimer');
        $this->ajouterRoute('POST', '/admin/commercants/{id}/renouveler', 'CommercantControleur', 'renouveler');
        $this->ajouterRoute('POST', '/admin/commercants/{id}/valider', 'CommercantControleur', 'valider');

        // Adhérents
        $this->ajouterRoute('GET', '/admin/adherents', 'AdherentControleur', 'index');
        $this->ajouterRoute('GET', '/admin/adherents/creer', 'AdherentControleur', 'creer');
        $this->ajouterRoute('POST', '/admin/adherents', 'AdherentControleur', 'enregistrer');
        $this->ajouterRoute('POST', '/admin/adherents/prix', 'AdherentControleur', 'modifierPrixAdhesion');
        $this->ajouterRoute('GET', '/admin/adherents/{id}/modifier', 'AdherentControleur', 'modifier');
        $this->ajouterRoute('GET', '/admin/adherents/{id}', 'AdherentControleur', 'detail');
        $this->ajouterRoute('POST', '/admin/adherents/{id}', 'AdherentControleur', 'mettreAJour');
        $this->ajouterRoute('POST', '/admin/adherents/{id}/supprimer', 'AdherentControleur', 'supprimer');
        $this->ajouterRoute('POST', '/admin/adherents/{id}/valider', 'AdherentControleur', 'valider');
        $this->ajouterRoute('POST', '/admin/adherents/{id}/renouveler', 'AdherentControleur', 'renouveler');
        $this->ajouterRoute('POST', '/admin/adherents/{id}/abonnements', 'AdherentControleur', 'ajouterAbonnement');
        $this->ajouterRoute('POST', '/admin/adherents/{id}/abonnements/{planningid}/supprimer', 'AdherentControleur', 'supprimerAbonnement');

        // Comptes administrateurs
        $this->ajouterRoute('GET', '/admin/administrateurs', 'AdministrateurControleur', 'index');
        $this->ajouterRoute('GET', '/admin/administrateurs/creer', 'AdministrateurControleur', 'creer');
        $this->ajouterRoute('POST', '/admin/administrateurs', 'AdministrateurControleur', 'enregistrer');
        $this->ajouterRoute('GET', '/admin/administrateurs/{id}/modifier', 'AdministrateurControleur', 'modifier');
        $this->ajouterRoute('POST', '/admin/administrateurs/{id}', 'AdministrateurControleur', 'mettreAJour');
        $this->ajouterRoute('POST', '/admin/administrateurs/{id}/supprimer', 'AdministrateurControleur', 'supprimer');

        // Messages des commerçants
        $this->ajouterRoute('GET', '/admin/messages', 'MessageAdminControleur', 'index');
        $this->ajouterRoute('GET', '/admin/messages/{id}', 'MessageAdminControleur', 'detail');
        $this->ajouterRoute('POST', '/admin/messages/{id}/repondre', 'MessageAdminControleur', 'repondre');
        $this->ajouterRoute('POST', '/admin/messages/{id}/cloturer', 'MessageAdminControleur', 'cloturer');

        // Collectes
        $this->ajouterRoute('GET', '/admin/collectes', 'CollecteControleur', 'index');
        $this->ajouterRoute('GET', '/admin/collectes/creer', 'CollecteControleur', 'creer');
        $this->ajouterRoute('POST', '/admin/collectes', 'CollecteControleur', 'enregistrer');
        $this->ajouterRoute('GET', '/admin/collectes/{id}/modifier', 'CollecteControleur', 'modifier');
        $this->ajouterRoute('POST', '/admin/collectes/{id}', 'CollecteControleur', 'mettreAJour');
        $this->ajouterRoute('POST', '/admin/collectes/{id}/terminer', 'CollecteControleur', 'terminer');
        $this->ajouterRoute('POST', '/admin/collectes/{id}/supprimer', 'CollecteControleur', 'supprimer');
        $this->ajouterRoute('POST', '/admin/collectes/{id}/valider', 'CollecteControleur', 'valider');
        $this->ajouterRoute('GET', '/admin/collectes/{id}', 'CollecteControleur', 'detail');
        $this->ajouterRoute('GET', '/admin/collectes/{id}/benevoles', 'CollecteControleur', 'gererBenevoles');
        $this->ajouterRoute('POST', '/admin/collectes/{id}/benevoles', 'CollecteControleur', 'ajouterBenevole');
        $this->ajouterRoute('POST', '/admin/collectes/{id}/benevoles/{benevoleid}/supprimer', 'CollecteControleur', 'supprimerBenevole');
        $this->ajouterRoute('GET', '/admin/collectes/{id}/pdf', 'CollecteControleur', 'pdf');

        // Produits
        $this->ajouterRoute('GET', '/admin/produits', 'ProduitControleur', 'index');
        $this->ajouterRoute('GET', '/admin/produits/creer', 'ProduitControleur', 'creer');
        $this->ajouterRoute('POST', '/admin/produits', 'ProduitControleur', 'enregistrer');
        $this->ajouterRoute('GET', '/admin/produits/{id}/modifier', 'ProduitControleur', 'modifier');
        $this->ajouterRoute('POST', '/admin/produits/{id}', 'ProduitControleur', 'mettreAJour');
        $this->ajouterRoute('POST', '/admin/produits/{id}/supprimer', 'ProduitControleur', 'supprimer');

        // Bénévoles
        $this->ajouterRoute('GET', '/admin/benevoles', 'BenevoleControleur', 'index');
        $this->ajouterRoute('GET', '/admin/benevoles/creer', 'BenevoleControleur', 'creer');
        $this->ajouterRoute('GET', '/admin/benevoles/calendrier', 'BenevoleControleur', 'calendrier');
        $this->ajouterRoute('POST', '/admin/benevoles', 'BenevoleControleur', 'enregistrer');
        $this->ajouterRoute('GET', '/admin/benevoles/{id}', 'BenevoleControleur', 'detail');
        $this->ajouterRoute('POST', '/admin/benevoles/{id}/statut', 'BenevoleControleur', 'changerStatut');
        $this->ajouterRoute('POST', '/admin/benevoles/{id}/supprimer', 'BenevoleControleur', 'supprimer');
        $this->ajouterRoute('POST', '/admin/benevoles/{id}/planning', 'BenevoleControleur', 'genererPlanning');
        // Routes pour les compétences
        $this->ajouterRoute('POST', '/admin/benevoles/{id}/ajouter-competence', 'BenevoleControleur', 'ajouterCompetence');
        $this->ajouterRoute('POST', '/admin/benevoles/{id}/supprimer-competence', 'BenevoleControleur', 'supprimerCompetence');

        // Tournées
        $this->ajouterRoute('GET', '/admin/tournees', 'TourneeControleur', 'index');
        $this->ajouterRoute('GET', '/admin/tournees/creer', 'TourneeControleur', 'creer');
        $this->ajouterRoute('POST', '/admin/tournees', 'TourneeControleur', 'enregistrer');
        $this->ajouterRoute('GET', '/admin/tournees/{id}', 'TourneeControleur', 'detail');
        $this->ajouterRoute('GET', '/admin/tournees/{id}/modifier', 'TourneeControleur', 'modifier');
        $this->ajouterRoute('POST', '/admin/tournees/{id}', 'TourneeControleur', 'mettreAJour');
        $this->ajouterRoute('POST', '/admin/tournees/{id}/terminer', 'TourneeControleur', 'terminer');
        $this->ajouterRoute('POST', '/admin/tournees/{id}/supprimer', 'TourneeControleur', 'supprimer');
        $this->ajouterRoute('GET', '/admin/tournees/{id}/pdf', 'TourneeControleur', 'pdf');

        // Services
        $this->ajouterRoute('GET', '/admin/services', 'ServicesAdminControleur', 'index');
        $this->ajouterRoute('GET', '/admin/services/plannings/creer', 'ServicesAdminControleur', 'creerPlanning');
        $this->ajouterRoute('POST', '/admin/services/plannings', 'ServicesAdminControleur', 'enregistrerPlanning');
        $this->ajouterRoute('GET', '/admin/services/creer', 'ServicesAdminControleur', 'creerService');
        $this->ajouterRoute('POST', '/admin/services', 'ServicesAdminControleur', 'enregistrerService');
        $this->ajouterRoute('GET', '/admin/services/{id}/modifier', 'ServicesAdminControleur', 'modifierService');
        $this->ajouterRoute('POST', '/admin/services/{id}', 'ServicesAdminControleur', 'mettreAJourService');
        $this->ajouterRoute('POST', '/admin/services/{id}/supprimer', 'ServicesAdminControleur', 'supprimerService');
        $this->ajouterRoute('GET', '/admin/services/plannings/{id}', 'ServicesAdminControleur', 'detailPlanning');
        $this->ajouterRoute('GET', '/admin/services/plannings/{id}/modifier', 'ServicesAdminControleur', 'modifierPlanning');
        $this->ajouterRoute('POST', '/admin/services/plannings/{id}', 'ServicesAdminControleur', 'mettreAJourPlanning');
        $this->ajouterRoute('POST', '/admin/services/plannings/{id}/supprimer', 'ServicesAdminControleur', 'supprimerPlanning');

        // Code-barres
        $this->ajouterRoute('GET', '/barcode/{code}.svg', 'BarcodeControleur', 'generer');    }

    private function ajouterRoute(string $methode, string $chemin, string $controleur, string $action): void
    {
        $this->routes[] = [
            'methode' => $methode,
            'chemin' => $chemin,
            'controleur' => $controleur,
            'action' => $action,
        ];
    }

    public function executer(): void
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';

        $basePath = rtrim(parse_url(BASE_URL, PHP_URL_PATH) ?: BASE_URL, '/');
        if ($basePath !== '' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        if ($uri === '' || $uri === '/') {
            $chemin = '/';
        } else {
            $chemin = '/' . ltrim($uri, '/');
        }

        $methode = $_SERVER['REQUEST_METHOD'];
        foreach ($this->routes as $route) {
            if ($route['methode'] !== $methode) {
                continue;
            }

            $pattern = preg_replace('/\{[a-z]+\}/', '([^/]+)', $route['chemin']);
            if (preg_match('#^' . $pattern . '$#', $chemin, $matches)) {
                array_shift($matches);

                $classeControleur = $route['controleur'];
                $action = $route['action'];
                $fichierControleur = __DIR__ . '/../controleurs/' . $classeControleur . '.php';

                if (file_exists($fichierControleur)) {
                    require_once $fichierControleur;
                    if (class_exists($classeControleur)) {
                        $controleur = new $classeControleur();
                        if (!empty($matches)) {
                            call_user_func_array([$controleur, $action], $matches);
                        } else {
                            $controleur->$action();
                        }
                        return;
                    }
                }
            }
        }

        http_response_code(404);
        echo '<h1>404 - Page non trouvée</h1><p><a href="' . htmlspecialchars(url('/')) . '">Accueil</a></p>';
    }
}