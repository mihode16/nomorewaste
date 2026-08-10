package repositories

import (
	"database/sql"
	"time"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/utils"
)

type TourneeRepository struct {
	db *sql.DB
}

func NouveauTourneeRepository(db *sql.DB) *TourneeRepository {
	return &TourneeRepository{db: db}
}


func (r *TourneeRepository) Creer(t *modeles.TourneeCreation) (int, error) {
	tx, err := r.db.Begin()
	if err != nil {
		return 0, err
	}
	defer tx.Rollback()

	depart, err := utils.ParseDateTime(t.DateHeureDepart)
	if err != nil {
		return 0, err
	}

	result, err := tx.Exec(`
		INSERT INTO tournee (date_heure_depart, adresse_depart, benevole_id, lieu_distribution_id)
		VALUES (?, ?, ?, ?)
	`, depart, t.AdresseDepart, t.BenevoleID, t.LieuDistributionID)
	if err != nil {
		return 0, err
	}
	id, _ := result.LastInsertId()

	for _, pid := range t.ProduitsIDs {
		if _, err = tx.Exec(`INSERT INTO tournee_produit (tournee_id, produit_id) VALUES (?, ?)`, id, pid); err != nil {
			return 0, err
		}
		if _, err = tx.Exec(`UPDATE produit SET statut = 'En tournée' WHERE id = ?`, pid); err != nil {
			return 0, err
		}
	}

	if err = tx.Commit(); err != nil {
		return 0, err
	}
	return int(id), nil
}

func (r *TourneeRepository) TrouverTous() ([]modeles.Tournee, error) {
	rows, err := r.db.Query(`
		SELECT t.id, t.date_heure_depart, t.date_heure_fin, t.adresse_depart, t.statut, t.benevole_id, t.lieu_distribution_id,
		       l.nom, l.type, l.adresse
		FROM tournee t
		LEFT JOIN lieu_distribution l ON t.lieu_distribution_id = l.id
		ORDER BY t.date_heure_depart DESC
	`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var list []modeles.Tournee
	for rows.Next() {
		var t modeles.Tournee
		var fin sql.NullTime
		var lieu modeles.LieuDistribution
		if err := rows.Scan(&t.ID, &t.DateHeureDepart, &fin, &t.AdresseDepart, &t.Statut, &t.BenevoleID, &t.LieuDistributionID, &lieu.Nom, &lieu.Type, &lieu.Adresse); err != nil {
			return nil, err
		}
		if fin.Valid {
			t.DateHeureFin = &fin.Time
		}
		lieu.ID = t.LieuDistributionID
		t.LieuDistribution = &lieu
		list = append(list, t)
	}
	return list, nil
}

func (r *TourneeRepository) TrouverParID(id int) (*modeles.Tournee, error) {
	var t modeles.Tournee
	var fin sql.NullTime
	err := r.db.QueryRow(`
		SELECT id, date_heure_depart, date_heure_fin, adresse_depart, statut, benevole_id, lieu_distribution_id
		FROM tournee WHERE id = ?
	`, id).Scan(&t.ID, &t.DateHeureDepart, &fin, &t.AdresseDepart, &t.Statut, &t.BenevoleID, &t.LieuDistributionID)
	if err != nil {
		return nil, err
	}
	if fin.Valid {
		t.DateHeureFin = &fin.Time
	}

	rows, err := r.db.Query(`
		SELECT p.id, p.code_barre, p.nom, p.categorie, p.quantite, p.date_peremption, p.date_entree_stock, p.statut, p.collecte_id
		FROM produit p JOIN tournee_produit tp ON tp.produit_id = p.id WHERE tp.tournee_id = ?
	`, id)
	if err == nil {
		defer rows.Close()
		for rows.Next() {
			var p modeles.Produit
			if err := rows.Scan(&p.ID, &p.CodeBarre, &p.Nom, &p.Categorie, &p.Quantite, &p.DatePeremption, &p.DateEntreeStock, &p.Statut, &p.CollecteID); err == nil {
				t.Produits = append(t.Produits, p)
			}
		}
	}
	return &t, nil
}

func (r *TourneeRepository) Terminer(id int) error {
	now := time.Now()
	_, err := r.db.Exec(`UPDATE tournee SET statut = 'Terminée', date_heure_fin = ? WHERE id = ?`, now, id)
	if err != nil {
		return err
	}
	rows, err := r.db.Query(`SELECT produit_id FROM tournee_produit WHERE tournee_id = ?`, id)
	if err != nil {
		return err
	}
	defer rows.Close()
	for rows.Next() {
		var pid int
		if err := rows.Scan(&pid); err == nil {
			r.db.Exec(`UPDATE produit SET statut = 'Distribué' WHERE id = ?`, pid)
		}
	}
	return nil
}

func (r *TourneeRepository) ListerLieux() ([]modeles.LieuDistribution, error) {
	rows, err := r.db.Query(`SELECT id, nom, type, adresse, personne_contact, telephone FROM lieu_distribution`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	var list []modeles.LieuDistribution
	for rows.Next() {
		var l modeles.LieuDistribution
		if err := rows.Scan(&l.ID, &l.Nom, &l.Type, &l.Adresse, &l.PersonneContact, &l.Telephone); err != nil {
			return nil, err
		}
		list = append(list, l)
	}
	return list, nil
}
