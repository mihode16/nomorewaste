package main

import (
	"log"
	"net/http"

	"github.com/gorilla/mux"
	"github.com/rs/cors"

	"nomorewaste/internal/config"
	"nomorewaste/internal/controleurs"
	"nomorewaste/internal/database"
	"nomorewaste/internal/repositories"
	"nomorewaste/internal/services"
)

func main() {
    // Charger la configuration
    config := config.ChargerConfig()

    // Se connecter à la base de données
    if err := database.Connecter(); err != nil {
        log.Fatal("Erreur de connexion à la base de données:", err)
    }
    defer database.Fermer()

    db := database.DB

    // Initialiser les repositories
    commercantRepo := repositories.NouveauCommercantRepository(db)

    // Initialiser les services
    commercantService := services.NouveauCommercantService(commercantRepo)

    // Initialiser les contrôleurs
    commercantControleur := controleurs.NouveauCommercantControleur(commercantService)

    // Créer le routeur
    router := mux.NewRouter()

    // Routes pour les commerçants
    router.HandleFunc("/api/commercants", commercantControleur.Creer).Methods("POST")
    router.HandleFunc("/api/commercants", commercantControleur.Lister).Methods("GET")
    router.HandleFunc("/api/commercants/{id}", commercantControleur.TrouverParID).Methods("GET")
    router.HandleFunc("/api/commercants/{id}", commercantControleur.MettreAJour).Methods("PUT")
    router.HandleFunc("/api/commercants/{id}", commercantControleur.Supprimer).Methods("DELETE")
    router.HandleFunc("/api/commercants/{id}/renouveler", commercantControleur.RenouvelerAdhesion).Methods("POST")
    router.HandleFunc("/api/commercants/adhesions/expirantes", commercantControleur.VerifierAdhesionsExpirantes).Methods("GET")

    // Route de santé
    router.HandleFunc("/api/health", func(w http.ResponseWriter, r *http.Request) {
        w.WriteHeader(http.StatusOK)
        w.Write([]byte(`{"status":"ok"}`))
    }).Methods("GET")

    // Middleware CORS
    c := cors.New(cors.Options{
        AllowedOrigins:   []string{"http://localhost", "http://localhost:8000"},
        AllowedMethods:   []string{"GET", "POST", "PUT", "DELETE", "OPTIONS"},
        AllowedHeaders:   []string{"Content-Type", "Authorization"},
        AllowCredentials: true,
    })

    handler := c.Handler(router)

    // Démarrer le serveur
    addr := ":" + config.APIPort
    log.Printf("🚀 Serveur API démarré sur http://localhost%s", addr)
    log.Fatal(http.ListenAndServe(addr, handler))
}