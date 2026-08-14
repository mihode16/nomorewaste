package repositories

import (
	"database/sql"
	"fmt"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/utils"
)

type ProduitRepository struct {
	db *sql.DB
}

func NouveauProduitRepository(db *sql.DB) *ProduitRepository {
	return &ProduitRepository{db: db}
}

func (r *ProduitRepository) Creer(p *modeles.ProduitCreation) (int, error) {
	_, err := utils.ParseDate(p.DatePeremption)
	if err != nil {
		return 0, err
	}
	qty := p.Quantite
	if qty <= 0 {
		qty = 1
	}

	// Tant que la collecte associée n'est pas validée ET terminée, le produit n'est pas
	// encore physiquement en stock : il reste "À venir". Un produit sans collecte (ajout
	// manuel direct) est considéré déjà en stock.
	statut := "Stocké"
	if p.CollecteID > 0 {
		var validee bool
		var statutCollecte string
		err := r.db.QueryRow(`SELECT validee, statut FROM collecte WHERE id = ?`, p.CollecteID).Scan(&validee, &statutCollecte)
		if err != nil || !validee || statutCollecte != "Terminée" {
			statut = "À venir"
		}
	}

	result, err := r.db.Exec(`
		INSERT INTO produit (code_barre, nom, categorie, quantite, date_peremption, collecte_id, statut)
		VALUES (?, ?, ?, ?, ?, ?, ?)
	`, p.CodeBarre, p.Nom, p.Categorie, qty, p.DatePeremption, p.CollecteID, statut)
	if err != nil {
		return 0, err
	}
	id, _ := result.LastInsertId()
	return int(id), nil
}

// scanProduit lit une ligne produit en gérant collecte_id qui peut être NULL en base
// (ex : après suppression d'une collecte via ON DELETE SET NULL).
func scanProduit(scanner interface {
	Scan(dest ...interface{}) error
}, p *modeles.Produit) error {
	var collecteID sql.NullInt64
	if err := scanner.Scan(&p.ID, &p.CodeBarre, &p.Nom, &p.Categorie, &p.Quantite, &p.DatePeremption, &p.DateEntreeStock, &p.Statut, &collecteID); err != nil {
		return err
	}
	if collecteID.Valid {
		p.CollecteID = int(collecteID.Int64)
	} else {
		p.CollecteID = 0
	}
	return nil
}

func (r *ProduitRepository) TrouverParCodeBarre(code string) (*modeles.Produit, error) {
	var p modeles.Produit
	row := r.db.QueryRow(`
		SELECT id, code_barre, nom, categorie, quantite, date_peremption, date_entree_stock, statut, collecte_id
		FROM produit WHERE code_barre = ?
	`, code)
	if err := scanProduit(row, &p); err != nil {
		return nil, err
	}
	return &p, nil
}

func (r *ProduitRepository) TrouverTous(search, categorie, statut, tri string) ([]modeles.Produit, error) {
	query := `SELECT id, code_barre, nom, categorie, quantite, date_peremption, date_entree_stock, statut, collecte_id FROM produit WHERE 1=1`
	var args []interface{}

	if search != "" {
		query += ` AND (code_barre LIKE ? OR nom LIKE ?)`
		like := "%" + search + "%"
		args = append(args, like, like)
	}
	if categorie != "" {
		query += ` AND categorie = ?`
		args = append(args, categorie)
	}
	if statut != "" {
		query += ` AND statut = ?`
		args = append(args, statut)
	}

	// Tri par date de péremption
	if tri == "proche" {
		query += ` ORDER BY date_peremption ASC`
	} else if tri == "lointain" {
		query += ` ORDER BY date_peremption DESC`
	} else {
		// Tri par défaut : date d'entrée en stock décroissante
		query += ` ORDER BY date_entree_stock DESC`
	}

	rows, err := r.db.Query(query, args...)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var list []modeles.Produit
	for rows.Next() {
		var p modeles.Produit
		if err := scanProduit(rows, &p); err != nil {
			return nil, err
		}
		list = append(list, p)
	}
	return list, nil
}

func (r *ProduitRepository) TrouverParID(id int) (*modeles.Produit, error) {
	var p modeles.Produit
	row := r.db.QueryRow(`
		SELECT id, code_barre, nom, categorie, quantite, date_peremption, date_entree_stock, statut, collecte_id
		FROM produit WHERE id = ?
	`, id)
	if err := scanProduit(row, &p); err != nil {
		return nil, err
	}
	return &p, nil
}

func (r *ProduitRepository) MettreAJour(p *modeles.Produit) error {
	dateStr := p.DatePeremption.Format("2006-01-02")
	result, err := r.db.Exec(`
		UPDATE produit SET code_barre = ?, nom = ?, categorie = ?, quantite = ?, date_peremption = ?, statut = ?, collecte_id = ?
		WHERE id = ?
	`, p.CodeBarre, p.Nom, p.Categorie, p.Quantite, dateStr, p.Statut, p.CollecteID, p.ID)
	if err != nil {
		return err
	}
	rows, err := result.RowsAffected()
	if err != nil {
		return err
	}
	if rows == 0 {
		return fmt.Errorf("aucun produit trouvé avec l'id %d", p.ID)
	}
	return nil
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
	return r.TrouverTous("", "", "Stocké", "")
}

func (r *ProduitRepository) TrouverParCollecteID(collecteID int) ([]modeles.Produit, error) {
	rows, err := r.db.Query(`
		SELECT id, code_barre, nom, categorie, quantite, date_peremption, date_entree_stock, statut, collecte_id
		FROM produit WHERE collecte_id = ?
		ORDER BY date_entree_stock DESC
	`, collecteID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var produits []modeles.Produit
	for rows.Next() {
		var p modeles.Produit
		if err := scanProduit(rows, &p); err != nil {
			return nil, err
		}
		produits = append(produits, p)
	}
	return produits, nil
}

func (r *ProduitRepository) ListerCategories() ([]string, error) {
    rows, err := r.db.Query(`SELECT DISTINCT categorie FROM produit WHERE categorie IS NOT NULL AND categorie != '' ORDER BY categorie`)
    if err != nil {
        return nil, err
    }
    defer rows.Close()
    var categories []string
    for rows.Next() {
        var c string
        if err := rows.Scan(&c); err != nil {
            return nil, err
        }
        categories = append(categories, c)
    }
    return categories, nil
}

func (r *ProduitRepository) TrouverTousFiltres(recherche, categorie, statut, tri string) ([]modeles.Produit, error) {
    query := `SELECT id, code_barre, nom, categorie, quantite, date_peremption, date_entree_stock, statut, collecte_id FROM produit WHERE 1=1`
    var args []interface{}
    if recherche != "" {
        query += ` AND (code_barre LIKE ? OR nom LIKE ?)`
        like := "%" + recherche + "%"
        args = append(args, like, like)
    }
    if categorie != "" {
        query += ` AND categorie = ?`
        args = append(args, categorie)
    }
    if statut != "" {
        query += ` AND statut = ?`
        args = append(args, statut)
    }
    switch tri {
    case "date_peremption_asc":
        query += ` ORDER BY date_peremption ASC`
    case "date_peremption_desc":
        query += ` ORDER BY date_peremption DESC`
    case "nom_asc":
        query += ` ORDER BY nom ASC`
    default:
        query += ` ORDER BY date_peremption ASC`
    }
    rows, err := r.db.Query(query, args...)
    if err != nil {
        return nil, err
    }
    defer rows.Close()
    var list []modeles.Produit
    for rows.Next() {
        var p modeles.Produit
        if err := scanProduit(rows, &p); err != nil {
            return nil, err
        }
        list = append(list, p)
    }
    return list, nil
}