package controleurs

import (
	"encoding/json"
	"net/http"
	"strconv"

	"github.com/gorilla/mux"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/services"
)

type ServiceAssociationControleur struct {
	service *services.ServiceAssociationService
}

func NouveauServiceAssociationControleur(service *services.ServiceAssociationService) *ServiceAssociationControleur {
	return &ServiceAssociationControleur{service: service}
}

func (c *ServiceAssociationControleur) ListerServices(w http.ResponseWriter, r *http.Request) {
	list, err := c.service.ListerServices()
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(list)
}

func (c *ServiceAssociationControleur) ListerPlannings(w http.ResponseWriter, r *http.Request) {
	list, err := c.service.ListerPlannings()
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(list)
}

func (c *ServiceAssociationControleur) CreerPlanning(w http.ResponseWriter, r *http.Request) {
	var req modeles.ServicePlanningCreation
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	id, err := c.service.CreerPlanning(&req)
	if err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]int{"id": id})
}

func (c *ServiceAssociationControleur) CreerAdherent(w http.ResponseWriter, r *http.Request) {
	var req modeles.AdherentCreation
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	id, err := c.service.CreerAdherent(&req)
	if err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]int{"id": id})
}

func (c *ServiceAssociationControleur) ListerAdherents(w http.ResponseWriter, r *http.Request) {
	list, err := c.service.ListerAdherents()
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(list)
}

func (c *ServiceAssociationControleur) Inscrire(w http.ResponseWriter, r *http.Request) {
	var req struct {
		AdherentID int `json:"adherent_id"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	planningID, _ := strconv.Atoi(mux.Vars(r)["id"])
	if err := c.service.Inscrire(req.AdherentID, planningID); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Inscription enregistrée"})
}
