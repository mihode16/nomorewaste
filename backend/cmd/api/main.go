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

	if err := database.Connecter(config); err != nil {
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
	parametreRepo := repositories.NouveauParametreRepository(db)
	responsableRepo := repositories.NouveauResponsableRepository(db)
	messageRepo := repositories.NouveauMessageRepository(db)
	disponibiliteRepo := repositories.NouveauDisponibiliteRepository(db)
	planningRepo := repositories.NouveauPlanningRepository(db)

	recapService := services.NouveauRecapService(produitRepo, config.LogoPath, "storage/pdfs")

	commercantService := services.NouveauCommercantService(commercantRepo)
	collecteService := services.NouveauCollecteService(collecteRepo, recapService)
	produitService := services.NouveauProduitService(produitRepo)
	benevoleService := services.NouveauBenevoleService(benevoleRepo)
	tourneeService := services.NouveauTourneeService(tourneeRepo, recapService)
	serviceAssociationService := services.NouveauServiceAssociationService(serviceRepo)
	authService := services.NouveauAuthService(authRepo)
	parametreService := services.NouveauParametreService(parametreRepo)
	responsableService := services.NouveauResponsableService(responsableRepo)
	messageService := services.NouveauMessageService(messageRepo)
	disponibiliteService := services.NouveauDisponibiliteService(disponibiliteRepo)
	planningService := services.NouveauPlanningService(planningRepo, benevoleRepo, collecteRepo, tourneeRepo, serviceRepo, "storage/plannings")

	commercantControleur := controleurs.NouveauCommercantControleur(commercantService)
	collecteControleur := controleurs.NouveauCollecteControleur(collecteService)
	produitControleur := controleurs.NouveauProduitControleur(produitService)
	benevoleControleur := controleurs.NouveauBenevoleControleur(benevoleService)
	tourneeControleur := controleurs.NouveauTourneeControleur(tourneeService)
	serviceAssociationControleur := controleurs.NouveauServiceAssociationControleur(serviceAssociationService)
	authControleur := controleurs.NouveauAuthControleur(authService)
	parametreControleur := controleurs.NouveauParametreControleur(parametreService)
	responsableControleur := controleurs.NouveauResponsableControleur(responsableService)
	messageControleur := controleurs.NouveauMessageControleur(messageService)
	disponibiliteControleur := controleurs.NouveauDisponibiliteControleur(disponibiliteService)
	planningControleur := controleurs.NouveauPlanningControleur(planningService)

	router := mux.NewRouter()

	router.HandleFunc("/api/auth/login", authControleur.Login).Methods("POST")

	// Routes spécifiques (sans paramètre)
	router.HandleFunc("/api/commercants/types", commercantControleur.ListeTypes).Methods("GET")
	router.HandleFunc("/api/commercants", commercantControleur.Lister).Methods("GET")
	router.HandleFunc("/api/commercants", commercantControleur.Creer).Methods("POST")
	// Routes avec paramètre {id}
	router.HandleFunc("/api/commercants/{id}/valider", commercantControleur.Valider).Methods("POST")
	router.HandleFunc("/api/commercants/{id}/renouveler", commercantControleur.RenouvelerAdhesion).Methods("POST")
	router.HandleFunc("/api/commercants/{id}/demander-renouvellement", commercantControleur.DemanderRenouvellement).Methods("POST")
	router.HandleFunc("/api/commercants/{id}", commercantControleur.TrouverParID).Methods("GET")
	router.HandleFunc("/api/commercants/{id}", commercantControleur.MettreAJour).Methods("PUT")
	router.HandleFunc("/api/commercants/{id}", commercantControleur.Supprimer).Methods("DELETE")


	router.HandleFunc("/api/collectes", collecteControleur.Creer).Methods("POST")
	router.HandleFunc("/api/collectes", collecteControleur.Lister).Methods("GET")
	router.HandleFunc("/api/collectes/{id}", collecteControleur.TrouverParID).Methods("GET")
	router.HandleFunc("/api/collectes/{id}/valider", collecteControleur.Valider).Methods("POST")
	router.HandleFunc("/api/collectes/{id}", collecteControleur.MettreAJour).Methods("PUT")
	router.HandleFunc("/api/collectes/{id}/terminer", collecteControleur.Terminer).Methods("POST")
	router.HandleFunc("/api/collectes/{id}", collecteControleur.Supprimer).Methods("DELETE")
	router.HandleFunc("/api/collectes/{id}/produits", produitControleur.ListerParCollecte).Methods("GET")
	router.HandleFunc("/api/collectes/{id}/benevoles", collecteControleur.AjouterBenevole).Methods("POST")
	router.HandleFunc("/api/collectes/{id}/benevoles/{benevoleId}", collecteControleur.SupprimerBenevole).Methods("DELETE")
	router.HandleFunc("/api/collectes/{id}/confirmer", collecteControleur.ConfirmerBenevole).Methods("POST")
	router.HandleFunc("/api/collectes/{id}/pdf", collecteControleur.TelechargerPDF).Methods("GET")

	router.HandleFunc("/api/produits/categories", produitControleur.ListerCategories).Methods("GET")
	router.HandleFunc("/api/produits", produitControleur.Creer).Methods("POST")
	router.HandleFunc("/api/produits", produitControleur.Lister).Methods("GET")
	router.HandleFunc("/api/produits/{id}", produitControleur.TrouverParID).Methods("GET")
	router.HandleFunc("/api/produits/{id}", produitControleur.MettreAJour).Methods("PUT")
	router.HandleFunc("/api/produits/{id}", produitControleur.Supprimer).Methods("DELETE")


	router.HandleFunc("/api/benevoles", benevoleControleur.Lister).Methods("GET")
	router.HandleFunc("/api/benevoles", benevoleControleur.Creer).Methods("POST")
	router.HandleFunc("/api/benevoles/valides", benevoleControleur.ListerValides).Methods("GET")
	router.HandleFunc("/api/benevoles/{id}", benevoleControleur.TrouverParID).Methods("GET")
	router.HandleFunc("/api/benevoles/{id}", benevoleControleur.MettreAJour).Methods("PUT")
	router.HandleFunc("/api/benevoles/{id}", benevoleControleur.Supprimer).Methods("DELETE")
	router.HandleFunc("/api/benevoles/{id}/statut", benevoleControleur.ChangerStatut).Methods("POST")
	router.HandleFunc("/api/benevoles/{id}/competences", benevoleControleur.AjouterCompetence).Methods("POST")
	router.HandleFunc("/api/benevoles/{id}/competences", benevoleControleur.SupprimerCompetence).Methods("DELETE")
	router.HandleFunc("/api/competences", benevoleControleur.ListerCompetences).Methods("GET")

	router.HandleFunc("/api/disponibilites", disponibiliteControleur.ListerToutes).Methods("GET")
	router.HandleFunc("/api/benevoles/{id}/disponibilites", disponibiliteControleur.ListerParBenevole).Methods("GET")
	router.HandleFunc("/api/benevoles/{id}/disponibilites", disponibiliteControleur.Creer).Methods("POST")
	router.HandleFunc("/api/benevoles/{id}/disponibilites/{dispoId}", disponibiliteControleur.Supprimer).Methods("DELETE")

	router.HandleFunc("/api/benevoles/{id}/planning", planningControleur.Generer).Methods("POST")
	router.HandleFunc("/api/benevoles/{id}/planning", planningControleur.Lister).Methods("GET")
	router.HandleFunc("/api/benevoles/{id}/planning/{planningId}/fichier", planningControleur.Telecharger).Methods("GET")
	

// Tournées
	router.HandleFunc("/api/tournees", tourneeControleur.Lister).Methods("GET")
	router.HandleFunc("/api/tournees", tourneeControleur.Creer).Methods("POST")
	router.HandleFunc("/api/tournees/{id}", tourneeControleur.TrouverParID).Methods("GET")
	router.HandleFunc("/api/tournees/{id}", tourneeControleur.MettreAJour).Methods("PUT")
	router.HandleFunc("/api/tournees/{id}", tourneeControleur.Supprimer).Methods("DELETE")
	router.HandleFunc("/api/tournees/{id}/terminer", tourneeControleur.Terminer).Methods("POST")
	router.HandleFunc("/api/tournees/{id}/confirmer", tourneeControleur.ConfirmerBenevole).Methods("POST")
	router.HandleFunc("/api/tournees/{id}/pdf", tourneeControleur.TelechargerPDF).Methods("GET")
	router.HandleFunc("/api/lieux-distribution", tourneeControleur.ListerLieux).Methods("GET")
	router.HandleFunc("/api/lieux-distribution", tourneeControleur.AjouterLieu).Methods("POST")
	

	router.HandleFunc("/api/services", serviceAssociationControleur.ListerServices).Methods("GET")
	router.HandleFunc("/api/services", serviceAssociationControleur.CreerService).Methods("POST")
	router.HandleFunc("/api/services/{id}", serviceAssociationControleur.TrouverServiceParID).Methods("GET")
	router.HandleFunc("/api/services/{id}", serviceAssociationControleur.MettreAJourService).Methods("PUT")
	router.HandleFunc("/api/services/{id}", serviceAssociationControleur.SupprimerService).Methods("DELETE")
	router.HandleFunc("/api/service-plannings", serviceAssociationControleur.ListerPlannings).Methods("GET")
	router.HandleFunc("/api/service-plannings", serviceAssociationControleur.CreerPlanning).Methods("POST")
	router.HandleFunc("/api/service-plannings/{id}", serviceAssociationControleur.TrouverPlanningParID).Methods("GET")
	router.HandleFunc("/api/service-plannings/{id}", serviceAssociationControleur.MettreAJourPlanning).Methods("PUT")
	router.HandleFunc("/api/service-plannings/{id}", serviceAssociationControleur.SupprimerPlanning).Methods("DELETE")
	router.HandleFunc("/api/service-plannings/{id}/inscrire", serviceAssociationControleur.Inscrire).Methods("POST")
	router.HandleFunc("/api/adherents", serviceAssociationControleur.ListerAdherents).Methods("GET")
	router.HandleFunc("/api/adherents", serviceAssociationControleur.CreerAdherent).Methods("POST")
	router.HandleFunc("/api/adherents/adhesions/expirantes", serviceAssociationControleur.VerifierAdhesionsAdherentExpirantes).Methods("GET")
	router.HandleFunc("/api/adherents/{id}/valider", serviceAssociationControleur.ValiderAdhesionAdherent).Methods("POST")
	router.HandleFunc("/api/adherents/{id}/renouveler", serviceAssociationControleur.RenouvelerAdhesionAdherent).Methods("POST")
	router.HandleFunc("/api/adherents/{id}/demander-renouvellement", serviceAssociationControleur.DemanderRenouvellementAdherent).Methods("POST")
	router.HandleFunc("/api/adherents/{id}/inscriptions", serviceAssociationControleur.ListerInscriptionsParAdherent).Methods("GET")
	router.HandleFunc("/api/adherents/{id}/inscriptions/{planningId}", serviceAssociationControleur.SupprimerInscription).Methods("DELETE")
	router.HandleFunc("/api/adherents/{id}", serviceAssociationControleur.TrouverAdherentParID).Methods("GET")
	router.HandleFunc("/api/adherents/{id}", serviceAssociationControleur.MettreAJourAdherent).Methods("PUT")
	router.HandleFunc("/api/adherents/{id}", serviceAssociationControleur.SupprimerAdherent).Methods("DELETE")

	router.HandleFunc("/api/parametres/prix-adhesion", parametreControleur.ObtenirPrixAdhesion).Methods("GET")
	router.HandleFunc("/api/parametres/prix-adhesion", parametreControleur.DefinirPrixAdhesion).Methods("PUT")

	router.HandleFunc("/api/administrateurs", responsableControleur.Lister).Methods("GET")
	router.HandleFunc("/api/administrateurs", responsableControleur.Creer).Methods("POST")
	router.HandleFunc("/api/administrateurs/{id}", responsableControleur.TrouverParID).Methods("GET")
	router.HandleFunc("/api/administrateurs/{id}", responsableControleur.MettreAJour).Methods("PUT")
	router.HandleFunc("/api/administrateurs/{id}", responsableControleur.Supprimer).Methods("DELETE")

	router.HandleFunc("/api/conversations/non-lus", messageControleur.CompterNonLus).Methods("GET")
	router.HandleFunc("/api/conversations", messageControleur.Lister).Methods("GET")
	router.HandleFunc("/api/conversations", messageControleur.Creer).Methods("POST")
	router.HandleFunc("/api/conversations/{id}", messageControleur.TrouverParID).Methods("GET")
	router.HandleFunc("/api/conversations/{id}/messages", messageControleur.AjouterMessage).Methods("POST")
	router.HandleFunc("/api/conversations/{id}/cloturer", messageControleur.Cloturer).Methods("POST")
	router.HandleFunc("/api/conversations/{id}/lu", messageControleur.MarquerLu).Methods("POST")

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
