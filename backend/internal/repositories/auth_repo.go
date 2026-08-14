package repositories

import (
	"database/sql"
	"errors"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/utils"
)

// ErrCompteEnAttente signale des identifiants corrects mais un profil (commerçant, adhérent ou
// bénévole) pas encore validé par un admin : la connexion doit rester bloquée jusque-là.
var ErrCompteEnAttente = errors.New("compte en attente de validation")

// ErrIdentifiantsIncorrects signale un email inconnu ou un mot de passe erroné.
var ErrIdentifiantsIncorrects = errors.New("identifiants incorrects")

type AuthRepository struct {
	db *sql.DB
}

func NouveauAuthRepository(db *sql.DB) *AuthRepository {
	return &AuthRepository{db: db}
}

func (r *AuthRepository) Authentifier(email, motDePasse string) (*modeles.Utilisateur, error) {
	var u modeles.Utilisateur
	var hache string
	var telephone sql.NullString
	var adresse sql.NullString
	var languePreferee sql.NullString

	err := r.db.QueryRow(`
		SELECT id, email, mot_de_passe, nom, prenom, telephone, adresse, date_inscription, est_actif, langue_preferee, type_utilisateur
		FROM utilisateur
		WHERE email = ? AND est_actif = 1 AND type_utilisateur IN ('responsable', 'commercant', 'adherent', 'benevole')
	`, email).Scan(
		&u.ID, &u.Email, &hache, &u.Nom, &u.Prenom,
		&telephone, &adresse,
		&u.DateInscription, &u.EstActif,
		&languePreferee, &u.TypeUtilisateur,
	)
	if err != nil {
		return nil, ErrIdentifiantsIncorrects
	}

	if !utils.VerifierMotDePasse(hache, motDePasse) {
		return nil, ErrIdentifiantsIncorrects
	}

	// Convertir les NullString en string (vide si NULL)
	u.Telephone = telephone.String
	u.Adresse = adresse.String
	u.LanguePreferee = languePreferee.String

	if err := r.verifierProfilValide(&u); err != nil {
		return nil, err
	}

	return &u, nil
}

// verifierProfilValide bloque la connexion tant que le profil du commerçant/adhérent/bénévole
// n'a pas été validé par un admin (les comptes responsable n'ont pas de validation à part).
func (r *AuthRepository) verifierProfilValide(u *modeles.Utilisateur) error {
	var statut string
	var err error
	switch u.TypeUtilisateur {
	case "commercant":
		err = r.db.QueryRow(`SELECT statut_adhesion FROM commercant WHERE id = ?`, u.ID).Scan(&statut)
		if err == nil && statut != "valide" {
			return ErrCompteEnAttente
		}
	case "adherent":
		err = r.db.QueryRow(`SELECT statut_adhesion FROM adherent WHERE id = ?`, u.ID).Scan(&statut)
		if err == nil && statut != "valide" {
			return ErrCompteEnAttente
		}
	case "benevole":
		err = r.db.QueryRow(`SELECT statut_candidature FROM benevole WHERE id = ?`, u.ID).Scan(&statut)
		if err == nil && statut != "Validé" {
			return ErrCompteEnAttente
		}
	}
	if err != nil {
		return err
	}
	return nil
}