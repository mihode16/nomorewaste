package services

import (
	"errors"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
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

func (s *ServiceAssociationService) CreerPlanning(p *modeles.ServicePlanningCreation) (int, error) {
	if p.ServiceID <= 0 || p.CapaciteMax <= 0 {
		return 0, errors.New("service et capacité requis")
	}
	return s.repo.CreerPlanning(p)
}

func (s *ServiceAssociationService) ListerPlannings() ([]modeles.ServicePlanning, error) {
	return s.repo.ListerPlannings()
}

func (s *ServiceAssociationService) CreerAdherent(a *modeles.AdherentCreation) (int, error) {
	if a.Email == "" || a.Nom == "" {
		return 0, errors.New("email et nom requis")
	}
	return s.repo.CreerAdherent(a)
}

func (s *ServiceAssociationService) ListerAdherents() ([]modeles.Adherent, error) {
	return s.repo.ListerAdherents()
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
