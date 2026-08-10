package services

import (
	"errors"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
)

type CollecteService struct {
	repo *repositories.CollecteRepository
}

func NouveauCollecteService(repo *repositories.CollecteRepository) *CollecteService {
	return &CollecteService{repo: repo}
}

func (s *CollecteService) Creer(collecte *modeles.CollecteCreation) (int, error) {
	if collecte.DateHeureCollecte == "" {
		return 0, errors.New("la date et l'heure de la collecte sont requises")
	}
	if collecte.AdresseCollecte == "" {
		return 0, errors.New("l'adresse de la collecte est requise")
	}
	if collecte.CommercantID <= 0 {
		return 0, errors.New("un commerçant valide est requis")
	}
	return s.repo.Creer(collecte)
}

func (s *CollecteService) Lister(commercant, statut, dateDebut, dateFin string) ([]modeles.Collecte, error) {
	return s.repo.TrouverTous(commercant, statut, dateDebut, dateFin)
}

func (s *CollecteService) TrouverParID(id int) (*modeles.Collecte, error) {
	return s.repo.TrouverParID(id)
}

func (s *CollecteService) ValiderCollecte(id int) error {
	existant, err := s.repo.TrouverParID(id)
	if err != nil || existant == nil {
		return errors.New("collecte non trouvée")
	}
	return s.repo.ValiderCollecte(id)
}

func (s *CollecteService) MettreAJour(collecte *modeles.Collecte) error {
	existant, err := s.repo.TrouverParID(collecte.ID)
	if err != nil || existant == nil {
		return errors.New("collecte non trouvée")
	}
	return s.repo.MettreAJour(collecte)
}

func (s *CollecteService) MarquerTerminee(id int) error {
	existant, err := s.repo.TrouverParID(id)
	if err != nil || existant == nil {
		return errors.New("collecte non trouvée")
	}
	if existant.Statut != "Planifiée" {
		return errors.New("la collecte n'est pas planifiée")
	}
	return s.repo.TerminerCollecte(id)
}

func (s *CollecteService) Supprimer(id int) error {
	return s.repo.Supprimer(id)
}

func (s *CollecteService) AjouterBenevole(collecteID, benevoleID int) error {
	existant, err := s.repo.TrouverParID(collecteID)
	if err != nil || existant == nil {
		return errors.New("collecte non trouvée")
	}
	return s.repo.AjouterBenevole(collecteID, benevoleID)
}

func (s *CollecteService) ConfirmerBenevole(collecteID, benevoleID int) error {
	existant, err := s.repo.TrouverParID(collecteID)
	if err != nil || existant == nil {
		return errors.New("collecte non trouvée")
	}
	if existant.Statut != "Planifiée" {
		return errors.New("la collecte n'est pas planifiée ou est déjà terminée")
	}
	return s.repo.ConfirmerBenevole(collecteID, benevoleID)
}