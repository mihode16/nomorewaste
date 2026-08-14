package services

import (
	"errors"
	"time"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
)

var joursValides = map[string]bool{
	"Lundi": true, "Mardi": true, "Mercredi": true, "Jeudi": true,
	"Vendredi": true, "Samedi": true, "Dimanche": true,
}

type DisponibiliteService struct {
	repo *repositories.DisponibiliteRepository
}

func NouveauDisponibiliteService(repo *repositories.DisponibiliteRepository) *DisponibiliteService {
	return &DisponibiliteService{repo: repo}
}

func (s *DisponibiliteService) ListerParBenevole(benevoleID int) ([]modeles.Disponibilite, error) {
	return s.repo.ListerParBenevole(benevoleID)
}

func (s *DisponibiliteService) ListerToutes() ([]modeles.Disponibilite, error) {
	return s.repo.ListerToutes()
}

// lundiDeLaSemaine renvoie le lundi (à minuit) de la semaine contenant t.
func lundiDeLaSemaine(t time.Time) time.Time {
	t = time.Date(t.Year(), t.Month(), t.Day(), 0, 0, 0, 0, t.Location())
	decalage := (int(t.Weekday()) + 6) % 7 // Lundi=0 ... Dimanche=6
	return t.AddDate(0, 0, -decalage)
}

func (s *DisponibiliteService) Creer(d *modeles.Disponibilite) (int, error) {
	if d.BenevoleID <= 0 {
		return 0, errors.New("bénévole requis")
	}
	if !joursValides[d.JourSemaine] {
		return 0, errors.New("jour de la semaine invalide")
	}
	if d.HeureDebut == "" || d.HeureFin == "" || d.HeureDebut >= d.HeureFin {
		return 0, errors.New("créneau horaire invalide")
	}
	date, err := time.Parse("2006-01-02", d.Date)
	if err != nil {
		return 0, errors.New("date invalide")
	}
	lundiCourant := lundiDeLaSemaine(time.Now())
	dimancheProchain := lundiCourant.AddDate(0, 0, 13) // dimanche de la semaine suivante
	if date.Before(lundiCourant) || date.After(dimancheProchain) {
		return 0, errors.New("la disponibilité ne peut être renseignée que pour la semaine courante ou la semaine suivante")
	}
	return s.repo.Creer(d)
}

// Supprimer vérifie que la disponibilité appartient bien au bénévole avant de la retirer.
func (s *DisponibiliteService) Supprimer(id, benevoleID int) error {
	ok, err := s.repo.AppartientA(id, benevoleID)
	if err != nil {
		return err
	}
	if !ok {
		return errors.New("disponibilité introuvable")
	}
	return s.repo.Supprimer(id)
}
