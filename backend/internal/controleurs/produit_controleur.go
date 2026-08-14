package controleurs

import (
	"encoding/json"
	"net/http"
	"strconv"

	"github.com/gorilla/mux"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/services"
	"nomorewaste/internal/utils"
)

type ProduitControleur struct {
	service *services.ProduitService
}

func NouveauProduitControleur(service *services.ProduitService) *ProduitControleur {
	return &ProduitControleur{service: service}
}

func (c *ProduitControleur) Creer(w http.ResponseWriter, r *http.Request) {
	var req modeles.ProduitCreation
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
	json.NewEncoder(w).Encode(map[string]interface{}{"id": id})
}

func (c *ProduitControleur) Lister(w http.ResponseWriter, r *http.Request) {
    recherche := r.URL.Query().Get("recherche")
    categorie := r.URL.Query().Get("categorie")
    statut := r.URL.Query().Get("statut")
    tri := r.URL.Query().Get("tri")
    list, err := c.service.Lister(recherche, categorie, statut, tri)
    if err != nil {
        http.Error(w, err.Error(), http.StatusInternalServerError)
        return
    }
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(list)
}

func (c *ProduitControleur) ListerCategories(w http.ResponseWriter, r *http.Request) {
    categories, err := c.service.ListerCategories()
    if err != nil {
        http.Error(w, err.Error(), http.StatusInternalServerError)
        return
    }
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(categories)
}

func (c *ProduitControleur) TrouverParID(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	p, err := c.service.TrouverParID(id)
	if err != nil {
		http.Error(w, "Produit non trouvé", http.StatusNotFound)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(p)
}

func (c *ProduitControleur) MettreAJour(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])

	var req struct {
		CodeBarre      string `json:"code_barre"`
		Nom            string `json:"nom"`
		Categorie      string `json:"categorie"`
		Quantite       int    `json:"quantite"`
		DatePeremption string `json:"date_peremption"`
		Statut         string `json:"statut"`
		CollecteID     int    `json:"collecte_id"`
	}
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}

	datePer, err := utils.ParseDate(req.DatePeremption)
	if err != nil {
		http.Error(w, "Date de péremption invalide", http.StatusBadRequest)
		return
	}

	produit := modeles.Produit{
		ID:             id,
		CodeBarre:      req.CodeBarre,
		Nom:            req.Nom,
		Categorie:      req.Categorie,
		Quantite:       req.Quantite,
		DatePeremption: datePer,
		Statut:         req.Statut,
		CollecteID:     req.CollecteID,
	}
	if err := c.service.MettreAJour(&produit); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}

func (c *ProduitControleur) Supprimer(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	if err := c.service.Supprimer(id); err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}

func (c *ProduitControleur) ListerParCollecte(w http.ResponseWriter, r *http.Request) {
	vars := mux.Vars(r)
	idStr := vars["id"]
	collecteID, err := strconv.Atoi(idStr)
	if err != nil {
		http.Error(w, "ID de collecte invalide", http.StatusBadRequest)
		return
	}

	produits, err := c.service.ListerParCollecte(collecteID)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(produits)
}



