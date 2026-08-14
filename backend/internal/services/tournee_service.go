package services

import (
	"errors"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
	"nomorewaste/internal/utils"
)

type TourneeService struct {
	repo  *repositories.TourneeRepository
	recap *RecapService
}

func NouveauTourneeService(repo *repositories.TourneeRepository, recap *RecapService) *TourneeService {
	return &TourneeService{repo: repo, recap: recap}
}

func (s *TourneeService) Creer(t *modeles.TourneeCreation) (int, error) {
	if t.AdresseDepart == "" || t.BenevoleID <= 0 || t.LieuDistributionID <= 0 {
		return 0, errors.New("adresse, bénévole chauffeur et lieu requis")
	}
	if err := utils.ValiderDateNonPassee(t.DateHeureDepart); err != nil {
		return 0, err
	}
	return s.repo.Creer(t)
}

func (s *TourneeService) Lister(statut, destination, date string) ([]modeles.Tournee, error) {
	return s.repo.TrouverTous(statut, destination, date)
}

func (s *TourneeService) TrouverParID(id int) (*modeles.Tournee, error) {
	return s.repo.TrouverParID(id)
}

func (s *TourneeService) Terminer(id int) error {
	avant, err := s.repo.TrouverParID(id)
	if err != nil || avant == nil {
		return errors.New("tournée non trouvée")
	}
	dejaTerminee := avant.Statut == "Terminée"
	if err := s.repo.Terminer(id); err != nil {
		return err
	}
	if !dejaTerminee {
		s.declencherRecapSiTerminee(id)
	}
	return nil
}

// declencherRecapSiTerminee génère le PDF récapitulatif si la tournée vient de basculer
// au statut "Terminée".
func (s *TourneeService) declencherRecapSiTerminee(id int) {
	apres, err := s.repo.TrouverParID(id)
	if err != nil || apres == nil || apres.Statut != "Terminée" {
		return
	}
	s.recap.TraiterTourneeTerminee(apres)
}

// ObtenirPDF retourne le PDF récapitulatif d'une tournée terminée (le génère si besoin).
func (s *TourneeService) ObtenirPDF(id int) ([]byte, error) {
	t, err := s.repo.TrouverParID(id)
	if err != nil || t == nil {
		return nil, errors.New("tournée non trouvée")
	}
	if t.Statut != "Terminée" {
		return nil, errors.New("le PDF n'est disponible qu'une fois la tournée terminée")
	}
	return s.recap.ObtenirTourneePDF(t)
}

// ConfirmerBenevole enregistre qu'un bénévole associé (chauffeur ou supplémentaire) a
// terminé la tournée. Quand tous les bénévoles associés ont confirmé, la tournée est
// automatiquement marquée "Terminée".
func (s *TourneeService) ConfirmerBenevole(tourneeID, benevoleID int) error {
	if benevoleID <= 0 {
		return errors.New("bénévole invalide")
	}
	t, err := s.repo.TrouverParID(tourneeID)
	if err != nil || t == nil {
		return errors.New("tournée non trouvée")
	}
	if t.Statut == "Terminée" {
		return errors.New("la tournée est déjà terminée")
	}
	if err := s.repo.ConfirmerBenevole(tourneeID, benevoleID); err != nil {
		return err
	}
	s.declencherRecapSiTerminee(tourneeID)
	return nil
}

func (s *TourneeService) ListerLieux() ([]modeles.LieuDistribution, error) {
	return s.repo.ListerLieux()
}

func (s *TourneeService) AjouterLieu(l *modeles.LieuDistribution) (int, error) {
	if l.Nom == "" || l.Adresse == "" {
		return 0, errors.New("nom et adresse requis")
	}
	return s.repo.AjouterLieu(l)
}

func (s *TourneeService) MettreAJour(t *modeles.TourneeUpdate) error {
    if t.BenevoleID <= 0 || t.LieuDistributionID <= 0 {
        return errors.New("bénévole chauffeur et lieu requis")
    }
    if err := utils.ValiderDateNonPassee(t.DateHeureDepart); err != nil {
        return err
    }
    return s.repo.MettreAJour(t)
}

func (s *TourneeService) Supprimer(id int) error {
    return s.repo.Supprimer(id)
}

func (s *TourneeService) CompterBenevoles(tourneeID int) (int, error) {
    return s.repo.CompterBenevoles(tourneeID)
}