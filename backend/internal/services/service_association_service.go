package services

import (
	"errors"
	"time"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
	"nomorewaste/internal/utils"
)

type ServiceAssociationService struct {
	repo *repositories.ServiceRepository
}

func NouveauServiceAssociationService(repo *repositories.ServiceRepository) *ServiceAssociationService {
	return &ServiceAssociationService{repo: repo}
}

func (s *ServiceAssociationService) ListerServices() ([]modeles.Service, error) {
	return s.repo.ListerServices()
}

func (s *ServiceAssociationService) CreerService(svc *modeles.ServiceCreation) (int, error) {
	if svc.Nom == "" {
		return 0, errors.New("nom du service requis")
	}
	return s.repo.CreerService(svc)
}

func (s *ServiceAssociationService) TrouverServiceParID(id int) (*modeles.Service, error) {
	return s.repo.TrouverServiceParID(id)
}

func (s *ServiceAssociationService) MettreAJourService(svc *modeles.Service) error {
	if svc.Nom == "" {
		return errors.New("nom du service requis")
	}
	return s.repo.MettreAJourService(svc)
}

func (s *ServiceAssociationService) SupprimerService(id int) error {
	return s.repo.SupprimerService(id)
}

func (s *ServiceAssociationService) CreerPlanning(p *modeles.ServicePlanningCreation) (int, error) {
	if p.ServiceID <= 0 || p.CapaciteMax <= 0 {
		return 0, errors.New("service et capacité requis")
	}
	if err := utils.ValiderDateNonPassee(p.DateHeureDebut); err != nil {
		return 0, err
	}
	return s.repo.CreerPlanning(p)
}

func (s *ServiceAssociationService) ListerPlannings() ([]modeles.ServicePlanning, error) {
	return s.repo.ListerPlannings()
}

func (s *ServiceAssociationService) TrouverPlanningParID(id int) (*modeles.ServicePlanning, error) {
	return s.repo.TrouverPlanningParID(id)
}

func (s *ServiceAssociationService) MettreAJourPlanning(p *modeles.ServicePlanningUpdate) error {
	if p.ServiceID <= 0 || p.CapaciteMax <= 0 {
		return errors.New("service et capacité requis")
	}
	if err := utils.ValiderDateNonPassee(p.DateHeureDebut); err != nil {
		return err
	}
	return s.repo.MettreAJourPlanning(p)
}

func (s *ServiceAssociationService) SupprimerPlanning(id int) error {
	return s.repo.SupprimerPlanning(id)
}

func (s *ServiceAssociationService) CreerAdherent(a *modeles.AdherentCreation) (int, error) {
	if a.Email == "" || a.Nom == "" {
		return 0, errors.New("email et nom requis")
	}
	if err := utils.ValiderComplexiteMotDePasse(a.MotDePasse); err != nil {
		return 0, err
	}
	hache, err := utils.HacherMotDePasse(a.MotDePasse)
	if err != nil {
		return 0, err
	}
	a.MotDePasse = hache
	return s.repo.CreerAdherent(a)
}

func (s *ServiceAssociationService) ListerAdherents(recherche, statut string) ([]modeles.Adherent, error) {
	return s.repo.ListerAdherents(recherche, statut)
}

func (s *ServiceAssociationService) TrouverAdherentParID(id int) (*modeles.Adherent, error) {
	return s.repo.TrouverAdherentParID(id)
}

func (s *ServiceAssociationService) MettreAJourAdherent(a *modeles.Adherent) error {
	if a.Nom == "" {
		return errors.New("nom requis")
	}
	if _, err := s.repo.TrouverAdherentParID(a.ID); err != nil {
		return errors.New("adhérent non trouvé")
	}
	return s.repo.MettreAJourAdherent(a)
}

func (s *ServiceAssociationService) ValiderAdhesionAdherent(id int) error {
	if _, err := s.repo.TrouverAdherentParID(id); err != nil {
		return errors.New("adhérent non trouvé")
	}
	return s.repo.ValiderAdhesionAdherent(id)
}

func (s *ServiceAssociationService) DemanderRenouvellementAdherent(id int) error {
	if _, err := s.repo.TrouverAdherentParID(id); err != nil {
		return errors.New("adhérent non trouvé")
	}
	return s.repo.DemanderRenouvellementAdherent(id)
}

func (s *ServiceAssociationService) RenouvelerAdhesionAdherent(id, dureeMois int) error {
	existant, err := s.repo.TrouverAdherentParID(id)
	if err != nil {
		return errors.New("adhérent non trouvé")
	}
	// Prolonge l'adhésion à partir de sa date de fin actuelle (si elle n'est pas déjà
	// expirée) plutôt que de toujours repartir d'aujourd'hui.
	base := existant.DateFinAdhesion
	if base.Before(time.Now()) {
		base = time.Now()
	}
	nouvelleDateFin := base.AddDate(0, dureeMois, 0)
	return s.repo.RenouvelerAdhesionAdherent(id, nouvelleDateFin)
}

func (s *ServiceAssociationService) SupprimerAdherent(id int) error {
	return s.repo.SupprimerAdherent(id)
}

func (s *ServiceAssociationService) VerifierAdhesionsAdherentExpirantes() ([]modeles.Adherent, error) {
	return s.repo.TrouverAdhesionsAdherentExpirantBientot(1)
}

func (s *ServiceAssociationService) ListerInscriptionsParAdherent(adherentID int) ([]modeles.ServicePlanning, error) {
	return s.repo.ListerInscriptionsParAdherent(adherentID)
}

func (s *ServiceAssociationService) SupprimerInscription(adherentID, planningID int) error {
	return s.repo.SupprimerInscription(adherentID, planningID)
}

func (s *ServiceAssociationService) Inscrire(adherentID, planningID int) error {
	if adherentID <= 0 || planningID <= 0 {
		return errors.New("adhérent et planning requis")
	}
	count, err := s.repo.CompterInscriptions(planningID)
	if err != nil {
		return err
	}
	plannings, err := s.repo.ListerPlannings()
	if err != nil {
		return err
	}
	for _, p := range plannings {
		if p.ID == planningID {
			if count >= p.CapaciteMax {
				return errors.New("capacité maximale atteinte")
			}
			break
		}
	}
	return s.repo.Inscrire(adherentID, planningID)
}
