package repositories

import (
	"database/sql"
	"time"

	"nomorewaste/internal/modeles"
)

type CommercantRepository struct {
    db *sql.DB
}

func NouveauCommercantRepository(db *sql.DB) *CommercantRepository {
    return &CommercantRepository{db: db}
}

func (r *CommercantRepository) Creer(commercant *modeles.CommercantCreation) (int, error) {
    tx, err := r.db.Begin()
    if err != nil {
        return 0, err
    }
    defer tx.Rollback()

    // Hash du mot de passe (à faire avec bcrypt)
    // Pour l'instant on stocke en clair, à améliorer

    // Insérer dans utilisateur
    result, err := tx.Exec(`
        INSERT INTO utilisateur (email, mot_de_passe, nom, prenom, telephone, adresse, type_utilisateur)
        VALUES (?, ?, ?, ?, ?, ?, 'commercant')
    `, commercant.Email, commercant.MotDePasse, commercant.Nom, commercant.Prenom,
        commercant.Telephone, commercant.Adresse)
    if err != nil {
        return 0, err
    }

    id, err := result.LastInsertId()
    if err != nil {
        return 0, err
    }

    // Insérer dans commercant
    dateDebut, err := time.Parse("2006-01-02", commercant.DateDebutAdhesion)
    if err != nil {
        return 0, err
    }
    dateFin, err := time.Parse("2006-01-02", commercant.DateFinAdhesion)
    if err != nil {
        return 0, err
    }

    _, err = tx.Exec(`
        INSERT INTO commercant (id, siret, raison_sociale, type_commerce, date_debut_adhesion, date_fin_adhesion, est_renouvele_automatiquement)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    `, id, commercant.Siret, commercant.RaisonSociale, commercant.TypeCommerce,
        dateDebut, dateFin, commercant.EstRenouveleAutomatiquement)
    if err != nil {
        return 0, err
    }

    if err = tx.Commit(); err != nil {
        return 0, err
    }

    return int(id), nil
}

func (r *CommercantRepository) TrouverTous() ([]modeles.Commercant, error) {
    query := `
        SELECT u.id, u.email, u.nom, u.prenom, u.telephone, u.adresse, u.date_inscription, u.est_actif,
               c.siret, c.raison_sociale, c.type_commerce, c.date_debut_adhesion, c.date_fin_adhesion, c.est_renouvele_automatiquement
        FROM utilisateur u
        JOIN commercant c ON u.id = c.id
        WHERE u.est_actif = 1
    `

    rows, err := r.db.Query(query)
    if err != nil {
        return nil, err
    }
    defer rows.Close()

    var commercants []modeles.Commercant
    for rows.Next() {
        var c modeles.Commercant
        err := rows.Scan(
            &c.ID, &c.Email, &c.Nom, &c.Prenom, &c.Telephone, &c.Adresse,
            &c.DateInscription, &c.EstActif,
            &c.Siret, &c.RaisonSociale, &c.TypeCommerce,
            &c.DateDebutAdhesion, &c.DateFinAdhesion, &c.EstRenouveleAutomatiquement,
        )
        if err != nil {
            return nil, err
        }
        commercants = append(commercants, c)
    }

    return commercants, nil
}

func (r *CommercantRepository) TrouverParID(id int) (*modeles.Commercant, error) {
    var c modeles.Commercant
    err := r.db.QueryRow(`
        SELECT u.id, u.email, u.nom, u.prenom, u.telephone, u.adresse, u.date_inscription, u.est_actif,
               c.siret, c.raison_sociale, c.type_commerce, c.date_debut_adhesion, c.date_fin_adhesion, c.est_renouvele_automatiquement
        FROM utilisateur u
        JOIN commercant c ON u.id = c.id
        WHERE u.id = ? AND u.est_actif = 1
    `, id).Scan(
        &c.ID, &c.Email, &c.Nom, &c.Prenom, &c.Telephone, &c.Adresse,
        &c.DateInscription, &c.EstActif,
        &c.Siret, &c.RaisonSociale, &c.TypeCommerce,
        &c.DateDebutAdhesion, &c.DateFinAdhesion, &c.EstRenouveleAutomatiquement,
    )
    if err != nil {
        return nil, err
    }
    return &c, nil
}

func (r *CommercantRepository) MettreAJour(commercant *modeles.Commercant) error {
    tx, err := r.db.Begin()
    if err != nil {
        return err
    }
    defer tx.Rollback()

    _, err = tx.Exec(`
        UPDATE utilisateur SET nom = ?, prenom = ?, telephone = ?, adresse = ?
        WHERE id = ?
    `, commercant.Nom, commercant.Prenom, commercant.Telephone, commercant.Adresse, commercant.ID)
    if err != nil {
        return err
    }

    _, err = tx.Exec(`
        UPDATE commercant SET siret = ?, raison_sociale = ?, type_commerce = ?,
            date_debut_adhesion = ?, date_fin_adhesion = ?, est_renouvele_automatiquement = ?
        WHERE id = ?
    `, commercant.Siret, commercant.RaisonSociale, commercant.TypeCommerce,
        commercant.DateDebutAdhesion, commercant.DateFinAdhesion,
        commercant.EstRenouveleAutomatiquement, commercant.ID)
    if err != nil {
        return err
    }

    return tx.Commit()
}

func (r *CommercantRepository) RenouvelerAdhesion(id int, nouvelleDateFin time.Time) error {
    _, err := r.db.Exec(`
        UPDATE commercant SET date_fin_adhesion = ? WHERE id = ?
    `, nouvelleDateFin, id)
    return err
}

func (r *CommercantRepository) TrouverAdhesionsExpirantBientot(joursAlerte int) ([]modeles.Commercant, error) {
    query := `
        SELECT u.id, u.email, u.nom, u.prenom, u.telephone, u.adresse, u.date_inscription, u.est_actif,
               c.siret, c.raison_sociale, c.type_commerce, c.date_debut_adhesion, c.date_fin_adhesion, c.est_renouvele_automatiquement
        FROM utilisateur u
        JOIN commercant c ON u.id = c.id
        WHERE u.est_actif = 1 AND c.date_fin_adhesion BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
    `

    rows, err := r.db.Query(query, joursAlerte)
    if err != nil {
        return nil, err
    }
    defer rows.Close()

    var commercants []modeles.Commercant
    for rows.Next() {
        var c modeles.Commercant
        err := rows.Scan(
            &c.ID, &c.Email, &c.Nom, &c.Prenom, &c.Telephone, &c.Adresse,
            &c.DateInscription, &c.EstActif,
            &c.Siret, &c.RaisonSociale, &c.TypeCommerce,
            &c.DateDebutAdhesion, &c.DateFinAdhesion, &c.EstRenouveleAutomatiquement,
        )
        if err != nil {
            return nil, err
        }
        commercants = append(commercants, c)
    }

    return commercants, nil
}

func (r *CommercantRepository) Supprimer(id int) error {
    _, err := r.db.Exec(`UPDATE utilisateur SET est_actif = 0 WHERE id = ?`, id)
    return err
}