package repositories

import (
	"database/sql"
	"log"

	"nomorewaste/internal/modeles"
)

type AuthRepository struct {
	db *sql.DB
}

func NouveauAuthRepository(db *sql.DB) *AuthRepository {
	return &AuthRepository{db: db}
}

func (r *AuthRepository) Authentifier(email, motDePasse string) (*modeles.Utilisateur, error) {
	log.Printf("🔎 Requête SQL pour email=%s, mot_de_passe=%s", email, motDePasse)

	var u modeles.Utilisateur
	var telephone sql.NullString
	var adresse sql.NullString
	var languePreferee sql.NullString

	err := r.db.QueryRow(`
		SELECT id, email, nom, prenom, telephone, adresse, date_inscription, est_actif, langue_preferee, type_utilisateur
		FROM utilisateur
		WHERE email = ? AND mot_de_passe = ? AND est_actif = 1 AND type_utilisateur = 'responsable'
	`, email, motDePasse).Scan(
		&u.ID, &u.Email, &u.Nom, &u.Prenom,
		&telephone, &adresse,
		&u.DateInscription, &u.EstActif,
		&languePreferee, &u.TypeUtilisateur,
	)
	if err != nil {
		log.Printf("❌ Erreur SQL dans Authentifier: %v", err)
		return nil, err
	}

	// Convertir les NullString en string (vide si NULL)
	u.Telephone = telephone.String
	u.Adresse = adresse.String
	u.LanguePreferee = languePreferee.String

	log.Printf("✅ Utilisateur trouvé: id=%d, email=%s, type=%s", u.ID, u.Email, u.TypeUtilisateur)
	return &u, nil
}