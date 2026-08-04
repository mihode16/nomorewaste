package services

import (
	"errors"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
)

type BenevoleService struct {
	repo *repositories.BenevoleRepository
}

func NouveauBenevoleService(repo *repositories.BenevoleRepository) *BenevoleService {
	return &BenevoleService{repo: repo}
}

func (s *BenevoleService) Creer(b *modeles.BenevoleCreation) (int, error) {
	if b.Email == "" || b.Nom == "" {
		return 0, errors.New("email et nom requis")
	}
	return s.repo.Creer(b)
}

func (s *BenevoleService) Lister() ([]modeles.Benevole, error) {
	return s.repo.TrouverTous()
}

func (s *BenevoleService) TrouverParID(id int) (*modeles.Benevole, error) {
	return s.repo.TrouverParID(id)
}

func (s *BenevoleService) ChangerStatut(id int, statut string) error {
	return s.repo.ChangerStatut(id, statut)
}

func (s *BenevoleService) ListerCompetences() ([]modeles.Competence, error) {
	return s.repo.ListerCompetences()
}

func (s *BenevoleService) ListerValides() ([]modeles.Benevole, error) {
	return s.repo.TrouverValides()
}
