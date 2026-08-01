package controleurs

import (
	"encoding/json"
	"net/http"
	"strconv"

	"github.com/gorilla/mux"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/services"
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
    collectes, err := c.service.Lister()
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

    var req modeles.Collecte
    if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
        http.Error(w, "Requête invalide", http.StatusBadRequest)
        return
    }
    req.ID = id

    if err := c.service.MettreAJour(&req); err != nil {
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