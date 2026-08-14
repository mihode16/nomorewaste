package controleurs

import (
	"encoding/json"
	"errors"
	"net/http"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
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
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}

	user, err := c.service.Login(&req)
	if err != nil {
		if errors.Is(err, repositories.ErrCompteEnAttente) {
			w.Header().Set("Content-Type", "application/json")
			w.WriteHeader(http.StatusForbidden)
			json.NewEncoder(w).Encode(map[string]string{
				"error": "Votre inscription est encore en cours d'étude par nos équipes. Vous pourrez vous connecter dès qu'elle sera validée.",
			})
			return
		}
		http.Error(w, "Identifiants incorrects", http.StatusUnauthorized)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]interface{}{
		"id":               user.ID,
		"email":            user.Email,
		"nom":              user.Nom,
		"prenom":           user.Prenom,
		"type_utilisateur": user.TypeUtilisateur,
	})
}
