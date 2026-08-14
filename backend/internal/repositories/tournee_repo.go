package repositories

import (
	"database/sql"
	"errors"
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
		INSERT INTO tournee (date_heure_depart, adresse_depart, benevole_id, lieu_distribution_id, statut)
		VALUES (?, ?, ?, ?, 'Prévue')
	`, depart, t.AdresseDepart, t.BenevoleID, t.LieuDistributionID)
	if err != nil {
		return 0, err
	}
	id, _ := result.LastInsertId()

	// Produits sélectionnés -> statut "À distribuer"
	for _, pid := range t.ProduitsIDs {
		if _, err = tx.Exec(`INSERT INTO tournee_produit (tournee_id, produit_id) VALUES (?, ?)`, id, pid); err != nil {
			return 0, err
		}
		if _, err = tx.Exec(`UPDATE produit SET statut = 'À distribuer' WHERE id = ?`, pid); err != nil {
			return 0, err
		}
	}

	// Bénévoles supplémentaires
	for _, bid := range t.BenevolesIDs {
		if _, err = tx.Exec(`INSERT INTO tournee_benevole (tournee_id, benevole_id) VALUES (?, ?)`, id, bid); err != nil {
			return 0, err
		}
	}

	if err = tx.Commit(); err != nil {
		return 0, err
	}
	return int(id), nil
}

func (r *TourneeRepository) TrouverTous(statut, destination, date string) ([]modeles.Tournee, error) {
	query := `
		SELECT t.id, t.date_heure_depart, t.date_heure_fin, t.adresse_depart, t.statut, t.benevole_id, t.lieu_distribution_id,
		       l.nom, l.type, l.adresse
		FROM tournee t
		LEFT JOIN lieu_distribution l ON t.lieu_distribution_id = l.id
		WHERE 1=1
	`
	var args []interface{}

	if statut != "" {
		query += " AND t.statut = ?"
		args = append(args, statut)
	}
	if destination != "" {
		query += " AND l.nom LIKE ?"
		args = append(args, "%"+destination+"%")
	}
	if date != "" {
		query += " AND DATE(t.date_heure_depart) = ?"
		args = append(args, date)
	}

	query += " ORDER BY t.date_heure_depart DESC"

	rows, err := r.db.Query(query, args...)
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

	// Bénévoles supplémentaires de chaque tournée
	rowsB, err := r.db.Query(`
		SELECT tb.tournee_id, b.id, u.nom, u.prenom
		FROM tournee_benevole tb
		JOIN benevole b ON tb.benevole_id = b.id
		JOIN utilisateur u ON b.id = u.id
	`)
	if err == nil {
		defer rowsB.Close()
		benevolesParTournee := make(map[int][]modeles.Benevole)
		for rowsB.Next() {
			var tourneeID int
			var b modeles.Benevole
			if err := rowsB.Scan(&tourneeID, &b.ID, &b.Nom, &b.Prenom); err == nil {
				benevolesParTournee[tourneeID] = append(benevolesParTournee[tourneeID], b)
			}
		}
		for i := range list {
			list[i].Benevoles = benevolesParTournee[list[i].ID]
		}
	}

	return list, nil
}

func (r *TourneeRepository) TrouverParID(id int) (*modeles.Tournee, error) {
    var t modeles.Tournee
    var fin sql.NullTime
    var lieu modeles.LieuDistribution
    var chauffeur modeles.Benevole
    var chauffeurNom, chauffeurPrenom sql.NullString
    var dateConfChauffeur sql.NullTime

    err := r.db.QueryRow(`
        SELECT t.id, t.date_heure_depart, t.date_heure_fin, t.adresse_depart, t.statut,
               t.benevole_id, t.lieu_distribution_id, t.chauffeur_confirme, t.date_confirmation_chauffeur,
               l.nom, l.type, l.adresse,
               u.nom, u.prenom
        FROM tournee t
        LEFT JOIN lieu_distribution l ON t.lieu_distribution_id = l.id
        LEFT JOIN utilisateur u ON t.benevole_id = u.id
        WHERE t.id = ?
    `, id).Scan(
        &t.ID, &t.DateHeureDepart, &fin, &t.AdresseDepart, &t.Statut,
        &t.BenevoleID, &t.LieuDistributionID, &t.ChauffeurConfirme, &dateConfChauffeur,
        &lieu.Nom, &lieu.Type, &lieu.Adresse,
        &chauffeurNom, &chauffeurPrenom,
    )
    if err != nil {
        return nil, err
    }
    if fin.Valid {
        t.DateHeureFin = &fin.Time
    }
    if dateConfChauffeur.Valid {
        t.DateConfirmationChauffeur = &dateConfChauffeur.Time
    }
    lieu.ID = t.LieuDistributionID
    t.LieuDistribution = &lieu

    // Remplir le bénévole chauffeur
    if t.BenevoleID > 0 {
        chauffeur.ID = t.BenevoleID
        chauffeur.Nom = chauffeurNom.String
        chauffeur.Prenom = chauffeurPrenom.String
        t.Benevole = &chauffeur
    }

    // Produits
    rows, err := r.db.Query(`
        SELECT p.id, p.code_barre, p.nom, p.categorie, p.quantite, p.date_peremption, p.date_entree_stock, p.statut, p.collecte_id
        FROM produit p 
        JOIN tournee_produit tp ON tp.produit_id = p.id 
        WHERE tp.tournee_id = ?
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

    // Bénévoles supplémentaires
    rowsB, err := r.db.Query(`
        SELECT tb.id, b.id, u.nom, u.prenom, tb.confirme, tb.date_confirmation
        FROM tournee_benevole tb
        JOIN benevole b ON tb.benevole_id = b.id
        JOIN utilisateur u ON b.id = u.id
        WHERE tb.tournee_id = ?
    `, id)
    if err == nil {
        defer rowsB.Close()
        for rowsB.Next() {
            var b modeles.Benevole
            var tb modeles.TourneeBenevole
            var nom, prenom string
            var dateConf sql.NullTime
            if err := rowsB.Scan(&tb.ID, &b.ID, &nom, &prenom, &tb.Confirme, &dateConf); err == nil {
                b.Nom = nom
                b.Prenom = prenom
                t.Benevoles = append(t.Benevoles, b)

                tb.TourneeID = t.ID
                tb.BenevoleID = b.ID
                tb.BenevoleNom = nom
                tb.BenevolePrenom = prenom
                if dateConf.Valid {
                    tb.DateConfirmation = &dateConf.Time
                }
                t.BenevolesConfirmation = append(t.BenevolesConfirmation, tb)
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

// ConfirmerBenevole marque un bénévole (chauffeur ou supplémentaire) comme ayant terminé
// la tournée. Une fois que tous les bénévoles associés ont confirmé, la tournée passe
// automatiquement au statut "Terminée".
func (r *TourneeRepository) ConfirmerBenevole(tourneeID, benevoleID int) error {
	tx, err := r.db.Begin()
	if err != nil {
		return err
	}
	defer tx.Rollback()

	// Verrouille la tournée pour éviter une double bascule concurrente vers "Terminée"
	var chauffeurID int
	if err := tx.QueryRow(`SELECT benevole_id FROM tournee WHERE id = ? FOR UPDATE`, tourneeID).Scan(&chauffeurID); err != nil {
		return err
	}

	now := time.Now()
	if benevoleID == chauffeurID {
		if _, err := tx.Exec(`UPDATE tournee SET chauffeur_confirme = 1, date_confirmation_chauffeur = ? WHERE id = ?`, now, tourneeID); err != nil {
			return err
		}
	} else {
		var count int
		if err := tx.QueryRow(`SELECT COUNT(*) FROM tournee_benevole WHERE tournee_id = ? AND benevole_id = ?`, tourneeID, benevoleID).Scan(&count); err != nil {
			return err
		}
		if count == 0 {
			return errors.New("bénévole non associé à cette tournée")
		}
		if _, err := tx.Exec(`UPDATE tournee_benevole SET confirme = 1, date_confirmation = ? WHERE tournee_id = ? AND benevole_id = ?`, now, tourneeID, benevoleID); err != nil {
			return err
		}
	}

	var chauffeurConfirme bool
	if err := tx.QueryRow(`SELECT chauffeur_confirme FROM tournee WHERE id = ?`, tourneeID).Scan(&chauffeurConfirme); err != nil {
		return err
	}
	var totalSupp, confirmesSupp int
	if err := tx.QueryRow(`SELECT COUNT(*) FROM tournee_benevole WHERE tournee_id = ?`, tourneeID).Scan(&totalSupp); err != nil {
		return err
	}
	if err := tx.QueryRow(`SELECT COUNT(*) FROM tournee_benevole WHERE tournee_id = ? AND confirme = 1`, tourneeID).Scan(&confirmesSupp); err != nil {
		return err
	}

	if err := tx.Commit(); err != nil {
		return err
	}

	if chauffeurConfirme && confirmesSupp >= totalSupp {
		return r.Terminer(tourneeID)
	}
	return nil
}

func (r *TourneeRepository) ListerLieux() ([]modeles.LieuDistribution, error) {
	rows, err := r.db.Query(`SELECT id, nom, type, adresse, COALESCE(personne_contact, ''), COALESCE(telephone, '') FROM lieu_distribution`)
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

func (r *TourneeRepository) AjouterLieu(l *modeles.LieuDistribution) (int, error) {
	result, err := r.db.Exec(`
		INSERT INTO lieu_distribution (nom, adresse, type) VALUES (?, ?, ?)
	`, l.Nom, l.Adresse, l.Type)
	if err != nil {
		return 0, err
	}
	id, _ := result.LastInsertId()
	return int(id), nil
}

// MettreAJour met à jour une tournée
func (r *TourneeRepository) MettreAJour(t *modeles.TourneeUpdate) error {
    tx, err := r.db.Begin()
    if err != nil {
        return err
    }
    defer tx.Rollback()

    // Mettre à jour les infos de base
    depart, err := utils.ParseDateTime(t.DateHeureDepart)
    if err != nil {
        return err
    }

    _, err = tx.Exec(`
        UPDATE tournee 
        SET date_heure_depart = ?, adresse_depart = ?, benevole_id = ?, lieu_distribution_id = ?
        WHERE id = ?
    `, depart, t.AdresseDepart, t.BenevoleID, t.LieuDistributionID, t.ID)
    if err != nil {
        return err
    }

    // Gérer les produits : supprimer les anciens, ajouter les nouveaux
    // 1. Récupérer les anciens produits pour remettre leur statut à "Stocké"
    rows, err := tx.Query(`SELECT produit_id FROM tournee_produit WHERE tournee_id = ?`, t.ID)
    if err != nil {
        return err
    }
    var oldProduits []int
    for rows.Next() {
        var pid int
        if err := rows.Scan(&pid); err == nil {
            oldProduits = append(oldProduits, pid)
        }
    }
    rows.Close()

    // 2. Supprimer les associations existantes
    _, err = tx.Exec(`DELETE FROM tournee_produit WHERE tournee_id = ?`, t.ID)
    if err != nil {
        return err
    }

    // 3. Remettre les anciens produits en "Stocké"
    for _, pid := range oldProduits {
        _, err = tx.Exec(`UPDATE produit SET statut = 'Stocké' WHERE id = ?`, pid)
        if err != nil {
            return err
        }
    }

    // 4. Ajouter les nouveaux produits et passer leur statut à "À distribuer"
    for _, pid := range t.ProduitsIDs {
        _, err = tx.Exec(`INSERT INTO tournee_produit (tournee_id, produit_id) VALUES (?, ?)`, t.ID, pid)
        if err != nil {
            return err
        }
        _, err = tx.Exec(`UPDATE produit SET statut = 'À distribuer' WHERE id = ?`, pid)
        if err != nil {
            return err
        }
    }

    // Gérer les bénévoles supplémentaires
    // 1. Supprimer les anciens
    _, err = tx.Exec(`DELETE FROM tournee_benevole WHERE tournee_id = ?`, t.ID)
    if err != nil {
        return err
    }

    // 2. Ajouter les nouveaux
    for _, bid := range t.BenevolesIDs {
        _, err = tx.Exec(`INSERT INTO tournee_benevole (tournee_id, benevole_id) VALUES (?, ?)`, t.ID, bid)
        if err != nil {
            return err
        }
    }

    return tx.Commit()
}

// Supprimer une tournée (soft delete ou physique ? On fait physique pour l'instant)
func (r *TourneeRepository) Supprimer(id int) error {
    // Récupérer les produits pour remettre leur statut en "Stocké"
    rows, err := r.db.Query(`SELECT produit_id FROM tournee_produit WHERE tournee_id = ?`, id)
    if err != nil {
        return err
    }
    defer rows.Close()
    for rows.Next() {
        var pid int
        if err := rows.Scan(&pid); err == nil {
            r.db.Exec(`UPDATE produit SET statut = 'Stocké' WHERE id = ?`, pid)
        }
    }

    _, err = r.db.Exec(`DELETE FROM tournee WHERE id = ?`, id)
    return err
}

// Compter les bénévoles pour une tournée
func (r *TourneeRepository) CompterBenevoles(tourneeID int) (int, error) {
    var count int
    // 1 pour le chauffeur + les supplémentaires
    err := r.db.QueryRow(`
        SELECT 1 + COUNT(*) FROM tournee_benevole WHERE tournee_id = ?
    `, tourneeID).Scan(&count)
    if err != nil {
        return 1, nil // au moins le chauffeur
    }
    return count, nil
}