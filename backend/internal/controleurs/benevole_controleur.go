package controleurs

import (
	"encoding/json"
	"net/http"
	"strconv"

	"github.com/gorilla/mux"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
	"nomorewaste/internal/services"
)

type BenevoleControleur struct {
	service *services.BenevoleService
}

func NouveauBenevoleControleur(service *services.BenevoleService) *BenevoleControleur {
	return &BenevoleControleur{service: service}
}

func (c *BenevoleControleur) Creer(w http.ResponseWriter, r *http.Request) {
	var req modeles.BenevoleCreation
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	id, err := c.service.Creer(&req)
	if err != nil {
		code := http.StatusBadRequest
		if err == repositories.ErrEmailExiste {
			code = http.StatusConflict
		}
		ecrireErreurJSON(w, err.Error(), code)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]int{"id": id})
}

func (c *BenevoleControleur) Lister(w http.ResponseWriter, r *http.Request) {
	nom := r.URL.Query().Get("nom")
	statut := r.URL.Query().Get("statut")
	competenceID, _ := strconv.Atoi(r.URL.Query().Get("competence_id"))

	list, err := c.service.Lister(nom, statut, competenceID)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(list)
}

func (c *BenevoleControleur) TrouverParID(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	b, err := c.service.TrouverParID(id)
	if err != nil {
		http.Error(w, "Bénévole non trouvé", http.StatusNotFound)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(b)
}

func (c *BenevoleControleur) MettreAJour(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	var req modeles.BenevoleMiseAJour
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	if err := c.service.MettreAJour(id, &req); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}

func (c *BenevoleControleur) ChangerStatut(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	var req struct {
		Statut string `json:"statut"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	if err := c.service.ChangerStatut(id, req.Statut); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}

func (c *BenevoleControleur) ListerCompetences(w http.ResponseWriter, r *http.Request) {
	list, err := c.service.ListerCompetences()
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(list)
}

func (c *BenevoleControleur) ListerValides(w http.ResponseWriter, r *http.Request) {
    list, err := c.service.ListerValides()
    if err != nil {
        http.Error(w, err.Error(), http.StatusInternalServerError)
        return
    }
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(list)
}

func (c *BenevoleControleur) Supprimer(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	if err := c.service.Supprimer(id); err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}

func (c *BenevoleControleur) AjouterCompetence(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	var req struct {
		CompetenceID int `json:"competence_id"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	if err := c.service.AjouterCompetence(id, req.CompetenceID); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}

func (c *BenevoleControleur) SupprimerCompetence(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	var req struct {
		CompetenceID int `json:"competence_id"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	if err := c.service.SupprimerCompetence(id, req.CompetenceID); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}