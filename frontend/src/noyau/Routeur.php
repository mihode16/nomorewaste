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
        $this->ajouterRoute('GET', '/lang/{code}', 'LangControleur', 'changer');

        // Authentification
        $this->ajouterRoute('GET', '/admin', 'AuthControleur', 'login');
        $this->ajouterRoute('POST', '/admin/login', 'AuthControleur', 'authentifier');
        $this->ajouterRoute('GET', '/admin/logout', 'AuthControleur', 'deconnecter');

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
        $this->ajouterRoute('POST', '/admin/benevoles', 'BenevoleControleur', 'enregistrer');
        $this->ajouterRoute('GET', '/admin/benevoles/{id}', 'BenevoleControleur', 'detail');
        $this->ajouterRoute('POST', '/admin/benevoles/{id}/statut', 'BenevoleControleur', 'changerStatut');

        // Tournées
        $this->ajouterRoute('GET', '/admin/tournees', 'TourneeControleur', 'index');
        $this->ajouterRoute('GET', '/admin/tournees/creer', 'TourneeControleur', 'creer');
        $this->ajouterRoute('POST', '/admin/tournees', 'TourneeControleur', 'enregistrer');
        $this->ajouterRoute('GET', '/admin/tournees/{id}', 'TourneeControleur', 'detail');
        $this->ajouterRoute('POST', '/admin/tournees/{id}/terminer', 'TourneeControleur', 'terminer');

        // Services
        $this->ajouterRoute('GET', '/admin/services', 'ServicesAdminControleur', 'index');
        $this->ajouterRoute('GET', '/admin/services/plannings/creer', 'ServicesAdminControleur', 'creerPlanning');
        $this->ajouterRoute('POST', '/admin/services/plannings', 'ServicesAdminControleur', 'enregistrerPlanning');

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