package services

import (
	"log"
	"time"
)

// SchedulerService exécute une fois par jour, à heure fixe, les tâches automatiques du site :
// l'envoi des rappels de renouvellement d'adhésion et l'envoi des plannings du jour aux
// bénévoles concernés.
type SchedulerService struct {
	rappelService   *RappelService
	planningService *PlanningService
	heureLocale     int
}

func NouveauSchedulerService(rappelService *RappelService, planningService *PlanningService, heureLocale int) *SchedulerService {
	return &SchedulerService{rappelService: rappelService, planningService: planningService, heureLocale: heureLocale}
}

// Demarrer lance la boucle de planification dans une goroutine dédiée.
func (s *SchedulerService) Demarrer() {
	go s.boucle()
}

func (s *SchedulerService) boucle() {
	for {
		time.Sleep(s.dureeAvantProchaineExecution())
		s.ExecuterTachesDuJour()
	}
}

func (s *SchedulerService) dureeAvantProchaineExecution() time.Duration {
	maintenant := time.Now()
	prochaine := time.Date(maintenant.Year(), maintenant.Month(), maintenant.Day(), s.heureLocale, 0, 0, 0, maintenant.Location())
	if !prochaine.After(maintenant) {
		prochaine = prochaine.Add(24 * time.Hour)
	}
	return prochaine.Sub(maintenant)
}

// ExecuterTachesDuJour déclenche immédiatement les tâches quotidiennes (rappels de
// renouvellement + plannings bénévoles).
func (s *SchedulerService) ExecuterTachesDuJour() {
	nbRappels, err := s.rappelService.EnvoyerRappels()
	if err != nil {
		log.Printf("⚠️ Erreur envoi rappels de renouvellement: %v", err)
	} else {
		log.Printf("✅ %d rappel(s) de renouvellement envoyé(s)", nbRappels)
	}

	nbPlannings, err := s.planningService.GenererEtEnvoyerPlanningsDuJour()
	if err != nil {
		log.Printf("⚠️ Erreur envoi plannings du jour: %v", err)
	} else {
		log.Printf("✅ %d planning(s) du jour envoyé(s)", nbPlannings)
	}
}
