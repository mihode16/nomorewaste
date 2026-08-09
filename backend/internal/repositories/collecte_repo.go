package repositories

import (
	"database/sql"
	"nomorewaste/internal/modeles"
)

type CollecteRepository struct {
	db *sql.DB
}

func NouveauCollecteRepository(db *sql.DB) *CollecteRepository {
	return &CollecteRepository{db: db}
}

func (r *CollecteRepository) Creer(collecte *modeles.CollecteCreation) (int, error) {
    dateHeure, err := parseDateTime(collecte.DateHeureCollecte)
    if err != nil {
        return 0, err
    }

    result, err := r.db.Exec(`
        INSERT INTO collecte (date_heure_collecte, adresse_collecte, commentaire, commercant_id, validee)
        VALUES (?, ?, ?, ?, ?)
    `, dateHeure, collecte.AdresseCollecte, collecte.Commentaire, collecte.CommercantID, 0)
	if err != nil {
		return 0, err
	}

	id, err := result.LastInsertId()
	if err != nil {
		return 0, err
	}

	return int(id), nil
}

func (r *CollecteRepository) TrouverTous() ([]modeles.Collecte, error) {
	rows, err := r.db.Query( `
		SELECT c.id, c.date_heure_collecte, c.adresse_collecte, c.statut, c.commentaire, c.commercant_id, c.validee,
			u.id, u.email, u.nom, u.prenom, u.telephone, u.adresse, u.date_inscription, u.est_actif,
			cm.siret, cm.raison_sociale, cm.type_commerce, cm.date_debut_adhesion, cm.date_fin_adhesion, cm.est_renouvele_automatiquement
		FROM collecte c
		LEFT JOIN commercant cm ON c.commercant_id = cm.id
		LEFT JOIN utilisateur u ON cm.id = u.id
		ORDER BY c.date_heure_collecte DESC
	`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var collectes []modeles.Collecte
	for rows.Next() {
		var c modeles.Collecte
		var cm modeles.Commercant
		var u modeles.Utilisateur

		// Nullables
		var commentaire sql.NullString
		var email, nom, prenom, telephone, adresse sql.NullString
		var siret, raisonSociale, typeCommerce sql.NullString

		err := rows.Scan(
			&c.ID, &c.DateHeureCollecte, &c.AdresseCollecte, &c.Statut, &commentaire, &c.CommercantID, &c.Validee,
			&u.ID, &email, &nom, &prenom, &telephone, &adresse, &u.DateInscription, &u.EstActif,
			&siret, &raisonSociale, &typeCommerce, &cm.DateDebutAdhesion, &cm.DateFinAdhesion, &cm.EstRenouveleAutomatiquement,
		)
		if err != nil {
			return nil, err
		}

		// Assignation
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
		collectes = append(collectes, c)
	}

	return collectes, nil
}

func (r *CollecteRepository) TrouverParID(id int) (*modeles.Collecte, error) {
	var c modeles.Collecte
	var cm modeles.Commercant
	var u modeles.Utilisateur

	var commentaire sql.NullString
	var email, nom, prenom, telephone, adresse sql.NullString
	var siret, raisonSociale, typeCommerce sql.NullString

	err := r.db.QueryRow(`
		SELECT c.id, c.date_heure_collecte, c.adresse_collecte, c.statut, c.commentaire, c.commercant_id, c.validee,
		       u.id, u.email, u.nom, u.prenom, u.telephone, u.adresse, u.date_inscription, u.est_actif,
		       cm.siret, cm.raison_sociale, cm.type_commerce, cm.date_debut_adhesion, cm.date_fin_adhesion, cm.est_renouvele_automatiquement
		FROM collecte c
		LEFT JOIN commercant cm ON c.commercant_id = cm.id
		LEFT JOIN utilisateur u ON cm.id = u.id
		WHERE c.id = ?
	`, id).Scan(
		&c.ID, &c.DateHeureCollecte, &c.AdresseCollecte, &c.Statut, &commentaire, &c.CommercantID, &c.Validee,
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
	return &c, nil
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
	_, err := r.db.Exec(`
		UPDATE collecte SET statut = ? WHERE id = ?
	`, statut, id)
	return err
}

func (r *CollecteRepository) ValiderCollecte(id int) error {
    _, err := r.db.Exec(`UPDATE collecte SET validee = 1 WHERE id = ?`, id)
    return err
}

func (r *CollecteRepository) Supprimer(id int) error {
	_, err := r.db.Exec(`DELETE FROM collecte WHERE id = ?`, id)
	return err
}