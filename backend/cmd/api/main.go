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
	config := config.ChargerConfig()

	if err := database.Connecter(); err != nil {
		log.Fatal("Erreur de connexion à la base de données:", err)
	}
	defer database.Fermer()

	db := database.DB

	commercantRepo := repositories.NouveauCommercantRepository(db)
	collecteRepo := repositories.NouveauCollecteRepository(db)
	produitRepo := repositories.NouveauProduitRepository(db)
	benevoleRepo := repositories.NouveauBenevoleRepository(db)
	tourneeRepo := repositories.NouveauTourneeRepository(db)
	serviceRepo := repositories.NouveauServiceRepository(db)
	authRepo := repositories.NouveauAuthRepository(db)

	commercantService := services.NouveauCommercantService(commercantRepo)
	collecteService := services.NouveauCollecteService(collecteRepo)
	produitService := services.NouveauProduitService(produitRepo)
	benevoleService := services.NouveauBenevoleService(benevoleRepo)
	tourneeService := services.NouveauTourneeService(tourneeRepo)
	serviceAssociationService := services.NouveauServiceAssociationService(serviceRepo)
	authService := services.NouveauAuthService(authRepo)

	commercantControleur := controleurs.NouveauCommercantControleur(commercantService)
	collecteControleur := controleurs.NouveauCollecteControleur(collecteService)
	produitControleur := controleurs.NouveauProduitControleur(produitService)
	benevoleControleur := controleurs.NouveauBenevoleControleur(benevoleService)
	tourneeControleur := controleurs.NouveauTourneeControleur(tourneeService)
	serviceAssociationControleur := controleurs.NouveauServiceAssociationControleur(serviceAssociationService)
	authControleur := controleurs.NouveauAuthControleur(authService)

	router := mux.NewRouter()

	router.HandleFunc("/api/auth/login", authControleur.Login).Methods("POST")

	router.HandleFunc("/api/commercants", commercantControleur.Creer).Methods("POST")
	router.HandleFunc("/api/commercants", commercantControleur.Lister).Methods("GET")
	router.HandleFunc("/api/commercants/{id}", commercantControleur.TrouverParID).Methods("GET")
	router.HandleFunc("/api/commercants/{id}", commercantControleur.MettreAJour).Methods("PUT")
	router.HandleFunc("/api/commercants/{id}", commercantControleur.Supprimer).Methods("DELETE")
	router.HandleFunc("/api/commercants/{id}/renouveler", commercantControleur.RenouvelerAdhesion).Methods("POST")
	router.HandleFunc("/api/commercants/adhesions/expirantes", commercantControleur.VerifierAdhesionsExpirantes).Methods("GET")

	router.HandleFunc("/api/collectes", collecteControleur.Creer).Methods("POST")
	router.HandleFunc("/api/collectes", collecteControleur.Lister).Methods("GET")
	router.HandleFunc("/api/collectes/{id}", collecteControleur.TrouverParID).Methods("GET")
	router.HandleFunc("/api/collectes/{id}", collecteControleur.MettreAJour).Methods("PUT")
	router.HandleFunc("/api/collectes/{id}/terminer", collecteControleur.Terminer).Methods("POST")
	router.HandleFunc("/api/collectes/{id}", collecteControleur.Supprimer).Methods("DELETE")

	router.HandleFunc("/api/produits", produitControleur.Creer).Methods("POST")
	router.HandleFunc("/api/produits", produitControleur.Lister).Methods("GET")
	router.HandleFunc("/api/produits/{id}", produitControleur.TrouverParID).Methods("GET")
	router.HandleFunc("/api/produits/{id}", produitControleur.MettreAJour).Methods("PUT")
	router.HandleFunc("/api/produits/{id}", produitControleur.Supprimer).Methods("DELETE")

	router.HandleFunc("/api/benevoles", benevoleControleur.Creer).Methods("POST")
	router.HandleFunc("/api/benevoles", benevoleControleur.Lister).Methods("GET")
	router.HandleFunc("/api/benevoles/valides", benevoleControleur.ListerValides).Methods("GET")
	router.HandleFunc("/api/benevoles/{id}", benevoleControleur.TrouverParID).Methods("GET")
	router.HandleFunc("/api/benevoles/{id}/statut", benevoleControleur.ChangerStatut).Methods("POST")
	router.HandleFunc("/api/competences", benevoleControleur.ListerCompetences).Methods("GET")

	router.HandleFunc("/api/tournees", tourneeControleur.Creer).Methods("POST")
	router.HandleFunc("/api/tournees", tourneeControleur.Lister).Methods("GET")
	router.HandleFunc("/api/tournees/{id}", tourneeControleur.TrouverParID).Methods("GET")
	router.HandleFunc("/api/tournees/{id}/terminer", tourneeControleur.Terminer).Methods("POST")
	router.HandleFunc("/api/lieux-distribution", tourneeControleur.ListerLieux).Methods("GET")

	router.HandleFunc("/api/services", serviceAssociationControleur.ListerServices).Methods("GET")
	router.HandleFunc("/api/service-plannings", serviceAssociationControleur.ListerPlannings).Methods("GET")
	router.HandleFunc("/api/service-plannings", serviceAssociationControleur.CreerPlanning).Methods("POST")
	router.HandleFunc("/api/service-plannings/{id}/inscrire", serviceAssociationControleur.Inscrire).Methods("POST")
	router.HandleFunc("/api/adherents", serviceAssociationControleur.ListerAdherents).Methods("GET")
	router.HandleFunc("/api/adherents", serviceAssociationControleur.CreerAdherent).Methods("POST")

	router.HandleFunc("/api/health", func(w http.ResponseWriter, r *http.Request) {
		w.WriteHeader(http.StatusOK)
		w.Write([]byte(`{"status":"ok"}`))
	}).Methods("GET")

	c := cors.New(cors.Options{
		AllowedOrigins:   []string{"http://localhost", "http://localhost:80", "http://127.0.0.1"},
		AllowedMethods:   []string{"GET", "POST", "PUT", "DELETE", "OPTIONS"},
		AllowedHeaders:   []string{"Content-Type", "Authorization"},
		AllowCredentials: true,
	})

	handler := c.Handler(router)
	addr := ":" + config.APIPort
	log.Printf("Serveur API démarré sur http://localhost%s", addr)
	log.Fatal(http.ListenAndServe(addr, handler))
}
