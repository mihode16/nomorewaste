package controleurs

import (
	"encoding/json"
	"net/http"
	"strconv"

	"github.com/gorilla/mux"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/services"
)

type DisponibiliteControleur struct {
	service *services.DisponibiliteService
}

func NouveauDisponibiliteControleur(service *services.DisponibiliteService) *DisponibiliteControleur {
	return &DisponibiliteControleur{service: service}
}

func (c *DisponibiliteControleur) ListerParBenevole(w http.ResponseWriter, r *http.Request) {
	benevoleID, _ := strconv.Atoi(mux.Vars(r)["id"])
	list, err := c.service.ListerParBenevole(benevoleID)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(list)
}

func (c *DisponibiliteControleur) ListerToutes(w http.ResponseWriter, r *http.Request) {
	list, err := c.service.ListerToutes()
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(list)
}

func (c *DisponibiliteControleur) Creer(w http.ResponseWriter, r *http.Request) {
	benevoleID, _ := strconv.Atoi(mux.Vars(r)["id"])
	var d modeles.Disponibilite
	if err := json.NewDecoder(r.Body).Decode(&d); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	d.BenevoleID = benevoleID
	id, err := c.service.Creer(&d)
	if err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]int{"id": id})
}

func (c *DisponibiliteControleur) Supprimer(w http.ResponseWriter, r *http.Request) {
	benevoleID, _ := strconv.Atoi(mux.Vars(r)["id"])
	dispoID, _ := strconv.Atoi(mux.Vars(r)["dispoId"])
	if err := c.service.Supprimer(dispoID, benevoleID); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}
