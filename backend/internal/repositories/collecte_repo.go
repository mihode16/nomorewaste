package repositories

import (
	"database/sql"
	"time"

	"nomorewaste/internal/modeles"
)

type CollecteRepository struct {
    db *sql.DB
}

func NouveauCollecteRepository(db *sql.DB) *CollecteRepository {
    return &CollecteRepository{db: db}
}

func (r *CollecteRepository) Creer(collecte *modeles.CollecteCreation) (int, error) {
    dateHeure, err := time.Parse("2006-01-02 15:04:05", collecte.DateHeureCollecte)
    if err != nil {
        // Essayer un autre format
        dateHeure, err = time.Parse("2006-01-02T15:04:05", collecte.DateHeureCollecte)
        if err != nil {
            return 0, err
        }
    }

    result, err := r.db.Exec(`
        INSERT INTO collecte (date_heure_collecte, adresse_collecte, commentaire, commercant_id)
        VALUES (?, ?, ?, ?)
    `, dateHeure, collecte.AdresseCollecte, collecte.Commentaire, collecte.CommercantID)
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
    rows, err := r.db.Query(`
        SELECT c.id, c.date_heure_collecte, c.adresse_collecte, c.statut, c.commentaire, c.commercant_id,
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
        
        err := rows.Scan(
            &c.ID, &c.DateHeureCollecte, &c.AdresseCollecte, &c.Statut, &c.Commentaire, &c.CommercantID,
            &u.ID, &u.Email, &u.Nom, &u.Prenom, &u.Telephone, &u.Adresse, &u.DateInscription, &u.EstActif,
            &cm.Siret, &cm.RaisonSociale, &cm.TypeCommerce, &cm.DateDebutAdhesion, &cm.DateFinAdhesion, &cm.EstRenouveleAutomatiquement,
        )
        if err != nil {
            return nil, err
        }
        
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

    err := r.db.QueryRow(`
        SELECT c.id, c.date_heure_collecte, c.adresse_collecte, c.statut, c.commentaire, c.commercant_id,
               u.id, u.email, u.nom, u.prenom, u.telephone, u.adresse, u.date_inscription, u.est_actif,
               cm.siret, cm.raison_sociale, cm.type_commerce, cm.date_debut_adhesion, cm.date_fin_adhesion, cm.est_renouvele_automatiquement
        FROM collecte c
        LEFT JOIN commercant cm ON c.commercant_id = cm.id
        LEFT JOIN utilisateur u ON cm.id = u.id
        WHERE c.id = ?
    `, id).Scan(
        &c.ID, &c.DateHeureCollecte, &c.AdresseCollecte, &c.Statut, &c.Commentaire, &c.CommercantID,
        &u.ID, &u.Email, &u.Nom, &u.Prenom, &u.Telephone, &u.Adresse, &u.DateInscription, &u.EstActif,
        &cm.Siret, &cm.RaisonSociale, &cm.TypeCommerce, &cm.DateDebutAdhesion, &cm.DateFinAdhesion, &cm.EstRenouveleAutomatiquement,
    )
    if err != nil {
        return nil, err
    }

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

func (r *CollecteRepository) Supprimer(id int) error {
    _, err := r.db.Exec(`DELETE FROM collecte WHERE id = ?`, id)
    return err
}