<?php

class Routeur
{
    private array $routes = [];
    
    public function __construct()
    {
        $this->initialiserRoutes();
    }
    
    private function initialiserRoutes(): void
    {
        // Routes frontoffice
        $this->ajouterRoute('GET', '/', 'AccueilControleur', 'index');
        $this->ajouterRoute('GET', '/services', 'ServiceControleur', 'index');
        $this->ajouterRoute('GET', '/adherer', 'AdhesionControleur', 'index');
        
        // Routes backoffice - Authentification
        $this->ajouterRoute('GET', '/admin', 'AuthControleur', 'login');
        $this->ajouterRoute('POST', '/admin/login', 'AuthControleur', 'authentifier');
        $this->ajouterRoute('GET', '/admin/logout', 'AuthControleur', 'deconnecter');
        
        // Routes backoffice - Dashboard
        $this->ajouterRoute('GET', '/admin/dashboard', 'DashboardControleur', 'index');
        
        // Routes backoffice - Commerçants
        $this->ajouterRoute('GET', '/admin/commercants', 'CommercantControleur', 'index');
        $this->ajouterRoute('GET', '/admin/commercants/creer', 'CommercantControleur', 'creer');
        $this->ajouterRoute('POST', '/admin/commercants', 'CommercantControleur', 'enregistrer');
        $this->ajouterRoute('GET', '/admin/commercants/{id}/modifier', 'CommercantControleur', 'modifier');
        $this->ajouterRoute('POST', '/admin/commercants/{id}', 'CommercantControleur', 'mettreAJour');
        $this->ajouterRoute('POST', '/admin/commercants/{id}/supprimer', 'CommercantControleur', 'supprimer');
        $this->ajouterRoute('POST', '/admin/commercants/{id}/renouveler', 'CommercantControleur', 'renouveler');
    }
    
    private function ajouterRoute(string $methode, string $chemin, string $controleur, string $action): void
    {
        $this->routes[] = [
            'methode' => $methode,
            'chemin' => $chemin,
            'controleur' => $controleur,
            'action' => $action
        ];
    }
    
    public function executer(): void
    {
        // Récupérer l'URL sans le sous-dossier
        $uri = $_SERVER['REQUEST_URI'];
        $basePath = '/nomorewaste'; // À ajuster selon ta config
        
        // Si l'URI commence par le basePath, on l'enlève
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Si l'URI est vide ou juste un slash
        if ($uri === '' || $uri === '/') {
            $chemin = '/';
        } else {
            $chemin = '/' . trim($uri, '/');
        }
        
        $methode = $_SERVER['REQUEST_METHOD'];
        
        foreach ($this->routes as $route) {
            if ($route['methode'] !== $methode) {
                continue;
            }
            
            $pattern = preg_replace('/\{[a-z]+\}/', '([0-9]+)', $route['chemin']);
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
        
        // Page 404
        http_response_code(404);
        echo "<h1>404 - Page non trouvée</h1>";
    }
}