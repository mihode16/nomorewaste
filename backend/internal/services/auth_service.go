package services

import (
	"errors"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
)

type AuthService struct {
	repo *repositories.AuthRepository
}

func NouveauAuthService(repo *repositories.AuthRepository) *AuthService {
	return &AuthService{repo: repo}
}

func (s *AuthService) Login(req *modeles.LoginRequest) (*modeles.Utilisateur, error) {
	if req.Email == "" || req.MotDePasse == "" {
		return nil, errors.New("email et mot de passe requis")
	}
	return s.repo.Authentifier(req.Email, req.MotDePasse)
}
