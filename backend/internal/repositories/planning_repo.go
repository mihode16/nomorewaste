package repositories

import (
	"database/sql"

	"nomorewaste/internal/modeles"
)

type PlanningRepository struct {
	db *sql.DB
}

func NouveauPlanningRepository(db *sql.DB) *PlanningRepository {
	return &PlanningRepository{db: db}
}

func (r *PlanningRepository) Creer(benevoleID int, dateDebut, dateFin string) (int, error) {
	result, err := r.db.Exec(`
		INSERT INTO planning_benevole (benevole_id, date_debut, date_fin)
		VALUES (?, ?, ?)
	`, benevoleID, dateDebut, dateFin)
	if err != nil {
		return 0, err
	}
	id, _ := result.LastInsertId()
	return int(id), nil
}

func (r *PlanningRepository) ListerParBenevole(benevoleID int) ([]modeles.PlanningBenevole, error) {
	rows, err := r.db.Query(`
		SELECT id, benevole_id, date_debut, date_fin, date_generation
		FROM planning_benevole
		WHERE benevole_id = ?
		ORDER BY date_generation DESC
	`, benevoleID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var list []modeles.PlanningBenevole
	for rows.Next() {
		var p modeles.PlanningBenevole
		if err := rows.Scan(&p.ID, &p.BenevoleID, &p.DateDebut, &p.DateFin, &p.DateGeneration); err != nil {
			return nil, err
		}
		list = append(list, p)
	}
	return list, nil
}

func (r *PlanningRepository) TrouverParID(id int) (*modeles.PlanningBenevole, error) {
	var p modeles.PlanningBenevole
	err := r.db.QueryRow(`
		SELECT id, benevole_id, date_debut, date_fin, date_generation
		FROM planning_benevole WHERE id = ?
	`, id).Scan(&p.ID, &p.BenevoleID, &p.DateDebut, &p.DateFin, &p.DateGeneration)
	if err != nil {
		return nil, err
	}
	return &p, nil
}
