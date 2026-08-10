package controleurs

import (
	"encoding/json"
	"log"
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
		log.Printf("❌ Erreur décodage JSON: %v", err)
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}

	id, err := c.service.Creer(&req)
	if err != nil {
		log.Printf("❌ Erreur création collecte: %v", err)
		http.Error(w, err.Error(), http.StatusBadRequest)
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
	// Récupérer les filtres
	commercant := r.URL.Query().Get("commercant")
	statut := r.URL.Query().Get("statut")
	dateDebut := r.URL.Query().Get("date_debut")
	dateFin := r.URL.Query().Get("date_fin")

	collectes, err := c.service.Lister(commercant, statut, dateDebut, dateFin)
	if err != nil {
		log.Printf("❌ Erreur liste collectes: %v", err)
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
		log.Printf("❌ Collecte %d non trouvée: %v", id, err)
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
		log.Printf("❌ Erreur décodage JSON: %v", err)
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}

	log.Printf("📝 Mise à jour collecte %d: date=%s, adresse=%s", id, req.DateHeureCollecte, req.AdresseCollecte)

	dateHeure, err := utils.ParseDateTime(req.DateHeureCollecte)
	if err != nil {
		log.Printf("❌ Erreur parsing date '%s': %v", req.DateHeureCollecte, err)
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
		log.Printf("❌ Erreur mise à jour collecte %d: %v", id, err)
		http.Error(w, err.Error(), http.StatusBadRequest)
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
		log.Printf("❌ Erreur terminer collecte %d: %v", id, err)
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
		log.Printf("❌ Erreur suppression collecte %d: %v", id, err)
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{
		"message": "Collecte supprimée avec succès",
	})
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
		log.Printf("❌ Erreur validation collecte %d: %v", id, err)
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
		log.Printf("❌ Erreur ajout bénévole à collecte %d: %v", collecteID, err)
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Bénévole ajouté"})
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
		log.Printf("❌ Erreur confirmation bénévole collecte %d: %v", collecteID, err)
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Confirmation enregistrée"})
}