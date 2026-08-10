package repositories

import (
	"database/sql"
	"log"
	"time"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/utils"
)

type CollecteRepository struct {
	db *sql.DB
}

func NouveauCollecteRepository(db *sql.DB) *CollecteRepository {
	return &CollecteRepository{db: db}
}

func (r *CollecteRepository) Creer(collecte *modeles.CollecteCreation) (int, error) {
	dateHeure, err := utils.ParseDateTime(collecte.DateHeureCollecte)
	if err != nil {
		return 0, err
	}

	result, err := r.db.Exec(`
		INSERT INTO collecte (date_heure_collecte, adresse_collecte, commentaire, commercant_id, validee, statut, nb_benevoles)
		VALUES (?, ?, ?, ?, ?, ?, ?)
	`, dateHeure, collecte.AdresseCollecte, collecte.Commentaire, collecte.CommercantID, 0, "", 0)
	if err != nil {
		return 0, err
	}

	id, err := result.LastInsertId()
	if err != nil {
		return 0, err
	}
	return int(id), nil
}

func (r *CollecteRepository) TrouverTous(commercant, statut, dateDebut, dateFin string) ([]modeles.Collecte, error) {
	query := `
		SELECT c.id, c.date_heure_collecte, c.adresse_collecte, c.statut, c.commentaire, c.commercant_id, c.validee, c.nb_benevoles,
		       u.id, u.email, u.nom, u.prenom, u.telephone, u.adresse, u.date_inscription, u.est_actif,
		       cm.siret, cm.raison_sociale, cm.type_commerce, cm.date_debut_adhesion, cm.date_fin_adhesion, cm.est_renouvele_automatiquement
		FROM collecte c
		LEFT JOIN commercant cm ON c.commercant_id = cm.id
		LEFT JOIN utilisateur u ON cm.id = u.id
		WHERE 1=1
	`
	var args []interface{}

	if commercant != "" {
		query += " AND (u.nom LIKE ? OR u.prenom LIKE ? OR cm.raison_sociale LIKE ?)"
		like := "%" + commercant + "%"
		args = append(args, like, like, like)
	}
	if statut != "" {
		query += " AND c.statut = ?"
		args = append(args, statut)
	}
	if dateDebut != "" {
		query += " AND DATE(c.date_heure_collecte) >= ?"
		args = append(args, dateDebut)
	}
	if dateFin != "" {
		query += " AND DATE(c.date_heure_collecte) <= ?"
		args = append(args, dateFin)
	}

	query += " ORDER BY c.date_heure_collecte DESC"

	log.Printf("🔍 Requête collectes: %s, args: %v", query, args)

	rows, err := r.db.Query(query, args...)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var collectes []modeles.Collecte
	for rows.Next() {
		var c modeles.Collecte
		var cm modeles.Commercant
		var u modeles.Utilisateur
		var commentaire, email, nom, prenom, telephone, adresse, siret, raisonSociale, typeCommerce sql.NullString

		err := rows.Scan(
			&c.ID, &c.DateHeureCollecte, &c.AdresseCollecte, &c.Statut, &commentaire, &c.CommercantID, &c.Validee, &c.NbBenevoles,
			&u.ID, &email, &nom, &prenom, &telephone, &adresse, &u.DateInscription, &u.EstActif,
			&siret, &raisonSociale, &typeCommerce, &cm.DateDebutAdhesion, &cm.DateFinAdhesion, &cm.EstRenouveleAutomatiquement,
		)
		if err != nil {
			return nil, err
		}
		c.Commentaire = commentaire.String
		u.Email = email.String
		u.Nom = nom.String
		u.Prenom = prenom.String
		u.Telephone = telephone.String
		u.Adresse = adresse.String
		cm.Siret = siret.String
		cm.RaisonSociale = raisonSociale.String
		cm.TypeCommerce = typeCommerce.String
		cm.Utilisateur = u
		c.Commercant = &cm

		benevoles, _ := r.TrouverBenevolesPourCollecte(c.ID)
		c.Benevoles = benevoles

		collectes = append(collectes, c)
	}
	return collectes, nil
}

func (r *CollecteRepository) TrouverParID(id int) (*modeles.Collecte, error) {
	var c modeles.Collecte
	var cm modeles.Commercant
	var u modeles.Utilisateur
	var commentaire, email, nom, prenom, telephone, adresse, siret, raisonSociale, typeCommerce sql.NullString

	err := r.db.QueryRow(`
		SELECT c.id, c.date_heure_collecte, c.adresse_collecte, c.statut, c.commentaire, c.commercant_id, c.validee, c.nb_benevoles,
		       u.id, u.email, u.nom, u.prenom, u.telephone, u.adresse, u.date_inscription, u.est_actif,
		       cm.siret, cm.raison_sociale, cm.type_commerce, cm.date_debut_adhesion, cm.date_fin_adhesion, cm.est_renouvele_automatiquement
		FROM collecte c
		LEFT JOIN commercant cm ON c.commercant_id = cm.id
		LEFT JOIN utilisateur u ON cm.id = u.id
		WHERE c.id = ?
	`, id).Scan(
		&c.ID, &c.DateHeureCollecte, &c.AdresseCollecte, &c.Statut, &commentaire, &c.CommercantID, &c.Validee, &c.NbBenevoles,
		&u.ID, &email, &nom, &prenom, &telephone, &adresse, &u.DateInscription, &u.EstActif,
		&siret, &raisonSociale, &typeCommerce, &cm.DateDebutAdhesion, &cm.DateFinAdhesion, &cm.EstRenouveleAutomatiquement,
	)
	if err != nil {
		return nil, err
	}
	c.Commentaire = commentaire.String
	u.Email = email.String
	u.Nom = nom.String
	u.Prenom = prenom.String
	u.Telephone = telephone.String
	u.Adresse = adresse.String
	cm.Siret = siret.String
	cm.RaisonSociale = raisonSociale.String
	cm.TypeCommerce = typeCommerce.String
	cm.Utilisateur = u
	c.Commercant = &cm

	benevoles, _ := r.TrouverBenevolesPourCollecte(c.ID)
	c.Benevoles = benevoles

	return &c, nil
}

func (r *CollecteRepository) TrouverBenevolesPourCollecte(collecteID int) ([]modeles.CollecteBenevole, error) {
	rows, err := r.db.Query(`
        SELECT cb.id, cb.collecte_id, cb.benevole_id, cb.confirme, cb.date_confirmation,
               u.nom, u.prenom
        FROM collecte_benevole cb
        JOIN utilisateur u ON cb.benevole_id = u.id
        WHERE cb.collecte_id = ?
    `, collecteID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var list []modeles.CollecteBenevole
	for rows.Next() {
		var cb modeles.CollecteBenevole
		var dateConf sql.NullTime
		var nom, prenom sql.NullString
		if err := rows.Scan(&cb.ID, &cb.CollecteID, &cb.BenevoleID, &cb.Confirme, &dateConf, &nom, &prenom); err != nil {
			return nil, err
		}
		if dateConf.Valid {
			cb.DateConfirmation = &dateConf.Time
		}
		// BenevoleNom et BenevolePrenom doivent être ajoutés dans le modèle
		// Si vous ne les avez pas, vous pouvez les ignorer.
		list = append(list, cb)
	}
	return list, nil
}

func (r *CollecteRepository) ValiderCollecte(id int) error {
	_, err := r.db.Exec(`UPDATE collecte SET validee = 1, statut = 'Planifiée' WHERE id = ?`, id)
	return err
}

func (r *CollecteRepository) AjouterBenevole(collecteID, benevoleID int) error {
	_, err := r.db.Exec(`
		INSERT INTO collecte_benevole (collecte_id, benevole_id, confirme) VALUES (?, ?, 0)
	`, collecteID, benevoleID)
	if err != nil {
		return err
	}
	_, err = r.db.Exec(`UPDATE collecte SET nb_benevoles = nb_benevoles + 1 WHERE id = ?`, collecteID)
	return err
}

func (r *CollecteRepository) ConfirmerBenevole(collecteID, benevoleID int) error {
	var count int
	err := r.db.QueryRow(`
		SELECT COUNT(*) FROM collecte_benevole WHERE collecte_id = ? AND benevole_id = ?
	`, collecteID, benevoleID).Scan(&count)
	if err != nil || count == 0 {
		return err
	}

	now := time.Now()
	_, err = r.db.Exec(`
		UPDATE collecte_benevole SET confirme = 1, date_confirmation = ? 
		WHERE collecte_id = ? AND benevole_id = ?
	`, now, collecteID, benevoleID)
	if err != nil {
		return err
	}

	var total, confirmes int
	err = r.db.QueryRow(`SELECT nb_benevoles FROM collecte WHERE id = ?`, collecteID).Scan(&total)
	if err != nil {
		return err
	}
	err = r.db.QueryRow(`
		SELECT COUNT(*) FROM collecte_benevole WHERE collecte_id = ? AND confirme = 1
	`, collecteID).Scan(&confirmes)
	if err != nil {
		return err
	}

	if confirmes >= total && total > 0 {
		return r.TerminerCollecte(collecteID)
	}
	return nil
}

func (r *CollecteRepository) TerminerCollecte(collecteID int) error {
	tx, err := r.db.Begin()
	if err != nil {
		return err
	}
	defer tx.Rollback()

	_, err = tx.Exec(`UPDATE collecte SET statut = 'Terminée' WHERE id = ?`, collecteID)
	if err != nil {
		return err
	}

	_, err = tx.Exec(`UPDATE produit SET statut = 'Stocké' WHERE collecte_id = ?`, collecteID)
	if err != nil {
		return err
	}

	return tx.Commit()
}

func (r *CollecteRepository) MettreAJour(collecte *modeles.Collecte) error {
	_, err := r.db.Exec(`
		UPDATE collecte 
		SET date_heure_collecte = ?, adresse_collecte = ?, commentaire = ?, commercant_id = ?
		WHERE id = ?
	`, collecte.DateHeureCollecte, collecte.AdresseCollecte, collecte.Commentaire, collecte.CommercantID, collecte.ID)
	return err
}

func (r *CollecteRepository) MettreAJourStatut(id int, statut string) error {
	_, err := r.db.Exec(`UPDATE collecte SET statut = ? WHERE id = ?`, statut, id)
	return err
}

func (r *CollecteRepository) Supprimer(id int) error {
	_, err := r.db.Exec(`DELETE FROM collecte WHERE id = ?`, id)
	return err
}