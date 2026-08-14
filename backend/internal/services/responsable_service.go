package services

import (
	"errors"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
	"nomorewaste/internal/utils"
)

type ResponsableService struct {
	repo *repositories.ResponsableRepository
}

func NouveauResponsableService(repo *repositories.ResponsableRepository) *ResponsableService {
	return &ResponsableService{repo: repo}
}

func (s *ResponsableService) Creer(u *modeles.Utilisateur) (int, error) {
	if u.Email == "" || u.MotDePasse == "" || u.Nom == "" || u.Prenom == "" {
		return 0, errors.New("email, mot de passe, nom et prénom requis")
	}
	if err := utils.ValiderComplexiteMotDePasse(u.MotDePasse); err != nil {
		return 0, err
	}
	hache, err := utils.HacherMotDePasse(u.MotDePasse)
	if err != nil {
		return 0, err
	}
	u.MotDePasse = hache
	return s.repo.Creer(u)
}

func (s *ResponsableService) Lister() ([]modeles.Utilisateur, error) {
	return s.repo.TrouverTous()
}

func (s *ResponsableService) TrouverParID(id int) (*modeles.Utilisateur, error) {
	return s.repo.TrouverParID(id)
}

func (s *ResponsableService) MettreAJour(u *modeles.Utilisateur) error {
	if u.Nom == "" || u.Prenom == "" {
		return errors.New("nom et prénom requis")
	}
	if _, err := s.repo.TrouverParID(u.ID); err != nil {
		return errors.New("administrateur non trouvé")
	}
	if u.MotDePasse != "" {
		if err := utils.ValiderComplexiteMotDePasse(u.MotDePasse); err != nil {
			return err
		}
		hache, err := utils.HacherMotDePasse(u.MotDePasse)
		if err != nil {
			return err
		}
		u.MotDePasse = hache
	}
	return s.repo.MettreAJour(u)
}

func (s *ResponsableService) Supprimer(id int) error {
	if _, err := s.repo.TrouverParID(id); err != nil {
		return errors.New("administrateur non trouvé")
	}
	n, err := s.repo.CompterActifs()
	if err != nil {
		return err
	}
	if n <= 1 {
		return errors.New("impossible de supprimer le dernier compte administrateur")
	}
	return s.repo.Supprimer(id)
}
