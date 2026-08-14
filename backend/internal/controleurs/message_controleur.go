package controleurs

import (
	"encoding/json"
	"net/http"
	"strconv"

	"github.com/gorilla/mux"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/services"
)

type MessageControleur struct {
	service *services.MessageService
}

func NouveauMessageControleur(service *services.MessageService) *MessageControleur {
	return &MessageControleur{service: service}
}

func (c *MessageControleur) Creer(w http.ResponseWriter, r *http.Request) {
	var req modeles.ConversationCreation
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	id, err := c.service.CreerConversation(&req)
	if err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]int{"id": id})
}

func (c *MessageControleur) Lister(w http.ResponseWriter, r *http.Request) {
	utilisateurID, _ := strconv.Atoi(r.URL.Query().Get("utilisateur_id"))
	var list []modeles.Conversation
	var err error
	if utilisateurID > 0 {
		list, err = c.service.ListerPourUtilisateur(utilisateurID)
	} else {
		list, err = c.service.ListerPourAdmin(r.URL.Query().Get("filtre"))
	}
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(list)
}

func (c *MessageControleur) TrouverParID(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	conv, err := c.service.TrouverParID(id)
	if err != nil {
		http.Error(w, "Conversation introuvable", http.StatusNotFound)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(conv)
}

func (c *MessageControleur) AjouterMessage(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	var req modeles.MessageCreation
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Requête invalide", http.StatusBadRequest)
		return
	}
	msgID, err := c.service.AjouterMessage(id, &req)
	if err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]int{"id": msgID})
}

func (c *MessageControleur) Cloturer(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	if err := c.service.Cloturer(id); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}

func (c *MessageControleur) CompterNonLus(w http.ResponseWriter, r *http.Request) {
	role := r.URL.Query().Get("role")
	if role == "" {
		role = "admin"
	}
	utilisateurID, _ := strconv.Atoi(r.URL.Query().Get("utilisateur_id"))
	n, err := c.service.CompterNonLus(role, utilisateurID)
	if err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]int{"non_lus": n})
}

func (c *MessageControleur) MarquerLu(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.Atoi(mux.Vars(r)["id"])
	role := r.URL.Query().Get("role")
	if role == "" {
		role = "admin"
	}
	utilisateurID, _ := strconv.Atoi(r.URL.Query().Get("utilisateur_id"))
	if err := c.service.MarquerLu(id, role, utilisateurID); err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "ok"})
}
