package services

import (
	"errors"
	"time"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
	"nomorewaste/internal/utils"
)

type CommercantService struct {
    repo *repositories.CommercantRepository
}

func NouveauCommercantService(repo *repositories.CommercantRepository) *CommercantService {
    return &CommercantService{repo: repo}
}

func (s *CommercantService) Creer(commercant *modeles.CommercantCreation) (int, error) {
    if commercant.Email == "" {
        return 0, errors.New("l'email est requis")
    }
    if commercant.MotDePasse == "" {
        return 0, errors.New("le mot de passe est requis")
    }
    if commercant.Siret == "" {
        return 0, errors.New("le SIRET est requis")
    }
    if commercant.RaisonSociale == "" {
        return 0, errors.New("la raison sociale est requise")
    }
    if err := utils.ValiderComplexiteMotDePasse(commercant.MotDePasse); err != nil {
        return 0, err
    }

    hache, err := utils.HacherMotDePasse(commercant.MotDePasse)
    if err != nil {
        return 0, err
    }
    commercant.MotDePasse = hache

    return s.repo.Creer(commercant)
}

func (s *CommercantService) Lister(raisonSociale, statut, typeCommerce string) ([]modeles.Commercant, error) {
    return s.repo.TrouverTous(raisonSociale, statut, typeCommerce)
}

func (s *CommercantService) TrouverParID(id int) (*modeles.Commercant, error) {
    return s.repo.TrouverParID(id)
}

func (s *CommercantService) ListerTypes() ([]string, error) {
    return s.repo.ListerTypes()
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

func (s *CommercantService) ValiderAdhesion(id int) error {
    // Vérifier l'existence
    _, err := s.repo.TrouverParID(id)
    if err != nil {
        return errors.New("commerçant non trouvé")
    }
    return s.repo.ValiderAdhesion(id)
}

func (s *CommercantService) DemanderRenouvellement(id int) error {
    // Vérifier l'existence
    _, err := s.repo.TrouverParID(id)
    if err != nil {
        return errors.New("commerçant non trouvé")
    }
    return s.repo.DemanderRenouvellement(id)
}

func (s *CommercantService) RenouvelerAdhesion(id int, dureeMois int) error {
    // Vérifier si le commerçant existe
    existant, err := s.repo.TrouverParID(id)
    if err != nil || existant == nil {
        return errors.New("commerçant non trouvé")
    }

    // Le renouvellement prolonge l'adhésion à partir de sa date de fin actuelle
    // (tant qu'elle n'est pas déjà expirée) plutôt que de repartir toujours d'aujourd'hui,
    // sinon renouveler une adhésion encore valide ne fait presque pas bouger sa date de fin.
    base := existant.DateFinAdhesion
    if base.Before(time.Now()) {
        base = time.Now()
    }
    nouvelleDateFin := base.AddDate(0, dureeMois, 0)
    return s.repo.RenouvelerAdhesion(id, nouvelleDateFin)
}

func (s *CommercantService) VerifierAdhesionsExpirantes() ([]modeles.Commercant, error) {
    // Alerte 30 jours avant expiration
    return s.repo.TrouverAdhesionsExpirantBientot(30)
}

func (s *CommercantService) Supprimer(id int) error {
    return s.repo.Supprimer(id)
}