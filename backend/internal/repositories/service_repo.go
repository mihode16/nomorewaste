package repositories

import (
	"database/sql"
	"time"

	"nomorewaste/internal/modeles"
)

type ServiceRepository struct {
	db *sql.DB
}

func NouveauServiceRepository(db *sql.DB) *ServiceRepository {
	return &ServiceRepository{db: db}
}

func (r *ServiceRepository) ListerServices() ([]modeles.Service, error) {
	rows, err := r.db.Query(`SELECT id, nom, description, type FROM service ORDER BY nom`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	var list []modeles.Service
	for rows.Next() {
		var s modeles.Service
		if err := rows.Scan(&s.ID, &s.Nom, &s.Description, &s.Type); err != nil {
			return nil, err
		}
		list = append(list, s)
	}
	return list, nil
}

func (r *ServiceRepository) CreerPlanning(p *modeles.ServicePlanningCreation) (int, error) {
	debut, err := parseDateTime(p.DateHeureDebut)
	if err != nil {
		return 0, err
	}
	fin, err := parseDateTime(p.DateHeureFin)
	if err != nil {
		return 0, err
	}
	result, err := r.db.Exec(`
		INSERT INTO service_planning (date_heure_debut, date_heure_fin, capacite_max, service_id, benevole_id)
		VALUES (?, ?, ?, ?, ?)
	`, debut, fin, p.CapaciteMax, p.ServiceID, p.BenevoleID)
	if err != nil {
		return 0, err
	}
	id, _ := result.LastInsertId()
	return int(id), nil
}

func (r *ServiceRepository) ListerPlannings() ([]modeles.ServicePlanning, error) {
	rows, err := r.db.Query(`
		SELECT sp.id, sp.date_heure_debut, sp.date_heure_fin, sp.capacite_max, sp.statut, sp.service_id, sp.benevole_id,
		       s.nom, s.description, s.type
		FROM service_planning sp
		JOIN service s ON s.id = sp.service_id
		ORDER BY sp.date_heure_debut DESC
	`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	var list []modeles.ServicePlanning
	for rows.Next() {
		var sp modeles.ServicePlanning
		var svc modeles.Service
		if err := rows.Scan(&sp.ID, &sp.DateHeureDebut, &sp.DateHeureFin, &sp.CapaciteMax, &sp.Statut, &sp.ServiceID, &sp.BenevoleID, &svc.Nom, &svc.Description, &svc.Type); err != nil {
			return nil, err
		}
		svc.ID = sp.ServiceID
		sp.Service = &svc
		list = append(list, sp)
	}
	return list, nil
}

func (r *ServiceRepository) CreerAdherent(a *modeles.AdherentCreation) (int, error) {
	tx, err := r.db.Begin()
	if err != nil {
		return 0, err
	}
	defer tx.Rollback()

	result, err := tx.Exec(`
		INSERT INTO utilisateur (email, mot_de_passe, nom, prenom, telephone, adresse, type_utilisateur)
		VALUES (?, ?, ?, ?, ?, ?, 'adherent')
	`, a.Email, a.MotDePasse, a.Nom, a.Prenom, a.Telephone, a.Adresse)
	if err != nil {
		return 0, err
	}
	id, _ := result.LastInsertId()

	debut, _ := time.Parse("2006-01-02", a.DateDebutAdhesion)
	fin, _ := time.Parse("2006-01-02", a.DateFinAdhesion)
	if debut.IsZero() {
		debut = time.Now()
	}
	if fin.IsZero() {
		fin = debut.AddDate(1, 0, 0)
	}

	_, err = tx.Exec(`INSERT INTO adherent (id, date_debut_adhesion, date_fin_adhesion) VALUES (?, ?, ?)`, id, debut, fin)
	if err != nil {
		return 0, err
	}
	return int(id), tx.Commit()
}

func (r *ServiceRepository) Inscrire(adherentID, planningID int) error {
	_, err := r.db.Exec(`
		INSERT INTO service_inscription (adherent_id, service_planning_id) VALUES (?, ?)
	`, adherentID, planningID)
	return err
}

func (r *ServiceRepository) ListerAdherents() ([]modeles.Adherent, error) {
	rows, err := r.db.Query(`
		SELECT u.id, u.email, u.nom, u.prenom, u.telephone, u.adresse, u.date_inscription, u.est_actif,
		       a.date_debut_adhesion, a.date_fin_adhesion
		FROM utilisateur u
		JOIN adherent a ON u.id = a.id
		WHERE u.est_actif = 1
		ORDER BY u.nom
	`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	var list []modeles.Adherent
	for rows.Next() {
		var a modeles.Adherent
		if err := rows.Scan(&a.ID, &a.Email, &a.Nom, &a.Prenom, &a.Telephone, &a.Adresse, &a.DateInscription, &a.EstActif, &a.DateDebutAdhesion, &a.DateFinAdhesion); err != nil {
			return nil, err
		}
		list = append(list, a)
	}
	return list, nil
}

func (r *ServiceRepository) CompterInscriptions(planningID int) (int, error) {
	var count int
	err := r.db.QueryRow(`SELECT COUNT(*) FROM service_inscription WHERE service_planning_id = ?`, planningID).Scan(&count)
	return count, err
}
