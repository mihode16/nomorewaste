package services

import (
	"errors"
	"time"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
)

type CommercantService struct {
    repo *repositories.CommercantRepository
}

func NouveauCommercantService(repo *repositories.CommercantRepository) *CommercantService {
    return &CommercantService{repo: repo}
}

func (s *CommercantService) Creer(commercant *modeles.CommercantCreation) (int, error) {
    // Validation
    if commercant.Email == "" {
        return 0, errors.New("l'email est requis")
    }
    if commercant.MotDePasse == "" {
        return 0, errors.New("le mot de passe est requis")
    }
    if commercant.Siret == "" {
        return 0, errors.New("le SIRET est requis")
    }

    // TODO: Vérifier si l'email existe déjà

    return s.repo.Creer(commercant)
}

func (s *CommercantService) Lister() ([]modeles.Commercant, error) {
    return s.repo.TrouverTous()
}

func (s *CommercantService) TrouverParID(id int) (*modeles.Commercant, error) {
    return s.repo.TrouverParID(id)
}

func (s *CommercantService) MettreAJour(commercant *modeles.Commercant) error {
    // Vérifier si le commerçant existe
    existant, err := s.repo.TrouverParID(commercant.ID)
    if err != nil {
        return errors.New("commerçant non trouvé")
    }
    if existant == nil {
        return errors.New("commerçant non trouvé")
    }

    return s.repo.MettreAJour(commercant)
}

func (s *CommercantService) RenouvelerAdhesion(id int, dureeMois int) error {
    // Vérifier si le commerçant existe
    existant, err := s.repo.TrouverParID(id)
    if err != nil || existant == nil {
        return errors.New("commerçant non trouvé")
    }

    nouvelleDateFin := time.Now().AddDate(0, dureeMois, 0)
    return s.repo.RenouvelerAdhesion(id, nouvelleDateFin)
}

func (s *CommercantService) VerifierAdhesionsExpirantes() ([]modeles.Commercant, error) {
    // Alerte 30 jours avant expiration
    return s.repo.TrouverAdhesionsExpirantBientot(30)
}

func (s *CommercantService) Supprimer(id int) error {
    return s.repo.Supprimer(id)
}