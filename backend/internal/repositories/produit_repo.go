package repositories

import (
	"database/sql"
	"time"

	"nomorewaste/internal/modeles"
)

type ProduitRepository struct {
	db *sql.DB
}

func NouveauProduitRepository(db *sql.DB) *ProduitRepository {
	return &ProduitRepository{db: db}
}

func parseDate(s string) (time.Time, error) {
	formats := []string{"2006-01-02", "2006-01-02T15:04:05", "2006-01-02 15:04:05"}
	var err error
	for _, f := range formats {
		var t time.Time
		t, err = time.Parse(f, s)
		if err == nil {
			return t, nil
		}
	}
	return time.Time{}, err
}

func (r *ProduitRepository) Creer(p *modeles.ProduitCreation) (int, error) {
	datePer, err := parseDate(p.DatePeremption)
	if err != nil {
		return 0, err
	}
	qty := p.Quantite
	if qty <= 0 {
		qty = 1
	}
	result, err := r.db.Exec(`
		INSERT INTO produit (code_barre, nom, categorie, quantite, date_peremption, collecte_id)
		VALUES (?, ?, ?, ?, ?, ?)
	`, p.CodeBarre, p.Nom, p.Categorie, qty, datePer, p.CollecteID)
	if err != nil {
		return 0, err
	}
	id, _ := result.LastInsertId()
	return int(id), nil
}

func (r *ProduitRepository) TrouverTous(codeBarre, statut string) ([]modeles.Produit, error) {
	query := `SELECT id, code_barre, nom, categorie, quantite, date_peremption, date_entree_stock, statut, collecte_id FROM produit WHERE 1=1`
	var args []interface{}
	if codeBarre != "" {
		query += ` AND code_barre LIKE ?`
		args = append(args, "%"+codeBarre+"%")
	}
	if statut != "" {
		query += ` AND statut = ?`
		args = append(args, statut)
	}
	query += ` ORDER BY date_entree_stock DESC`

	rows, err := r.db.Query(query, args...)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var list []modeles.Produit
	for rows.Next() {
		var p modeles.Produit
		if err := rows.Scan(&p.ID, &p.CodeBarre, &p.Nom, &p.Categorie, &p.Quantite, &p.DatePeremption, &p.DateEntreeStock, &p.Statut, &p.CollecteID); err != nil {
			return nil, err
		}
		list = append(list, p)
	}
	return list, nil
}

func (r *ProduitRepository) TrouverParID(id int) (*modeles.Produit, error) {
	var p modeles.Produit
	err := r.db.QueryRow(`
		SELECT id, code_barre, nom, categorie, quantite, date_peremption, date_entree_stock, statut, collecte_id
		FROM produit WHERE id = ?
	`, id).Scan(&p.ID, &p.CodeBarre, &p.Nom, &p.Categorie, &p.Quantite, &p.DatePeremption, &p.DateEntreeStock, &p.Statut, &p.CollecteID)
	if err != nil {
		return nil, err
	}
	return &p, nil
}

func (r *ProduitRepository) MettreAJour(p *modeles.Produit) error {
	_, err := r.db.Exec(`
		UPDATE produit SET code_barre = ?, nom = ?, categorie = ?, quantite = ?, date_peremption = ?, statut = ?, collecte_id = ?
		WHERE id = ?
	`, p.CodeBarre, p.Nom, p.Categorie, p.Quantite, p.DatePeremption, p.Statut, p.CollecteID, p.ID)
	return err
}

func (r *ProduitRepository) Supprimer(id int) error {
	_, err := r.db.Exec(`DELETE FROM produit WHERE id = ?`, id)
	return err
}

func (r *ProduitRepository) MarquerDistribue(ids []int) error {
	for _, id := range ids {
		if _, err := r.db.Exec(`UPDATE produit SET statut = 'Distribué' WHERE id = ?`, id); err != nil {
			return err
		}
	}
	return nil
}

func (r *ProduitRepository) TrouverEnStock() ([]modeles.Produit, error) {
	return r.TrouverTous("", "Stocké")
}
