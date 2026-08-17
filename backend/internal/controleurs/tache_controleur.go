package controleurs

import (
	"encoding/json"
	"net/http"

	"nomorewaste/internal/services"
)

// TacheControleur déclenche manuellement les tâches planifiées quotidiennes (rappels de
// renouvellement + plannings bénévoles), sans attendre l'heure planifiée.
type TacheControleur struct {
	scheduler *services.SchedulerService
}

func NouveauTacheControleur(scheduler *services.SchedulerService) *TacheControleur {
	return &TacheControleur{scheduler: scheduler}
}

func (c *TacheControleur) ExecuterMaintenant(w http.ResponseWriter, r *http.Request) {
	c.scheduler.ExecuterTachesDuJour()
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"message": "Tâches quotidiennes exécutées"})
}
