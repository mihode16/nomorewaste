package repositories

import (
	"database/sql"

	"nomorewaste/internal/modeles"
)

type BenevoleRepository struct {
	db *sql.DB
}

func NouveauBenevoleRepository(db *sql.DB) *BenevoleRepository {
	return &BenevoleRepository{db: db}
}

func (r *BenevoleRepository) Creer(b *modeles.BenevoleCreation) (int, error) {
	tx, err := r.db.Begin()
	if err != nil {
		return 0, err
	}
	defer tx.Rollback()

	var nbExistants int
	if err = tx.QueryRow(`SELECT COUNT(*) FROM utilisateur WHERE email = ?`, b.Email).Scan(&nbExistants); err != nil {
		return 0, err
	}
	if nbExistants > 0 {
		return 0, ErrEmailExiste
	}

	result, err := tx.Exec(`
		INSERT INTO utilisateur (email, mot_de_passe, nom, prenom, telephone, adresse, type_utilisateur)
		VALUES (?, ?, ?, ?, ?, ?, 'benevole')
	`, b.Email, b.MotDePasse, b.Nom, b.Prenom, b.Telephone, b.Adresse)
	if err != nil {
		return 0, err
	}
	id, _ := result.LastInsertId()

	_, err = tx.Exec(`INSERT INTO benevole (id, statut_candidature) VALUES (?, 'En attente')`, id)
	if err != nil {
		return 0, err
	}

	for _, cid := range b.Competences {
		if _, err = tx.Exec(`INSERT INTO benevole_competence (benevole_id, competence_id) VALUES (?, ?)`, id, cid); err != nil {
			return 0, err
		}
	}

	if err = tx.Commit(); err != nil {
		return 0, err
	}
	return int(id), nil
}

func (r *BenevoleRepository) TrouverTous(nom, statut string, competenceID int) ([]modeles.Benevole, error) {
    query := `
        SELECT u.id, u.email, u.nom, u.prenom, u.telephone, u.adresse, u.date_inscription, u.est_actif,
               b.date_candidature, b.statut_candidature
        FROM utilisateur u
        JOIN benevole b ON u.id = b.id
        WHERE u.est_actif = 1
    `
    var args []interface{}

    if nom != "" {
        query += " AND (u.nom LIKE ? OR u.prenom LIKE ?)"
        like := "%" + nom + "%"
        args = append(args, like, like)
    }
    if statut != "" {
        query += " AND b.statut_candidature = ?"
        args = append(args, statut)
    }
    if competenceID > 0 {
        query += " AND EXISTS (SELECT 1 FROM benevole_competence bc WHERE bc.benevole_id = b.id AND bc.competence_id = ?)"
        args = append(args, competenceID)
    }
    query += " ORDER BY b.date_candidature DESC"

    rows, err := r.db.Query(query, args...)
    if err != nil {
        return nil, err
    }
    defer rows.Close()

    var list []modeles.Benevole
    for rows.Next() {
        var b modeles.Benevole
        var telephone, adresse sql.NullString
        var dateCandidature sql.NullTime

        err := rows.Scan(
            &b.ID, &b.Email, &b.Nom, &b.Prenom, &telephone, &adresse,
            &b.DateInscription, &b.EstActif,
            &dateCandidature, &b.StatutCandidature,
        )
        if err != nil {
            return nil, err
        }

        b.Telephone = telephone.String
        b.Adresse = adresse.String
        if dateCandidature.Valid {
            b.DateCandidature = dateCandidature.Time
        }

        b.Competences, _ = r.competencesPour(int(b.ID))
        list = append(list, b)
    }
    return list, nil
}

func (r *BenevoleRepository) competencesPour(benevoleID int) ([]modeles.Competence, error) {
	rows, err := r.db.Query(`
		SELECT c.id, c.nom, c.description FROM competence c
		JOIN benevole_competence bc ON bc.competence_id = c.id
		WHERE bc.benevole_id = ?
	`, benevoleID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	var comps []modeles.Competence
	for rows.Next() {
		var c modeles.Competence
		if err := rows.Scan(&c.ID, &c.Nom, &c.Description); err != nil {
			return nil, err
		}
		comps = append(comps, c)
	}
	return comps, nil
}

func (r *BenevoleRepository) TrouverParID(id int) (*modeles.Benevole, error) {
	var b modeles.Benevole
	var telephone, adresse sql.NullString
	var dateCandidature sql.NullTime

	err := r.db.QueryRow(`
		SELECT u.id, u.email, u.nom, u.prenom, u.telephone, u.adresse, u.date_inscription, u.est_actif,
		       bv.date_candidature, bv.statut_candidature
		FROM utilisateur u
		JOIN benevole bv ON u.id = bv.id
		WHERE u.id = ?
	`, id).Scan(
		&b.ID, &b.Email, &b.Nom, &b.Prenom, &telephone, &adresse,
		&b.DateInscription, &b.EstActif,
		&dateCandidature, &b.StatutCandidature,
	)
	if err != nil {
		return nil, err
	}

	b.Telephone = telephone.String
	b.Adresse = adresse.String
	if dateCandidature.Valid {
		b.DateCandidature = dateCandidature.Time
	}

	b.Competences, _ = r.competencesPour(id)
	return &b, nil
}

func (r *BenevoleRepository) MettreAJour(id int, b *modeles.BenevoleMiseAJour) error {
	_, err := r.db.Exec(`
		UPDATE utilisateur SET nom = ?, prenom = ?, telephone = ?, adresse = ? WHERE id = ?
	`, b.Nom, b.Prenom, b.Telephone, b.Adresse, id)
	return err
}

func (r *BenevoleRepository) ChangerStatut(id int, statut string) error {
	_, err := r.db.Exec(`UPDATE benevole SET statut_candidature = ? WHERE id = ?`, statut, id)
	return err
}

func (r *BenevoleRepository) ListerCompetences() ([]modeles.Competence, error) {
	rows, err := r.db.Query(`SELECT id, nom, description FROM competence ORDER BY nom`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	var list []modeles.Competence
	for rows.Next() {
		var c modeles.Competence
		if err := rows.Scan(&c.ID, &c.Nom, &c.Description); err != nil {
			return nil, err
		}
		list = append(list, c)
	}
	return list, nil
}

func (r *BenevoleRepository) TrouverValides() ([]modeles.Benevole, error) {
    return r.TrouverTous("", "Validé", 0)
}

func (r *BenevoleRepository) Supprimer(id int) error {
	_, err := r.db.Exec(`UPDATE utilisateur SET est_actif = 0 WHERE id = ?`, id)
	return err
}

func (r *BenevoleRepository) AjouterCompetence(benevoleID, competenceID int) error {
	var count int
	err := r.db.QueryRow(`
		SELECT COUNT(*) FROM benevole_competence WHERE benevole_id = ? AND competence_id = ?
	`, benevoleID, competenceID).Scan(&count)
	if err != nil {
		return err
	}
	if count > 0 {
		return nil
	}
	_, err = r.db.Exec(`
		INSERT INTO benevole_competence (benevole_id, competence_id) VALUES (?, ?)
	`, benevoleID, competenceID)
	return err
}

func (r *BenevoleRepository) SupprimerCompetence(benevoleID, competenceID int) error {
	_, err := r.db.Exec(`
		DELETE FROM benevole_competence WHERE benevole_id = ? AND competence_id = ?
	`, benevoleID, competenceID)
	return err
}