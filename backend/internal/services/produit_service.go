package services

import (
	"errors"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
)

type ProduitService struct {
	repo *repositories.ProduitRepository
}

func NouveauProduitService(repo *repositories.ProduitRepository) *ProduitService {
	return &ProduitService{repo: repo}
}

func (s *ProduitService) Creer(p *modeles.ProduitCreation) (int, error) {
	if p.CodeBarre == "" || p.Nom == "" {
		return 0, errors.New("code barre et nom requis")
	}
	return s.repo.Creer(p)
}

func (s *ProduitService) Lister(codeBarre, statut string) ([]modeles.Produit, error) {
	return s.repo.TrouverTous(codeBarre, statut)
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
