package services

import (
	"errors"
	"time"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
	"nomorewaste/internal/utils"
)

type CollecteService struct {
	repo  *repositories.CollecteRepository
	recap *RecapService
}

func NouveauCollecteService(repo *repositories.CollecteRepository, recap *RecapService) *CollecteService {
	return &CollecteService{repo: repo, recap: recap}
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
	if err := utils.ValiderDateNonPassee(collecte.DateHeureCollecte); err != nil {
		return 0, err
	}
	return s.repo.Creer(collecte)
}

func (s *CollecteService) Lister(commercant, statut, dateDebut, dateFin string, commercantID int) ([]modeles.Collecte, error) {
	return s.repo.TrouverTous(commercant, statut, dateDebut, dateFin, commercantID)
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
	if collecte.DateHeureCollecte.Before(time.Now()) {
		return errors.New("la date ne peut pas être dans le passé")
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
	if err := s.repo.TerminerCollecte(id); err != nil {
		return err
	}
	s.declencherRecapSiTerminee(id)
	return nil
}

// declencherRecapSiTerminee génère (et pour une collecte, envoie par email) le PDF
// récapitulatif si la collecte vient de basculer au statut "Terminée".
func (s *CollecteService) declencherRecapSiTerminee(id int) {
	apres, err := s.repo.TrouverParID(id)
	if err != nil || apres == nil || apres.Statut != "Terminée" {
		return
	}
	s.recap.TraiterCollecteTerminee(apres)
}

// ObtenirPDF retourne le PDF récapitulatif d'une collecte terminée (le génère si besoin).
func (s *CollecteService) ObtenirPDF(id int) ([]byte, error) {
	collecte, err := s.repo.TrouverParID(id)
	if err != nil || collecte == nil {
		return nil, errors.New("collecte non trouvée")
	}
	if collecte.Statut != "Terminée" {
		return nil, errors.New("le PDF n'est disponible qu'une fois la collecte terminée")
	}
	return s.recap.ObtenirCollectePDF(collecte)
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

func (s *CollecteService) SupprimerBenevole(collecteID, benevoleID int) error {
	existant, err := s.repo.TrouverParID(collecteID)
	if err != nil || existant == nil {
		return errors.New("collecte non trouvée")
	}
	return s.repo.SupprimerBenevole(collecteID, benevoleID)
}

func (s *CollecteService) ConfirmerBenevole(collecteID, benevoleID int) error {
	existant, err := s.repo.TrouverParID(collecteID)
	if err != nil || existant == nil {
		return errors.New("collecte non trouvée")
	}
	if existant.Statut != "Planifiée" {
		return errors.New("la collecte n'est pas planifiée ou est déjà terminée")
	}
	if err := s.repo.ConfirmerBenevole(collecteID, benevoleID); err != nil {
		return err
	}
	s.declencherRecapSiTerminee(collecteID)
	return nil
}