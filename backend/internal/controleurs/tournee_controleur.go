package controleurs

import (
	"encoding/json"
	"fmt"
	"net/http"
	"strconv"

	"github.com/gorilla/mux"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/services"
)

type TourneeControleur struct {
	service *services.TourneeService
}

func NouveauTourneeControleur(service *services.TourneeService) *TourneeControleur {
	return &TourneeControleur{service: service}
}

func (c *TourneeControleur) Creer(w http.ResponseWriter, r *http.Request) {
	var req modeles.TourneeCreation
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	id, err := c.service.Creer(&req)
	if err != nil {
		ecrireErreurJSON(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]int{"id": id})
}

func (c *TourneeControleur) Lister(w http.ResponseWriter, r *http.Request) {
	statut := r.URL.Query().Get("statut")
	destination := r.URL.Query().Get("destination")
	date := r.URL.Query().Get("date")

	list, err := c.service.Lister(statut, destination, date)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(list)
}

func (c *TourneeControleur) TrouverParID(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	t, err := c.service.TrouverParID(id)
	if err != nil {
		http.Error(w, "Tournée non trouvée", http.StatusNotFound)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(t)
}

func (c *TourneeControleur) TelechargerPDF(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	data, err := c.service.ObtenirPDF(id)
	if err != nil {
		http.Error(w, err.Error(), http.StatusNotFound)
		return
	}
	w.Header().Set("Content-Type", "application/pdf")
	w.Header().Set("Content-Disposition", fmt.Sprintf("inline; filename=\"tournee-%d.pdf\"", id))
	w.Write(data)
}

func (c *TourneeControleur) Terminer(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	if err := c.service.Terminer(id); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}

// ConfirmerBenevole permet à un bénévole associé de marquer la tournée comme terminée
// pour lui-même. Une fois que tous les bénévoles associés ont confirmé, la tournée
// bascule automatiquement au statut "Terminée".
func (c *TourneeControleur) ConfirmerBenevole(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	var req struct {
		BenevoleID int `json:"benevole_id"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	if err := c.service.ConfirmerBenevole(id, req.BenevoleID); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Confirmation enregistrée"})
}

func (c *TourneeControleur) ListerLieux(w http.ResponseWriter, r *http.Request) {
	list, err := c.service.ListerLieux()
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(list)
}

func (c *TourneeControleur) AjouterLieu(w http.ResponseWriter, r *http.Request) {
	var req modeles.LieuDistribution
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	id, err := c.service.AjouterLieu(&req)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]int{"id": id})
}

// MettreAJour met à jour une tournée
func (c *TourneeControleur) MettreAJour(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	var req modeles.TourneeUpdate
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	req.ID = id
	if err := c.service.MettreAJour(&req); err != nil {
		ecrireErreurJSON(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}

// Supprimer supprime une tournée
func (c *TourneeControleur) Supprimer(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	if err := c.service.Supprimer(id); err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}