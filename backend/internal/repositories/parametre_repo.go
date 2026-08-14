package repositories

import "database/sql"

type ParametreRepository struct {
	db *sql.DB
}

func NouveauParametreRepository(db *sql.DB) *ParametreRepository {
	return &ParametreRepository{db: db}
}

func (r *ParametreRepository) Obtenir(cle string) (string, error) {
	var valeur string
	err := r.db.QueryRow(`SELECT valeur FROM parametre WHERE cle = ?`, cle).Scan(&valeur)
	return valeur, err
}

func (r *ParametreRepository) Definir(cle, valeur string) error {
	_, err := r.db.Exec(`
		INSERT INTO parametre (cle, valeur) VALUES (?, ?)
		ON DUPLICATE KEY UPDATE valeur = ?
	`, cle, valeur, valeur)
	return err
}
