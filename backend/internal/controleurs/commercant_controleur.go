package controleurs

import (
	"encoding/json"
	"log"
	"net/http"
	"strconv"
	"time"

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
        log.Printf("❌ Erreur de décodage JSON: %v", err)
        http.Error(w, "Requête invalide", http.StatusBadRequest)
        return
    }

    log.Printf("📝 Création d'un commerçant: email=%s, raison_sociale=%s", req.Email, req.RaisonSociale)

    id, err := c.service.Creer(&req)
    if err != nil {
        log.Printf("❌ Erreur création commerçant: %v", err)
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

    // Structure temporaire avec des dates en string
    var req struct {
        Nom                         string `json:"nom"`
        Prenom                      string `json:"prenom"`
        Telephone                   string `json:"telephone"`
        Adresse                     string `json:"adresse"`
        Siret                       string `json:"siret"`
        RaisonSociale               string `json:"raison_sociale"`
        TypeCommerce                string `json:"type_commerce"`
        DateDebutAdhesion           string `json:"date_debut_adhesion"`
        DateFinAdhesion             string `json:"date_fin_adhesion"`
        EstRenouveleAutomatiquement bool   `json:"est_renouvele_automatiquement"`
    }

    if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
        log.Printf("❌ Erreur de décodage JSON: %v", err)
        http.Error(w, "Requête invalide", http.StatusBadRequest)
        return
    }

    // Conversion des dates (format YYYY-MM-DD)
    dateDebut, err := time.Parse("2006-01-02", req.DateDebutAdhesion)
    if err != nil {
        http.Error(w, "Date de début invalide", http.StatusBadRequest)
        return
    }
    dateFin, err := time.Parse("2006-01-02", req.DateFinAdhesion)
    if err != nil {
        http.Error(w, "Date de fin invalide", http.StatusBadRequest)
        return
    }

    // Construction de l'objet commerçant
    commercant := modeles.Commercant{
        Utilisateur: modeles.Utilisateur{
            ID:        id,
            Nom:       req.Nom,
            Prenom:    req.Prenom,
            Telephone: req.Telephone,
            Adresse:   req.Adresse,
        },
        Siret:                         req.Siret,
        RaisonSociale:                 req.RaisonSociale,
        TypeCommerce:                  req.TypeCommerce,
        DateDebutAdhesion:             dateDebut,
        DateFinAdhesion:               dateFin,
        EstRenouveleAutomatiquement:   req.EstRenouveleAutomatiquement,
        // StatutAdhesion n'est pas modifié
    }

    if err := c.service.MettreAJour(&commercant); err != nil {
        log.Printf("❌ Erreur mise à jour commerçant %d: %v", id, err)
        http.Error(w, err.Error(), http.StatusBadRequest)
        return
    }

    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(map[string]string{"message": "Commerçant mis à jour avec succès"})
}

func (c *CommercantControleur) Valider(w http.ResponseWriter, r *http.Request) {
    vars := mux.Vars(r)
    idStr := vars["id"]
    id, err := strconv.Atoi(idStr)
    if err != nil {
        http.Error(w, "ID invalide", http.StatusBadRequest)
        return
    }

    if err := c.service.ValiderAdhesion(id); err != nil {
        http.Error(w, err.Error(), http.StatusBadRequest)
        return
    }

    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(map[string]string{"message": "Adhésion validée"})
}

func (c *CommercantControleur) DemanderRenouvellement(w http.ResponseWriter, r *http.Request) {
    vars := mux.Vars(r)
    idStr := vars["id"]
    id, err := strconv.Atoi(idStr)
    if err != nil {
        http.Error(w, "ID invalide", http.StatusBadRequest)
        return
    }

    if err := c.service.DemanderRenouvellement(id); err != nil {
        http.Error(w, err.Error(), http.StatusBadRequest)
        return
    }

    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(map[string]string{"message": "Demande de renouvellement enregistrée"})
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