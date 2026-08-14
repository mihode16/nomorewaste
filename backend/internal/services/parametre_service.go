package services

import (
	"errors"
	"fmt"
	"strconv"

	"nomorewaste/internal/repositories"
)

const CleAdhesionPrixMensuel = "prix_adhesion_mensuel"
const prixAdhesionParDefaut = 7.99

type ParametreService struct {
	repo *repositories.ParametreRepository
}

func NouveauParametreService(repo *repositories.ParametreRepository) *ParametreService {
	return &ParametreService{repo: repo}
}

func (s *ParametreService) ObtenirPrixAdhesion() (float64, error) {
	valeur, err := s.repo.Obtenir(CleAdhesionPrixMensuel)
	if err != nil {
		return prixAdhesionParDefaut, nil
	}
	prix, err := strconv.ParseFloat(valeur, 64)
	if err != nil {
		return prixAdhesionParDefaut, nil
	}
	return prix, nil
}

func (s *ParametreService) DefinirPrixAdhesion(prix float64) error {
	if prix <= 0 {
		return errors.New("le prix doit être strictement positif")
	}
	return s.repo.Definir(CleAdhesionPrixMensuel, fmt.Sprintf("%.2f", prix))
}
