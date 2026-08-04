<?php

class Routeur
{
    private array $routes = [];
    private string $basePath;
    
    public function __construct()
    {
        // Le chemin de base de ton projet
        $this->basePath = '/nomorewaste';
        $this->initialiserRoutes();
    }
    
    private function initialiserRoutes(): void
    {
        // ============================================
        // FRONTOFFICE - Pages publiques
        // ============================================
        $this->ajouterRoute('GET', '/', 'AccueilControleur', 'index');
        $this->ajouterRoute('GET', '/accueil', 'AccueilControleur', 'index');
        $this->ajouterRoute('GET', '/services', 'ServiceControleur', 'index');
        $this->ajouterRoute('GET', '/adherer', 'AdhesionControleur', 'index');
        
        // ============================================
        // BACKOFFICE - Authentification
        // ============================================
        $this->ajouterRoute('GET', '/admin', 'AuthControleur', 'login');
        $this->ajouterRoute('POST', '/admin/login', 'AuthControleur', 'authentifier');
        $this->ajouterRoute('GET', '/admin/logout', 'AuthControleur', 'deconnecter');
        
        // ============================================
        // BACKOFFICE - Dashboard
        // ============================================
        $this->ajouterRoute('GET', '/admin/dashboard', 'DashboardControleur', 'index');
        
        // ============================================
        // BACKOFFICE - Commerçants
        // ============================================
        $this->ajouterRoute('GET', '/admin/commercants', 'CommercantControleur', 'index');
        $this->ajouterRoute('GET', '/admin/commercants/creer', 'CommercantControleur', 'creer');
        $this->ajouterRoute('POST', '/admin/commercants', 'CommercantControleur', 'enregistrer');
        $this->ajouterRoute('GET', '/admin/commercants/{id}/modifier', 'CommercantControleur', 'modifier');
        $this->ajouterRoute('POST', '/admin/commercants/{id}', 'CommercantControleur', 'mettreAJour');
        $this->ajouterRoute('POST', '/admin/commercants/{id}/supprimer', 'CommercantControleur', 'supprimer');
        $this->ajouterRoute('POST', '/admin/commercants/{id}/renouveler', 'CommercantControleur', 'renouveler');
        
        // ============================================
        // BACKOFFICE - Collectes ⭐ NOUVEAU
        // ============================================
        $this->ajouterRoute('GET', '/admin/collectes', 'CollecteControleur', 'index');
        $this->ajouterRoute('GET', '/admin/collectes/creer', 'CollecteControleur', 'creer');
        $this->ajouterRoute('POST', '/admin/collectes', 'CollecteControleur', 'enregistrer');
        $this->ajouterRoute('GET', '/admin/collectes/{id}/modifier', 'CollecteControleur', 'modifier');
        $this->ajouterRoute('POST', '/admin/collectes/{id}', 'CollecteControleur', 'mettreAJour');
        $this->ajouterRoute('POST', '/admin/collectes/{id}/terminer', 'CollecteControleur', 'terminer');
        $this->ajouterRoute('POST', '/admin/collectes/{id}/supprimer', 'CollecteControleur', 'supprimer');
        
        // ============================================
        // BACKOFFICE - Produits (à venir)
        // ============================================
        $this->ajouterRoute('GET', '/admin/produits', 'ProduitControleur', 'index');
        $this->ajouterRoute('GET', '/admin/produits/scanner', 'ProduitControleur', 'scanner');
        
        // ============================================
        // BACKOFFICE - Bénévoles (à venir)
        // ============================================
        $this->ajouterRoute('GET', '/admin/benevoles', 'BenevoleControleur', 'index');
        $this->ajouterRoute('GET', '/admin/benevoles/candidatures', 'BenevoleControleur', 'candidatures');
        $this->ajouterRoute('POST', '/admin/benevoles/{id}/valider', 'BenevoleControleur', 'valider');
        $this->ajouterRoute('POST', '/admin/benevoles/{id}/refuser', 'BenevoleControleur', 'refuser');
        
        // ============================================
        // BACKOFFICE - Tournées (à venir)
        // ============================================
        $this->ajouterRoute('GET', '/admin/tournees', 'TourneeControleur', 'index');
        $this->ajouterRoute('GET', '/admin/tournees/creer', 'TourneeControleur', 'creer');
        $this->ajouterRoute('POST', '/admin/tournees', 'TourneeControleur', 'enregistrer');
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
        // Récupérer l'URI
        $uri = $_SERVER['REQUEST_URI'];
        
        // Enlever le basePath de l'URI
        if (strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }
        
        // Nettoyer l'URI
        $uri = parse_url($uri, PHP_URL_PATH);
        if ($uri === '' || $uri === '/') {
            $chemin = '/';
        } else {
            $chemin = '/' . ltrim($uri, '/');
        }
        
        $methode = $_SERVER['REQUEST_METHOD'];
        
        // 🔍 DEBUG - Décommente pour voir ce qui se passe
        // echo "BasePath: " . $this->basePath . "<br>";
        // echo "URI: " . $_SERVER['REQUEST_URI'] . "<br>";
        // echo "Chemin: " . $chemin . "<br>";
        // echo "Méthode: " . $methode . "<br>";
        
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
        echo "<p>Chemin demandé : " . htmlspecialchars($chemin) . "</p>";
        echo "<p>URI : " . htmlspecialchars($_SERVER['REQUEST_URI']) . "</p>";
    }
}