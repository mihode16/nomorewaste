package utils

import (
	"errors"
	"math/rand"
	"strconv"
)

// GenerateEAN13 génère un code EAN-13 valide et unique.
// La fonction existsFunc vérifie si le code existe déjà en base.
func GenerateEAN13(existsFunc func(string) (bool, error)) (string, error) {
	prefix := "376"
	maxAttempts := 30

	for i := 0; i < maxAttempts; i++ {
		// 9 chiffres aléatoires (100000000 à 999999999)
		n := rand.Intn(900000000) + 100000000
		code12 := prefix + strconv.Itoa(n)
		check := calculateEAN13CheckDigit(code12)
		fullCode := code12 + strconv.Itoa(check)

		exists, err := existsFunc(fullCode)
		if err != nil {
			return "", err
		}
		if !exists {
			return fullCode, nil
		}
	}
	return "", errors.New("impossible de générer un code-barres unique après plusieurs tentatives")
}

// calculateEAN13CheckDigit calcule la clé de contrôle pour 12 premiers chiffres
func calculateEAN13CheckDigit(code string) int {
	sum := 0
	for i, ch := range code {
		digit := int(ch - '0')
		if i%2 == 0 {
			sum += digit
		} else {
			sum += digit * 3
		}
	}
	return (10 - (sum % 10)) % 10
}

// ValidateEAN13 vérifie qu'un code est valide (13 chiffres et clé correcte)
func ValidateEAN13(code string) bool {
	if len(code) != 13 {
		return false
	}
	for _, c := range code {
		if c < '0' || c > '9' {
			return false
		}
	}
	sum := 0
	for i := 0; i < 12; i++ {
		digit := int(code[i] - '0')
		if i%2 == 0 {
			sum += digit
		} else {
			sum += digit * 3
		}
	}
	checkDigit := (10 - (sum % 10)) % 10
	return checkDigit == int(code[12]-'0')
}