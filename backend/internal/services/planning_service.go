package services

import (
	"errors"
	"fmt"
	"log"
	"os"
	"path/filepath"
	"sort"
	"time"

	"nomorewaste/internal/excelgen"
	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
)

type PlanningService struct {
	repo            *repositories.PlanningRepository
	benevoleRepo    *repositories.BenevoleRepository
	collecteRepo    *repositories.CollecteRepository
	tourneeRepo     *repositories.TourneeRepository
	serviceRepo     *repositories.ServiceRepository
	dossierStockage string
}

func NouveauPlanningService(
	repo *repositories.PlanningRepository,
	benevoleRepo *repositories.BenevoleRepository,
	collecteRepo *repositories.CollecteRepository,
	tourneeRepo *repositories.TourneeRepository,
	serviceRepo *repositories.ServiceRepository,
	dossierStockage string,
) *PlanningService {
	if err := os.MkdirAll(dossierStockage, 0755); err != nil {
		log.Printf("⚠️ Impossible de créer le dossier de stockage des plannings (%s): %v", dossierStockage, err)
	}
	return &PlanningService{
		repo: repo, benevoleRepo: benevoleRepo, collecteRepo: collecteRepo,
		tourneeRepo: tourneeRepo, serviceRepo: serviceRepo, dossierStockage: dossierStockage,
	}
}

func (s *PlanningService) chemin(id int) string {
	return filepath.Join(s.dossierStockage, fmt.Sprintf("planning-%d.xlsx", id))
}

// GenererPlanning agrège les collectes, tournées et services affectés à un bénévole sur une
// période donnée, construit le fichier Excel correspondant et le conserve sur disque.
func (s *PlanningService) GenererPlanning(benevoleID int, dateDebutStr, dateFinStr string) (int, error) {
	benevole, err := s.benevoleRepo.TrouverParID(benevoleID)
	if err != nil || benevole == nil {
		return 0, errors.New("bénévole non trouvé")
	}
	dateDebut, err := time.Parse("2006-01-02", dateDebutStr)
	if err != nil {
		return 0, errors.New("date de début invalide")
	}
	dateFin, err := time.Parse("2006-01-02", dateFinStr)
	if err != nil {
		return 0, errors.New("date de fin invalide")
	}
	if dateFin.Before(dateDebut) {
		return 0, errors.New("la date de fin doit être après la date de début")
	}
	finInclusive := dateFin.Add(24*time.Hour - time.Second)

	var lignes []excelgen.LignePlanning

	collectes, err := s.collecteRepo.TrouverTous("", "", dateDebutStr, dateFinStr, 0)
	if err != nil {
		return 0, err
	}
	for _, c := range collectes {
		for _, b := range c.Benevoles {
			if b.BenevoleID == benevoleID {
				statut := c.Statut
				if statut == "" {
					statut = "En attente"
				}
				lignes = append(lignes, excelgen.LignePlanning{
					Date: c.DateHeureCollecte, Type: "Collecte", Detail: c.AdresseCollecte, Statut: statut,
				})
				break
			}
		}
	}

	tournees, err := s.tourneeRepo.TrouverTous("", "", "")
	if err != nil {
		return 0, err
	}
	for _, t := range tournees {
		if t.DateHeureDepart.Before(dateDebut) || t.DateHeureDepart.After(finInclusive) {
			continue
		}
		estAffecte := t.BenevoleID == benevoleID
		for _, b := range t.Benevoles {
			if b.ID == benevoleID {
				estAffecte = true
			}
		}
		if !estAffecte {
			continue
		}
		detail := t.AdresseDepart
		if t.LieuDistribution != nil && t.LieuDistribution.Nom != "" {
			detail += " → " + t.LieuDistribution.Nom
		}
		role := "Bénévole"
		if t.BenevoleID == benevoleID {
			role = "Chauffeur"
		}
		lignes = append(lignes, excelgen.LignePlanning{
			Date: t.DateHeureDepart, Type: "Tournée (" + role + ")", Detail: detail, Statut: t.Statut,
		})
	}

	plannings, err := s.serviceRepo.ListerPlannings()
	if err != nil {
		return 0, err
	}
	for _, p := range plannings {
		if p.BenevoleID != benevoleID {
			continue
		}
		if p.DateHeureDebut.Before(dateDebut) || p.DateHeureDebut.After(finInclusive) {
			continue
		}
		nomService := "Service"
		if p.Service != nil && p.Service.Nom != "" {
			nomService = p.Service.Nom
		}
		lignes = append(lignes, excelgen.LignePlanning{
			Date: p.DateHeureDebut, Type: "Service", Detail: nomService, Statut: p.Statut,
		})
	}

	sort.Slice(lignes, func(i, j int) bool { return lignes[i].Date.Before(lignes[j].Date) })

	id, err := s.repo.Creer(benevoleID, dateDebutStr, dateFinStr)
	if err != nil {
		return 0, err
	}

	nomComplet := benevole.Prenom + " " + benevole.Nom
	periode := dateDebut.Format("02/01/2006") + " au " + dateFin.Format("02/01/2006")
	data, err := excelgen.Generer(nomComplet, periode, lignes)
	if err != nil {
		return 0, err
	}
	if err := os.WriteFile(s.chemin(id), data, 0644); err != nil {
		log.Printf("⚠️ Impossible d'enregistrer le planning %d: %v", id, err)
	}
	return id, nil
}

func (s *PlanningService) ListerParBenevole(benevoleID int) ([]modeles.PlanningBenevole, error) {
	return s.repo.ListerParBenevole(benevoleID)
}

// ObtenirFichier vérifie que le planning appartient bien au bénévole avant de servir le fichier.
func (s *PlanningService) ObtenirFichier(planningID, benevoleID int) ([]byte, error) {
	p, err := s.repo.TrouverParID(planningID)
	if err != nil || p == nil {
		return nil, errors.New("planning non trouvé")
	}
	if p.BenevoleID != benevoleID {
		return nil, errors.New("planning non trouvé")
	}
	return os.ReadFile(s.chemin(planningID))
}
