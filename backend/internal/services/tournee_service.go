package services

import (
	"errors"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
)

type TourneeService struct {
	repo *repositories.TourneeRepository
}

func NouveauTourneeService(repo *repositories.TourneeRepository) *TourneeService {
	return &TourneeService{repo: repo}
}

func (s *TourneeService) Creer(t *modeles.TourneeCreation) (int, error) {
	if t.AdresseDepart == "" || t.BenevoleID <= 0 || t.LieuDistributionID <= 0 {
		return 0, errors.New("adresse, bénévole et lieu requis")
	}
	return s.repo.Creer(t)
}

func (s *TourneeService) Lister() ([]modeles.Tournee, error) {
	return s.repo.TrouverTous()
}

func (s *TourneeService) TrouverParID(id int) (*modeles.Tournee, error) {
	return s.repo.TrouverParID(id)
}

func (s *TourneeService) Terminer(id int) error {
	return s.repo.Terminer(id)
}

func (s *TourneeService) ListerLieux() ([]modeles.LieuDistribution, error) {
	return s.repo.ListerLieux()
}
