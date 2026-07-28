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
        
        // Routes backoffice
        $this->ajouterRoute('GET', '/admin', 'AuthControleur', 'login');
        $this->ajouterRoute('POST', '/admin/login', 'AuthControleur', 'authentifier');
        $this->ajouterRoute('GET', '/admin/dashboard', 'DashboardControleur', 'index');
        $this->ajouterRoute('GET', '/admin/logout', 'AuthControleur', 'deconnecter');
        
        // Gestion des commerçants
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
        $chemin = isset($_GET['url']) ? '/' . trim($_GET['url'], '/') : '/';
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