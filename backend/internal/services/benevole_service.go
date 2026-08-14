package services

import (
	"errors"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
	"nomorewaste/internal/utils"
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
	if err := utils.ValiderComplexiteMotDePasse(b.MotDePasse); err != nil {
		return 0, err
	}
	hache, err := utils.HacherMotDePasse(b.MotDePasse)
	if err != nil {
		return 0, err
	}
	b.MotDePasse = hache
	return s.repo.Creer(b)
}

func (s *BenevoleService) Lister(nom, statut string, competenceID int) ([]modeles.Benevole, error) {
    return s.repo.TrouverTous(nom, statut, competenceID)
}

func (s *BenevoleService) TrouverParID(id int) (*modeles.Benevole, error) {
	return s.repo.TrouverParID(id)
}

func (s *BenevoleService) MettreAJour(id int, b *modeles.BenevoleMiseAJour) error {
	if b.Nom == "" || b.Prenom == "" {
		return errors.New("nom et prénom requis")
	}
	return s.repo.MettreAJour(id, b)
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

func (s *BenevoleService) Supprimer(id int) error {
	return s.repo.Supprimer(id)
}

func (s *BenevoleService) AjouterCompetence(benevoleID, competenceID int) error {
	return s.repo.AjouterCompetence(benevoleID, competenceID)
}

func (s *BenevoleService) SupprimerCompetence(benevoleID, competenceID int) error {
	return s.repo.SupprimerCompetence(benevoleID, competenceID)
}