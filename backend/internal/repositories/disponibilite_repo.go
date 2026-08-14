package repositories

import (
	"database/sql"

	"nomorewaste/internal/modeles"
)

type DisponibiliteRepository struct {
	db *sql.DB
}

func NouveauDisponibiliteRepository(db *sql.DB) *DisponibiliteRepository {
	return &DisponibiliteRepository{db: db}
}

func scanDisponibilite(scanner interface{ Scan(dest ...interface{}) error }, d *modeles.Disponibilite, avecBenevole bool) error {
	var dateDispo sql.NullTime
	var err error
	if avecBenevole {
		err = scanner.Scan(&d.ID, &d.BenevoleID, &d.JourSemaine, &dateDispo, &d.HeureDebut, &d.HeureFin, &d.BenevoleNom, &d.BenevolePrenom)
	} else {
		err = scanner.Scan(&d.ID, &d.BenevoleID, &d.JourSemaine, &dateDispo, &d.HeureDebut, &d.HeureFin)
	}
	if err != nil {
		return err
	}
	if dateDispo.Valid {
		d.Date = dateDispo.Time.Format("2006-01-02")
	}
	return nil
}

func (r *DisponibiliteRepository) ListerParBenevole(benevoleID int) ([]modeles.Disponibilite, error) {
	rows, err := r.db.Query(`
		SELECT id, benevole_id, jour_semaine, date_dispo, heure_debut, heure_fin
		FROM benevole_disponibilite
		WHERE benevole_id = ?
		ORDER BY date_dispo, heure_debut`, benevoleID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var list []modeles.Disponibilite
	for rows.Next() {
		var d modeles.Disponibilite
		if err := scanDisponibilite(rows, &d, false); err != nil {
			return nil, err
		}
		list = append(list, d)
	}
	return list, nil
}

// ListerToutes retourne les disponibilités de tous les bénévoles actifs, nom/prénom inclus
// (utilisé pour le grand calendrier admin).
func (r *DisponibiliteRepository) ListerToutes() ([]modeles.Disponibilite, error) {
	rows, err := r.db.Query(`
		SELECT bd.id, bd.benevole_id, bd.jour_semaine, bd.date_dispo, bd.heure_debut, bd.heure_fin, u.nom, u.prenom
		FROM benevole_disponibilite bd
		JOIN utilisateur u ON bd.benevole_id = u.id
		WHERE u.est_actif = 1
		ORDER BY bd.date_dispo, bd.heure_debut`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var list []modeles.Disponibilite
	for rows.Next() {
		var d modeles.Disponibilite
		if err := scanDisponibilite(rows, &d, true); err != nil {
			return nil, err
		}
		list = append(list, d)
	}
	return list, nil
}

func (r *DisponibiliteRepository) Creer(d *modeles.Disponibilite) (int, error) {
	var dateDispo interface{}
	if d.Date != "" {
		dateDispo = d.Date
	}
	result, err := r.db.Exec(`
		INSERT INTO benevole_disponibilite (benevole_id, jour_semaine, date_dispo, heure_debut, heure_fin)
		VALUES (?, ?, ?, ?, ?)
	`, d.BenevoleID, d.JourSemaine, dateDispo, d.HeureDebut, d.HeureFin)
	if err != nil {
		return 0, err
	}
	id, _ := result.LastInsertId()
	return int(id), nil
}

func (r *DisponibiliteRepository) AppartientA(id, benevoleID int) (bool, error) {
	var count int
	err := r.db.QueryRow(`SELECT COUNT(*) FROM benevole_disponibilite WHERE id = ? AND benevole_id = ?`, id, benevoleID).Scan(&count)
	return count > 0, err
}

func (r *DisponibiliteRepository) Supprimer(id int) error {
	_, err := r.db.Exec(`DELETE FROM benevole_disponibilite WHERE id = ?`, id)
	return err
}
