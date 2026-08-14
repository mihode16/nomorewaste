package controleurs

import (
	"encoding/json"
	"net/http"
	"strconv"

	"github.com/gorilla/mux"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
	"nomorewaste/internal/services"
)

type ResponsableControleur struct {
	service *services.ResponsableService
}

func NouveauResponsableControleur(service *services.ResponsableService) *ResponsableControleur {
	return &ResponsableControleur{service: service}
}

func (c *ResponsableControleur) Creer(w http.ResponseWriter, r *http.Request) {
	var req modeles.Utilisateur
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	id, err := c.service.Creer(&req)
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

func (c *ResponsableControleur) Lister(w http.ResponseWriter, r *http.Request) {
	list, err := c.service.Lister()
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(list)
}

func (c *ResponsableControleur) TrouverParID(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	u, err := c.service.TrouverParID(id)
	if err != nil {
		http.Error(w, "Administrateur non trouvé", http.StatusNotFound)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(u)
}

func (c *ResponsableControleur) MettreAJour(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	var req modeles.Utilisateur
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
	json.NewEncoder(w).Encode(map[string]string{"message": "Administrateur mis à jour"})
}

func (c *ResponsableControleur) Supprimer(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	if err := c.service.Supprimer(id); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Administrateur supprimé"})
}
