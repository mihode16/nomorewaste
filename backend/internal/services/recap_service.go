package services

import (
	"fmt"
	"log"
	"os"
	"path/filepath"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/pdfgen"
	"nomorewaste/internal/repositories"
)

// RecapService génère les PDF récapitulatifs de collectes et tournées terminées et les
// conserve sur disque.
type RecapService struct {
	produitRepo     *repositories.ProduitRepository
	logoPath        string
	dossierStockage string
}

func NouveauRecapService(produitRepo *repositories.ProduitRepository, logoPath, dossierStockage string) *RecapService {
	if err := os.MkdirAll(dossierStockage, 0755); err != nil {
		log.Printf("⚠️ Impossible de créer le dossier de stockage des PDF (%s): %v", dossierStockage, err)
	}
	return &RecapService{
		produitRepo:     produitRepo,
		logoPath:        logoPath,
		dossierStockage: dossierStockage,
	}
}

func (s *RecapService) chemin(prefixe string, id int) string {
	return filepath.Join(s.dossierStockage, fmt.Sprintf("%s-%d.pdf", prefixe, id))
}

// ---- Collectes ----

func (s *RecapService) construireDocCollecte(collecte *modeles.Collecte) (pdfgen.DocumentRecap, error) {
	produits, err := s.produitRepo.TrouverParCollecteID(collecte.ID)
	if err != nil {
		return pdfgen.DocumentRecap{}, err
	}

	benevoles := make([]pdfgen.LigneBenevole, 0, len(collecte.Benevoles))
	for _, b := range collecte.Benevoles {
		benevoles = append(benevoles, pdfgen.LigneBenevole{ID: b.BenevoleID, Nom: b.BenevoleNom, Prenom: b.BenevolePrenom})
	}

	lignesProduits := make([]pdfgen.LigneProduit, 0, len(produits))
	for _, p := range produits {
		lignesProduits = append(lignesProduits, pdfgen.LigneProduit{ID: p.ID, CodeBarre: p.CodeBarre, Nom: p.Nom, Quantite: p.Quantite})
	}

	adresses := []pdfgen.LigneInfo{
		{Label: "Adresse de collecte", Valeur: collecte.AdresseCollecte},
	}
	if collecte.Commercant != nil {
		nom := collecte.Commercant.RaisonSociale
		if nom == "" {
			nom = collecte.Commercant.Prenom + " " + collecte.Commercant.Nom
		}
		adresses = append(adresses, pdfgen.LigneInfo{Label: "Commerçant", Valeur: nom})
	}

	var complements []pdfgen.LigneInfo
	if collecte.Commentaire != "" {
		complements = append(complements, pdfgen.LigneInfo{Label: "Commentaire", Valeur: collecte.Commentaire})
	}

	return pdfgen.DocumentRecap{
		TypeDocument:   "Collecte",
		NumeroID:       collecte.ID,
		Statut:         collecte.Statut,
		DateDebut:      collecte.DateHeureCollecte,
		LabelDateDebut: "Date de collecte",
		Adresses:       adresses,
		Complements:    complements,
		Benevoles:      benevoles,
		Produits:       lignesProduits,
		TitreProduits:  "Produits collectés",
	}, nil
}

func (s *RecapService) genererCollectePDF(collecte *modeles.Collecte) ([]byte, error) {
	doc, err := s.construireDocCollecte(collecte)
	if err != nil {
		return nil, err
	}
	data, err := pdfgen.Generer(doc, s.logoPath)
	if err != nil {
		return nil, err
	}
	if err := os.WriteFile(s.chemin("collecte", collecte.ID), data, 0644); err != nil {
		log.Printf("⚠️ Impossible d'enregistrer le PDF de la collecte %d: %v", collecte.ID, err)
	}
	return data, nil
}

// ObtenirCollectePDF sert le PDF déjà généré, ou le génère à la volée s'il est absent.
func (s *RecapService) ObtenirCollectePDF(collecte *modeles.Collecte) ([]byte, error) {
	if data, err := os.ReadFile(s.chemin("collecte", collecte.ID)); err == nil {
		return data, nil
	}
	return s.genererCollectePDF(collecte)
}

// TraiterCollecteTerminee génère le PDF récapitulatif d'une collecte qui vient de se terminer.
func (s *RecapService) TraiterCollecteTerminee(collecte *modeles.Collecte) {
	if _, err := s.genererCollectePDF(collecte); err != nil {
		log.Printf("❌ Erreur génération PDF collecte %d: %v", collecte.ID, err)
	}
}

// ---- Tournées ----

func (s *RecapService) construireDocTournee(t *modeles.Tournee) pdfgen.DocumentRecap {
	benevoles := make([]pdfgen.LigneBenevole, 0, len(t.Benevoles)+1)
	if t.Benevole != nil {
		benevoles = append(benevoles, pdfgen.LigneBenevole{ID: t.BenevoleID, Nom: t.Benevole.Nom, Prenom: t.Benevole.Prenom, Role: "Chauffeur"})
	}
	for _, b := range t.Benevoles {
		benevoles = append(benevoles, pdfgen.LigneBenevole{ID: b.ID, Nom: b.Nom, Prenom: b.Prenom})
	}

	lignesProduits := make([]pdfgen.LigneProduit, 0, len(t.Produits))
	for _, p := range t.Produits {
		lignesProduits = append(lignesProduits, pdfgen.LigneProduit{ID: p.ID, CodeBarre: p.CodeBarre, Nom: p.Nom, Quantite: p.Quantite})
	}

	adresses := []pdfgen.LigneInfo{
		{Label: "Adresse de départ", Valeur: t.AdresseDepart},
	}
	if t.LieuDistribution != nil {
		adresses = append(adresses, pdfgen.LigneInfo{Label: "Lieu de distribution", Valeur: t.LieuDistribution.Nom + " — " + t.LieuDistribution.Adresse})
	}

	return pdfgen.DocumentRecap{
		TypeDocument:   "Tournée",
		NumeroID:       t.ID,
		Statut:         t.Statut,
		DateDebut:      t.DateHeureDepart,
		LabelDateDebut: "Date de départ",
		DateFin:        t.DateHeureFin,
		Adresses:       adresses,
		Benevoles:      benevoles,
		Produits:       lignesProduits,
		TitreProduits:  "Produits livrés",
	}
}

func (s *RecapService) genererTourneePDF(t *modeles.Tournee) ([]byte, error) {
	doc := s.construireDocTournee(t)
	data, err := pdfgen.Generer(doc, s.logoPath)
	if err != nil {
		return nil, err
	}
	if err := os.WriteFile(s.chemin("tournee", t.ID), data, 0644); err != nil {
		log.Printf("⚠️ Impossible d'enregistrer le PDF de la tournée %d: %v", t.ID, err)
	}
	return data, nil
}

// ObtenirTourneePDF sert le PDF déjà généré, ou le génère à la volée s'il est absent.
func (s *RecapService) ObtenirTourneePDF(t *modeles.Tournee) ([]byte, error) {
	if data, err := os.ReadFile(s.chemin("tournee", t.ID)); err == nil {
		return data, nil
	}
	return s.genererTourneePDF(t)
}

// TraiterTourneeTerminee génère le PDF récapitulatif d'une tournée qui vient de se terminer.
func (s *RecapService) TraiterTourneeTerminee(t *modeles.Tournee) {
	if _, err := s.genererTourneePDF(t); err != nil {
		log.Printf("❌ Erreur génération PDF tournée %d: %v", t.ID, err)
	}
}
