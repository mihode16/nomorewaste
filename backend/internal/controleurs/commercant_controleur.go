package controleurs

import (
	"encoding/json"
	"net/http"
	"strconv"

	"github.com/gorilla/mux"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/services"
)

type CommercantControleur struct {
    service *services.CommercantService
}

func NouveauCommercantControleur(service *services.CommercantService) *CommercantControleur {
    return &CommercantControleur{service: service}
}

func (c *CommercantControleur) Creer(w http.ResponseWriter, r *http.Request) {
    var req modeles.CommercantCreation
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
        "message": "Commerçant créé avec succès",
    })
}

func (c *CommercantControleur) Lister(w http.ResponseWriter, r *http.Request) {
    commercants, err := c.service.Lister()
    if err != nil {
        http.Error(w, err.Error(), http.StatusInternalServerError)
        return
    }

    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(commercants)
}

func (c *CommercantControleur) TrouverParID(w http.ResponseWriter, r *http.Request) {
    vars := mux.Vars(r)
    idStr := vars["id"]
    id, err := strconv.Atoi(idStr)
    if err != nil {
        http.Error(w, "ID invalide", http.StatusBadRequest)
        return
    }

    commercant, err := c.service.TrouverParID(id)
    if err != nil {
        http.Error(w, "Commerçant non trouvé", http.StatusNotFound)
        return
    }

    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(commercant)
}

func (c *CommercantControleur) MettreAJour(w http.ResponseWriter, r *http.Request) {
    vars := mux.Vars(r)
    idStr := vars["id"]
    id, err := strconv.Atoi(idStr)
    if err != nil {
        http.Error(w, "ID invalide", http.StatusBadRequest)
        return
    }

    var req modeles.Commercant
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
        "message": "Commerçant mis à jour avec succès",
    })
}

func (c *CommercantControleur) RenouvelerAdhesion(w http.ResponseWriter, r *http.Request) {
    vars := mux.Vars(r)
    idStr := vars["id"]
    id, err := strconv.Atoi(idStr)
    if err != nil {
        http.Error(w, "ID invalide", http.StatusBadRequest)
        return
    }

    var req struct {
        DureeMois int `json:"duree_mois"`
    }
    if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
        http.Error(w, "Requête invalide", http.StatusBadRequest)
        return
    }

    if req.DureeMois <= 0 {
        req.DureeMois = 12 // Renouvellement d'un an par défaut
    }

    if err := c.service.RenouvelerAdhesion(id, req.DureeMois); err != nil {
        http.Error(w, err.Error(), http.StatusBadRequest)
        return
    }

    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(map[string]string{
        "message": "Adhésion renouvelée avec succès",
    })
}

func (c *CommercantControleur) Supprimer(w http.ResponseWriter, r *http.Request) {
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
        "message": "Commerçant supprimé avec succès",
    })
}

func (c *CommercantControleur) VerifierAdhesionsExpirantes(w http.ResponseWriter, r *http.Request) {
    commercants, err := c.service.VerifierAdhesionsExpirantes()
    if err != nil {
        http.Error(w, err.Error(), http.StatusInternalServerError)
        return
    }

    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(commercants)
}