package repositories

import "errors"

// ErrEmailExiste signale qu'un compte existe déjà avec cette adresse email, pour renvoyer un
// message clair au lieu de laisser remonter l'erreur SQL de clé dupliquée.
var ErrEmailExiste = errors.New("cette adresse email est déjà utilisée")
