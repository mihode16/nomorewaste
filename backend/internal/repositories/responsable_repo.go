package repositories

import (
	"database/sql"

	"nomorewaste/internal/modeles"
)

type ResponsableRepository struct {
	db *sql.DB
}

func NouveauResponsableRepository(db *sql.DB) *ResponsableRepository {
	return &ResponsableRepository{db: db}
}

func (r *ResponsableRepository) Creer(u *modeles.Utilisateur) (int, error) {
	var nbExistants int
	if err := r.db.QueryRow(`SELECT COUNT(*) FROM utilisateur WHERE email = ?`, u.Email).Scan(&nbExistants); err != nil {
		return 0, err
	}
	if nbExistants > 0 {
		return 0, ErrEmailExiste
	}

	result, err := r.db.Exec(`
		INSERT INTO utilisateur (email, mot_de_passe, nom, prenom, telephone, adresse, type_utilisateur)
		VALUES (?, ?, ?, ?, ?, ?, 'responsable')
	`, u.Email, u.MotDePasse, u.Nom, u.Prenom, u.Telephone, u.Adresse)
	if err != nil {
		return 0, err
	}
	id, _ := result.LastInsertId()
	return int(id), nil
}

func (r *ResponsableRepository) TrouverTous() ([]modeles.Utilisateur, error) {
	rows, err := r.db.Query(`
		SELECT id, email, nom, prenom, telephone, adresse, date_inscription, est_actif, langue_preferee, type_utilisateur
		FROM utilisateur
		WHERE type_utilisateur = 'responsable' AND est_actif = 1
		ORDER BY nom
	`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	var list []modeles.Utilisateur
	for rows.Next() {
		u, err := scanResponsableRow(rows)
		if err != nil {
			return nil, err
		}
		list = append(list, *u)
	}
	return list, nil
}

func scanResponsableRow(rows *sql.Rows) (*modeles.Utilisateur, error) {
	var u modeles.Utilisateur
	var telephone, adresse, langue sql.NullString
	if err := rows.Scan(&u.ID, &u.Email, &u.Nom, &u.Prenom, &telephone, &adresse, &u.DateInscription, &u.EstActif, &langue, &u.TypeUtilisateur); err != nil {
		return nil, err
	}
	u.Telephone = telephone.String
	u.Adresse = adresse.String
	u.LanguePreferee = langue.String
	return &u, nil
}

func (r *ResponsableRepository) TrouverParID(id int) (*modeles.Utilisateur, error) {
	rows, err := r.db.Query(`
		SELECT id, email, nom, prenom, telephone, adresse, date_inscription, est_actif, langue_preferee, type_utilisateur
		FROM utilisateur
		WHERE id = ? AND type_utilisateur = 'responsable' AND est_actif = 1
	`, id)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	if !rows.Next() {
		return nil, sql.ErrNoRows
	}
	return scanResponsableRow(rows)
}

func (r *ResponsableRepository) CompterActifs() (int, error) {
	var n int
	err := r.db.QueryRow(`SELECT COUNT(*) FROM utilisateur WHERE type_utilisateur = 'responsable' AND est_actif = 1`).Scan(&n)
	return n, err
}

func (r *ResponsableRepository) MettreAJour(u *modeles.Utilisateur) error {
	if u.MotDePasse != "" {
		_, err := r.db.Exec(`
			UPDATE utilisateur SET nom = ?, prenom = ?, telephone = ?, adresse = ?, mot_de_passe = ?
			WHERE id = ? AND type_utilisateur = 'responsable'
		`, u.Nom, u.Prenom, u.Telephone, u.Adresse, u.MotDePasse, u.ID)
		return err
	}
	_, err := r.db.Exec(`
		UPDATE utilisateur SET nom = ?, prenom = ?, telephone = ?, adresse = ?
		WHERE id = ? AND type_utilisateur = 'responsable'
	`, u.Nom, u.Prenom, u.Telephone, u.Adresse, u.ID)
	return err
}

func (r *ResponsableRepository) Supprimer(id int) error {
	_, err := r.db.Exec(`UPDATE utilisateur SET est_actif = 0 WHERE id = ? AND type_utilisateur = 'responsable'`, id)
	return err
}
