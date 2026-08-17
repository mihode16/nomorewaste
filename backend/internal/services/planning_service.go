package services

import (
	"errors"
	"fmt"
	"log"
	"os"
	"path/filepath"
	"sort"
	"time"

	"nomorewaste/internal/config"
	"nomorewaste/internal/excelgen"
	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
	"nomorewaste/internal/utils"
)

type PlanningService struct {
	repo            *repositories.PlanningRepository
	benevoleRepo    *repositories.BenevoleRepository
	collecteRepo    *repositories.CollecteRepository
	tourneeRepo     *repositories.TourneeRepository
	serviceRepo     *repositories.ServiceRepository
	dossierStockage string
	cfg             *config.Config
}

func NouveauPlanningService(
	repo *repositories.PlanningRepository,
	benevoleRepo *repositories.BenevoleRepository,
	collecteRepo *repositories.CollecteRepository,
	tourneeRepo *repositories.TourneeRepository,
	serviceRepo *repositories.ServiceRepository,
	dossierStockage string,
	cfg *config.Config,
) *PlanningService {
	if err := os.MkdirAll(dossierStockage, 0755); err != nil {
		log.Printf("⚠️ Impossible de créer le dossier de stockage des plannings (%s): %v", dossierStockage, err)
	}
	return &PlanningService{
		repo: repo, benevoleRepo: benevoleRepo, collecteRepo: collecteRepo,
		tourneeRepo: tourneeRepo, serviceRepo: serviceRepo, dossierStockage: dossierStockage, cfg: cfg,
	}
}

func (s *PlanningService) chemin(id int) string {
	return filepath.Join(s.dossierStockage, fmt.Sprintf("planning-%d.xlsx", id))
}

// construireLignes agrège les collectes, tournées et services affectés à un bénévole sur une
// période donnée (bornes incluses), triés chronologiquement.
func (s *PlanningService) construireLignes(benevoleID int, dateDebut, dateFin time.Time) ([]excelgen.LignePlanning, error) {
	dateDebutStr := dateDebut.Format("2006-01-02")
	dateFinStr := dateFin.Format("2006-01-02")
	finInclusive := dateFin.Add(24*time.Hour - time.Second)

	var lignes []excelgen.LignePlanning

	collectes, err := s.collecteRepo.TrouverTous("", "", dateDebutStr, dateFinStr, 0)
	if err != nil {
		return nil, err
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
		return nil, err
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
		return nil, err
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
	return lignes, nil
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

	lignes, err := s.construireLignes(benevoleID, dateDebut, dateFin)
	if err != nil {
		return 0, err
	}

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

// GenererEtEnvoyerPlanningsDuJour génère, pour chaque bénévole validé ayant au moins une
// affectation aujourd'hui (collecte, tournée ou service), son planning du jour au format
// Excel, et le lui envoie par email en pièce jointe. Conçu pour être appelé une fois par jour
// par la tâche planifiée. Retourne le nombre d'emails effectivement envoyés.
func (s *PlanningService) GenererEtEnvoyerPlanningsDuJour() (int, error) {
	benevoles, err := s.benevoleRepo.TrouverValides()
	if err != nil {
		return 0, fmt.Errorf("lecture des bénévoles validés: %w", err)
	}

	aujourdhui := time.Now()
	debutJour := time.Date(aujourdhui.Year(), aujourdhui.Month(), aujourdhui.Day(), 0, 0, 0, 0, aujourdhui.Location())
	finJour := debutJour

	nbEnvoyes := 0
	for _, b := range benevoles {
		lignes, err := s.construireLignes(b.ID, debutJour, finJour)
		if err != nil {
			log.Printf("⚠️ Construction planning du jour pour bénévole %d: %v", b.ID, err)
			continue
		}
		if len(lignes) == 0 {
			continue
		}

		nomComplet := b.Prenom + " " + b.Nom
		periode := debutJour.Format("02/01/2006")
		data, err := excelgen.Generer(nomComplet, periode, lignes)
		if err != nil {
			log.Printf("⚠️ Génération Excel planning du jour pour bénévole %d: %v", b.ID, err)
			continue
		}

		id, err := s.repo.Creer(b.ID, debutJour.Format("2006-01-02"), finJour.Format("2006-01-02"))
		if err != nil {
			log.Printf("⚠️ Enregistrement planning du jour pour bénévole %d: %v", b.ID, err)
			continue
		}
		if err := os.WriteFile(s.chemin(id), data, 0644); err != nil {
			log.Printf("⚠️ Écriture fichier planning du jour pour bénévole %d: %v", b.ID, err)
		}

		sujet := fmt.Sprintf("Votre planning du %s — NO MORE WASTE", debutJour.Format("02/01/2006"))
		corps := fmt.Sprintf(`
			<p>Bonjour %s,</p>
			<p>Voici votre planning pour aujourd'hui (%s), en pièce jointe.</p>
			<p>Vous pouvez également le retrouver à tout moment dans votre espace bénévole.</p>
			<p>L'équipe NO MORE WASTE</p>
		`, nomComplet, debutJour.Format("02/01/2006"))
		nomFichier := fmt.Sprintf("planning-%s.xlsx", debutJour.Format("2006-01-02"))
		if err := utils.EnvoyerEmail(s.cfg, b.Email, sujet, corps, utils.PieceJointe{Nom: nomFichier, Contenu: data}); err != nil {
			log.Printf("⚠️ Échec envoi email planning à %s: %v", b.Email, err)
			continue
		}
		nbEnvoyes++
	}

	return nbEnvoyes, nil
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
