package utils

import (
	"errors"
	"regexp"
	"time"

	"golang.org/x/crypto/bcrypt"
)

// HacherMotDePasse retourne le hachage bcrypt d'un mot de passe en clair, à stocker en base
// à la place du mot de passe lui-même.
func HacherMotDePasse(motDePasse string) (string, error) {
	hache, err := bcrypt.GenerateFromPassword([]byte(motDePasse), bcrypt.DefaultCost)
	if err != nil {
		return "", err
	}
	return string(hache), nil
}

// VerifierMotDePasse compare un mot de passe en clair à un hachage bcrypt stocké en base.
func VerifierMotDePasse(hache, motDePasse string) bool {
	return bcrypt.CompareHashAndPassword([]byte(hache), []byte(motDePasse)) == nil
}

var (
	regexMajuscule = regexp.MustCompile(`[A-ZÀ-Ö]`)
	regexChiffre   = regexp.MustCompile(`[0-9]`)
)

// ValiderComplexiteMotDePasse impose au moins 8 caractères, une majuscule et un chiffre.
func ValiderComplexiteMotDePasse(motDePasse string) error {
	if len(motDePasse) < 8 {
		return errors.New("le mot de passe doit contenir au moins 8 caractères")
	}
	if !regexMajuscule.MatchString(motDePasse) {
		return errors.New("le mot de passe doit contenir au moins une majuscule")
	}
	if !regexChiffre.MatchString(motDePasse) {
		return errors.New("le mot de passe doit contenir au moins un chiffre")
	}
	return nil
}

// ValiderDateNonPassee vérifie qu'une date/heure soumise (au format accepté par ParseDateTime)
// n'est pas antérieure à maintenant, pour empêcher de planifier une collecte, une tournée ou un
// créneau de service dans le passé.
func ValiderDateNonPassee(dateStr string) error {
	date, err := ParseDateTime(dateStr)
	if err != nil {
		return errors.New("date invalide")
	}
	if date.Before(time.Now()) {
		return errors.New("la date ne peut pas être dans le passé")
	}
	return nil
}
