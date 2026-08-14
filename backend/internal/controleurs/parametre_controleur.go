package controleurs

import (
	"encoding/json"
	"net/http"

	"nomorewaste/internal/services"
)

type ParametreControleur struct {
	service *services.ParametreService
}

func NouveauParametreControleur(service *services.ParametreService) *ParametreControleur {
	return &ParametreControleur{service: service}
}

func (c *ParametreControleur) ObtenirPrixAdhesion(w http.ResponseWriter, r *http.Request) {
	prix, err := c.service.ObtenirPrixAdhesion()
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]float64{"prix": prix})
}

func (c *ParametreControleur) DefinirPrixAdhesion(w http.ResponseWriter, r *http.Request) {
	var req struct {
		Prix float64 `json:"prix"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	if err := c.service.DefinirPrixAdhesion(req.Prix); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Prix mis à jour"})
}
