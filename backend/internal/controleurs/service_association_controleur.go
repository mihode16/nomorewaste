package controleurs

import (
	"encoding/json"
	"net/http"
	"strconv"
	"time"

	"github.com/gorilla/mux"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
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

func (c *ServiceAssociationControleur) CreerService(w http.ResponseWriter, r *http.Request) {
	var req modeles.ServiceCreation
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	id, err := c.service.CreerService(&req)
	if err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]int{"id": id})
}

func (c *ServiceAssociationControleur) TrouverServiceParID(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	svc, err := c.service.TrouverServiceParID(id)
	if err != nil {
		http.Error(w, "Service non trouvé", http.StatusNotFound)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(svc)
}

func (c *ServiceAssociationControleur) MettreAJourService(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	var req modeles.Service
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	req.ID = id
	if err := c.service.MettreAJourService(&req); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}

func (c *ServiceAssociationControleur) SupprimerService(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	if err := c.service.SupprimerService(id); err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}

func (c *ServiceAssociationControleur) TrouverPlanningParID(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	p, err := c.service.TrouverPlanningParID(id)
	if err != nil {
		http.Error(w, "Planning non trouvé", http.StatusNotFound)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(p)
}

func (c *ServiceAssociationControleur) MettreAJourPlanning(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	var req modeles.ServicePlanningUpdate
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	req.ID = id
	if err := c.service.MettreAJourPlanning(&req); err != nil {
		ecrireErreurJSON(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}

func (c *ServiceAssociationControleur) SupprimerPlanning(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	if err := c.service.SupprimerPlanning(id); err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
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
		ecrireErreurJSON(w, err.Error(), http.StatusBadRequest)
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

func (c *ServiceAssociationControleur) ListerAdherents(w http.ResponseWriter, r *http.Request) {
	recherche := r.URL.Query().Get("recherche")
	statut := r.URL.Query().Get("statut")
	list, err := c.service.ListerAdherents(recherche, statut)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(list)
}

func (c *ServiceAssociationControleur) TrouverAdherentParID(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	a, err := c.service.TrouverAdherentParID(id)
	if err != nil {
		http.Error(w, "Adhérent non trouvé", http.StatusNotFound)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(a)
}

func (c *ServiceAssociationControleur) MettreAJourAdherent(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	var req struct {
		Nom                         string `json:"nom"`
		Prenom                      string `json:"prenom"`
		Telephone                   string `json:"telephone"`
		Adresse                     string `json:"adresse"`
		DateDebutAdhesion           string `json:"date_debut_adhesion"`
		DateFinAdhesion             string `json:"date_fin_adhesion"`
		EstRenouveleAutomatiquement bool   `json:"est_renouvele_automatiquement"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
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
	a := modeles.Adherent{
		Utilisateur: modeles.Utilisateur{
			ID:        id,
			Nom:       req.Nom,
			Prenom:    req.Prenom,
			Telephone: req.Telephone,
			Adresse:   req.Adresse,
		},
		DateDebutAdhesion:           dateDebut,
		DateFinAdhesion:             dateFin,
		EstRenouveleAutomatiquement: req.EstRenouveleAutomatiquement,
	}
	if err := c.service.MettreAJourAdherent(&a); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Adhérent mis à jour"})
}

func (c *ServiceAssociationControleur) ValiderAdhesionAdherent(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	if err := c.service.ValiderAdhesionAdherent(id); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Adhésion validée"})
}

func (c *ServiceAssociationControleur) DemanderRenouvellementAdherent(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	if err := c.service.DemanderRenouvellementAdherent(id); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Demande de renouvellement enregistrée"})
}

func (c *ServiceAssociationControleur) RenouvelerAdhesionAdherent(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	var req struct {
		DureeMois int `json:"duree_mois"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	if req.DureeMois <= 0 {
		req.DureeMois = 12
	}
	if err := c.service.RenouvelerAdhesionAdherent(id, req.DureeMois); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Adhésion renouvelée"})
}

func (c *ServiceAssociationControleur) SupprimerAdherent(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	if err := c.service.SupprimerAdherent(id); err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Adhérent supprimé"})
}

func (c *ServiceAssociationControleur) VerifierAdhesionsAdherentExpirantes(w http.ResponseWriter, r *http.Request) {
	list, err := c.service.VerifierAdhesionsAdherentExpirantes()
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(list)
}

func (c *ServiceAssociationControleur) ListerInscriptionsParAdherent(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	list, err := c.service.ListerInscriptionsParAdherent(id)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(list)
}

func (c *ServiceAssociationControleur) SupprimerInscription(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	adherentID, _ := strconv.Atoi(vars["id"])
	planningID, _ := strconv.Atoi(vars["planningId"])
	if err := c.service.SupprimerInscription(adherentID, planningID); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Désinscription effectuée"})
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
