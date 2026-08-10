package services

import (
	"database/sql"
	"errors"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
	"nomorewaste/internal/utils"
)

type ProduitService struct {
	repo *repositories.ProduitRepository
}

func NouveauProduitService(repo *repositories.ProduitRepository) *ProduitService {
	return &ProduitService{repo: repo}
}

func (s *ProduitService) Creer(p *modeles.ProduitCreation) (int, error) {
	if p.Nom == "" {
		return 0, errors.New("le nom est requis")
	}

	if p.CodeBarre == "" {
		existsFunc := func(code string) (bool, error) {
			existing, err := s.repo.TrouverParCodeBarre(code)
			if err == nil && existing != nil {
				return true, nil
			}
			if err == sql.ErrNoRows {
				return false, nil
			}
			return false, err
		}
		generated, err := utils.GenerateEAN13(existsFunc)
		if err != nil {
			return 0, err
		}
		p.CodeBarre = generated
	} else {
		if !utils.ValidateEAN13(p.CodeBarre) {
			return 0, errors.New("code-barre EAN-13 invalide")
		}
		existing, _ := s.repo.TrouverParCodeBarre(p.CodeBarre)
		if existing != nil {
			return 0, errors.New("ce code-barre existe déjà")
		}
	}

	return s.repo.Creer(p)
}

func (s *ProduitService) Lister(recherche, categorie, tri string) ([]modeles.Produit, error) {
    return s.repo.TrouverTousFiltres(recherche, categorie, tri)
}

func (s *ProduitService) ListerCategories() ([]string, error) {
    return s.repo.ListerCategories()
}

func (s *ProduitService) TrouverParID(id int) (*modeles.Produit, error) {
	return s.repo.TrouverParID(id)
}

func (s *ProduitService) MettreAJour(p *modeles.Produit) error {
	return s.repo.MettreAJour(p)
}

func (s *ProduitService) Supprimer(id int) error {
	return s.repo.Supprimer(id)
}

func (s *ProduitService) ListerParCollecte(collecteID int) ([]modeles.Produit, error) {
	return s.repo.TrouverParCollecteID(collecteID)
}