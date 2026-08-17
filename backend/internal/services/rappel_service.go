package services

import (
	"fmt"
	"log"

	"nomorewaste/internal/config"
	"nomorewaste/internal/repositories"
	"nomorewaste/internal/utils"
)

// RappelService envoie automatiquement un email de rappel aux commerçants et adhérents dont
// l'adhésion approche de son échéance (moins d'un mois), une seule fois par cycle d'adhésion.
type RappelService struct {
	cfg            *config.Config
	rappelRepo     *repositories.RappelRepository
	commercantRepo *repositories.CommercantRepository
	serviceRepo    *repositories.ServiceRepository
}

func NouveauRappelService(cfg *config.Config, rappelRepo *repositories.RappelRepository, commercantRepo *repositories.CommercantRepository, serviceRepo *repositories.ServiceRepository) *RappelService {
	return &RappelService{cfg: cfg, rappelRepo: rappelRepo, commercantRepo: commercantRepo, serviceRepo: serviceRepo}
}

// EnvoyerRappels vérifie les adhésions commerçants et adhérents expirant sous un mois et
// envoie un email de rappel à celles qui n'en ont pas encore reçu un pour leur échéance
// actuelle. Retourne le nombre d'emails effectivement envoyés.
func (s *RappelService) EnvoyerRappels() (int, error) {
	nbEnvoyes := 0

	commercants, err := s.commercantRepo.TrouverAdhesionsExpirantBientot(1)
	if err != nil {
		return nbEnvoyes, fmt.Errorf("lecture adhésions commerçants expirantes: %w", err)
	}
	for _, c := range commercants {
		dejaEnvoye, err := s.rappelRepo.DejaEnvoyeCommercant(c.ID, c.DateFinAdhesion)
		if err != nil {
			log.Printf("⚠️ Vérification rappel commerçant %d: %v", c.ID, err)
			continue
		}
		if dejaEnvoye {
			continue
		}
		sujet := "Votre adhésion NO MORE WASTE arrive bientôt à échéance"
		corps := corpsEmailRappel(c.RaisonSociale, c.DateFinAdhesion.Format("02/01/2006"), s.cfg.SiteURL+"/commercant/adhesion")
		if err := utils.EnvoyerEmail(s.cfg, c.Email, sujet, corps); err != nil {
			log.Printf("⚠️ Échec envoi rappel commerçant %d (%s): %v", c.ID, c.Email, err)
			continue
		}
		if err := s.rappelRepo.MarquerEnvoyeCommercant(c.ID, c.DateFinAdhesion); err != nil {
			log.Printf("⚠️ Échec enregistrement rappel commerçant %d: %v", c.ID, err)
			continue
		}
		nbEnvoyes++
	}

	adherents, err := s.serviceRepo.TrouverAdhesionsAdherentExpirantBientot(1)
	if err != nil {
		return nbEnvoyes, fmt.Errorf("lecture adhésions adhérents expirantes: %w", err)
	}
	for _, a := range adherents {
		dejaEnvoye, err := s.rappelRepo.DejaEnvoyeAdherent(a.ID, a.DateFinAdhesion)
		if err != nil {
			log.Printf("⚠️ Vérification rappel adhérent %d: %v", a.ID, err)
			continue
		}
		if dejaEnvoye {
			continue
		}
		sujet := "Votre adhésion NO MORE WASTE arrive bientôt à échéance"
		corps := corpsEmailRappel(a.Prenom+" "+a.Nom, a.DateFinAdhesion.Format("02/01/2006"), s.cfg.SiteURL+"/adherent/adhesion")
		if err := utils.EnvoyerEmail(s.cfg, a.Email, sujet, corps); err != nil {
			log.Printf("⚠️ Échec envoi rappel adhérent %d (%s): %v", a.ID, a.Email, err)
			continue
		}
		if err := s.rappelRepo.MarquerEnvoyeAdherent(a.ID, a.DateFinAdhesion); err != nil {
			log.Printf("⚠️ Échec enregistrement rappel adhérent %d: %v", a.ID, err)
			continue
		}
		nbEnvoyes++
	}

	return nbEnvoyes, nil
}

func corpsEmailRappel(nomAffiche, dateEcheance, lienEspace string) string {
	return fmt.Sprintf(`
		<p>Bonjour %s,</p>
		<p>Votre adhésion à <strong>NO MORE WASTE</strong> arrive à échéance le <strong>%s</strong>.</p>
		<p>Pour continuer à en profiter sans interruption, pensez à demander son renouvellement depuis votre espace :</p>
		<p><a href="%s">%s</a></p>
		<p>L'équipe NO MORE WASTE</p>
	`, nomAffiche, dateEcheance, lienEspace, lienEspace)
}
