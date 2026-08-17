package repositories

import (
	"database/sql"
	"time"
)

// RappelRepository journalise les rappels de renouvellement d'adhésion déjà envoyés par
// email, pour ne pas ré-envoyer le même rappel à chaque exécution de la tâche quotidienne.
type RappelRepository struct {
	db *sql.DB
}

func NouveauRappelRepository(db *sql.DB) *RappelRepository {
	return &RappelRepository{db: db}
}

// DejaEnvoyeCommercant indique si un rappel a déjà été envoyé à ce commerçant pour ce cycle
// d'adhésion précis (identifié par sa date de fin actuelle).
func (r *RappelRepository) DejaEnvoyeCommercant(commercantID int, dateFinAdhesion time.Time) (bool, error) {
	var n int
	err := r.db.QueryRow(`
		SELECT COUNT(*) FROM rappel_renouvellement
		WHERE commercant_id = ? AND date_fin_adhesion = ? AND est_envoye = 1
	`, commercantID, dateFinAdhesion.Format("2006-01-02")).Scan(&n)
	return n > 0, err
}

func (r *RappelRepository) DejaEnvoyeAdherent(adherentID int, dateFinAdhesion time.Time) (bool, error) {
	var n int
	err := r.db.QueryRow(`
		SELECT COUNT(*) FROM rappel_renouvellement
		WHERE adherent_id = ? AND date_fin_adhesion = ? AND est_envoye = 1
	`, adherentID, dateFinAdhesion.Format("2006-01-02")).Scan(&n)
	return n > 0, err
}

func (r *RappelRepository) MarquerEnvoyeCommercant(commercantID int, dateFinAdhesion time.Time) error {
	_, err := r.db.Exec(`
		INSERT INTO rappel_renouvellement (commercant_id, date_fin_adhesion, date_rappel, est_envoye)
		VALUES (?, ?, CURDATE(), 1)
	`, commercantID, dateFinAdhesion.Format("2006-01-02"))
	return err
}

func (r *RappelRepository) MarquerEnvoyeAdherent(adherentID int, dateFinAdhesion time.Time) error {
	_, err := r.db.Exec(`
		INSERT INTO rappel_renouvellement (adherent_id, date_fin_adhesion, date_rappel, est_envoye)
		VALUES (?, ?, CURDATE(), 1)
	`, adherentID, dateFinAdhesion.Format("2006-01-02"))
	return err
}
