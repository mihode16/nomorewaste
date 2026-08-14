package controleurs

import (
	"encoding/json"
	"net/http"
	"strconv"

	"github.com/gorilla/mux"

	"nomorewaste/internal/services"
)

type PlanningControleur struct {
	service *services.PlanningService
}

func NouveauPlanningControleur(service *services.PlanningService) *PlanningControleur {
	return &PlanningControleur{service: service}
}

func (c *PlanningControleur) Generer(w http.ResponseWriter, r *http.Request) {
	benevoleID, _ := strconv.Atoi(mux.Vars(r)["id"])
	var req struct {
		DateDebut string `json:"date_debut"`
		DateFin   string `json:"date_fin"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	id, err := c.service.GenererPlanning(benevoleID, req.DateDebut, req.DateFin)
	if err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]int{"id": id})
}

func (c *PlanningControleur) Lister(w http.ResponseWriter, r *http.Request) {
	benevoleID, _ := strconv.Atoi(mux.Vars(r)["id"])
	list, err := c.service.ListerParBenevole(benevoleID)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(list)
}

func (c *PlanningControleur) Telecharger(w http.ResponseWriter, r *http.Request) {
	benevoleID, _ := strconv.Atoi(mux.Vars(r)["id"])
	planningID, _ := strconv.Atoi(mux.Vars(r)["planningId"])
	data, err := c.service.ObtenirFichier(planningID, benevoleID)
	if err != nil {
		http.Error(w, "Planning non trouvé", http.StatusNotFound)
		return
	}
	w.Header().Set("Content-Type", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
	w.Header().Set("Content-Disposition", "attachment; filename=\"planning-"+strconv.Itoa(planningID)+".xlsx\"")
	w.Write(data)
}
