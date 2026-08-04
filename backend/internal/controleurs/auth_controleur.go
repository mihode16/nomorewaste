package controleurs

import (
	"encoding/json"
	"log"
	"net/http"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/services"
)

type AuthControleur struct {
	service *services.AuthService
}

func NouveauAuthControleur(service *services.AuthService) *AuthControleur {
	return &AuthControleur{service: service}
}

func (c *AuthControleur) Login(w http.ResponseWriter, r *http.Request) {
	var req modeles.LoginRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		log.Printf("❌ Erreur de décodage JSON: %v", err)
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}

	log.Printf("🔐 Tentative de login: email=%s, mot_de_passe=%s", req.Email, req.MotDePasse)

	user, err := c.service.Login(&req)
	if err != nil {
		log.Printf("❌ Échec de l'authentification: %v", err)
		http.Error(w, "Identifiants incorrects", http.StatusUnauthorized)
		return
	}

	log.Printf("✅ Authentification réussie pour %s (id=%d)", user.Email, user.ID)

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]interface{}{
		"id":               user.ID,
		"email":            user.Email,
		"nom":              user.Nom,
		"prenom":           user.Prenom,
		"type_utilisateur": user.TypeUtilisateur,
	})
}