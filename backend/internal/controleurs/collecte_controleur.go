package controleurs

import (
	"encoding/json"
	"fmt"
	"net/http"
	"strconv"

	"github.com/gorilla/mux"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/services"
	"nomorewaste/internal/utils"
)

type CollecteControleur struct {
	service *services.CollecteService
}

func NouveauCollecteControleur(service *services.CollecteService) *CollecteControleur {
	return &CollecteControleur{service: service}
}

func (c *CollecteControleur) Creer(w http.ResponseWriter, r *http.Request) {
	var req modeles.CollecteCreation
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
	json.NewEncoder(w).Encode(map[string]interface{}{
		"id":      id,
		"message": "Collecte créée avec succès",
	})
}

func (c *CollecteControleur) Lister(w http.ResponseWriter, r *http.Request) {
	commercant := r.URL.Query().Get("commercant")
	statut := r.URL.Query().Get("statut")
	dateDebut := r.URL.Query().Get("date_debut")
	dateFin := r.URL.Query().Get("date_fin")
	commercantID, _ := strconv.Atoi(r.URL.Query().Get("commercant_id"))

	collectes, err := c.service.Lister(commercant, statut, dateDebut, dateFin, commercantID)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(collectes)
}

func (c *CollecteControleur) TrouverParID(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	idStr := vars["id"]
	id, err := strconv.Atoi(idStr)
	if err != nil {
		http.Error(w, "ID invalide", http.StatusBadRequest)
		return
	}

	collecte, err := c.service.TrouverParID(id)
	if err != nil {
		http.Error(w, "Collecte non trouvée", http.StatusNotFound)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(collecte)
}

func (c *CollecteControleur) MettreAJour(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	idStr := vars["id"]
	id, err := strconv.Atoi(idStr)
	if err != nil {
		http.Error(w, "ID invalide", http.StatusBadRequest)
		return
	}

	var req struct {
		DateHeureCollecte string `json:"date_heure_collecte"`
		AdresseCollecte   string `json:"adresse_collecte"`
		CommercantID      int    `json:"commercant_id"`
		Commentaire       string `json:"commentaire"`
		Statut            string `json:"statut"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}

	dateHeure, err := utils.ParseDateTime(req.DateHeureCollecte)
	if err != nil {
		http.Error(w, "Date/heure invalide", http.StatusBadRequest)
		return
	}

	collecte := &modeles.Collecte{
		ID:                id,
		DateHeureCollecte: dateHeure,
		AdresseCollecte:   req.AdresseCollecte,
		Commentaire:       req.Commentaire,
		CommercantID:      req.CommercantID,
		Statut:            req.Statut,
	}

	if err := c.service.MettreAJour(collecte); err != nil {
		ecrireErreurJSON(w, err.Error(), http.StatusBadRequest)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{
		"message": "Collecte mise à jour avec succès",
	})
}

func (c *CollecteControleur) Terminer(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	idStr := vars["id"]
	id, err := strconv.Atoi(idStr)
	if err != nil {
		http.Error(w, "ID invalide", http.StatusBadRequest)
		return
	}

	if err := c.service.MarquerTerminee(id); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{
		"message": "Collecte terminée avec succès",
	})
}

func (c *CollecteControleur) Supprimer(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	idStr := vars["id"]
	id, err := strconv.Atoi(idStr)
	if err != nil {
		http.Error(w, "ID invalide", http.StatusBadRequest)
		return
	}

	if err := c.service.Supprimer(id); err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{
		"message": "Collecte supprimée avec succès",
	})
}

func (c *CollecteControleur) TelechargerPDF(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	data, err := c.service.ObtenirPDF(id)
	if err != nil {
		http.Error(w, err.Error(), http.StatusNotFound)
		return
	}
	w.Header().Set("Content-Type", "application/pdf")
	w.Header().Set("Content-Disposition", fmt.Sprintf("inline; filename=\"collecte-%d.pdf\"", id))
	w.Write(data)
}

func (c *CollecteControleur) Valider(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	idStr := vars["id"]
	id, err := strconv.Atoi(idStr)
	if err != nil {
		http.Error(w, "ID invalide", http.StatusBadRequest)
		return
	}

	if err := c.service.ValiderCollecte(id); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{
		"message": "Collecte validée avec succès",
	})
}

func (c *CollecteControleur) AjouterBenevole(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	collecteID, _ := strconv.Atoi(vars["id"])
	var req struct {
		BenevoleID int `json:"benevole_id"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	if err := c.service.AjouterBenevole(collecteID, req.BenevoleID); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Bénévole ajouté"})
}

func (c *CollecteControleur) SupprimerBenevole(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	collecteID, _ := strconv.Atoi(vars["id"])
	benevoleID, _ := strconv.Atoi(vars["benevoleId"])
	if err := c.service.SupprimerBenevole(collecteID, benevoleID); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Bénévole retiré"})
}

func (c *CollecteControleur) ConfirmerBenevole(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	collecteID, _ := strconv.Atoi(vars["id"])
	var req struct {
		BenevoleID int `json:"benevole_id"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	if err := c.service.ConfirmerBenevole(collecteID, req.BenevoleID); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Confirmation enregistrée"})
}
