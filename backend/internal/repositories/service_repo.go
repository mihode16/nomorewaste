package repositories

import (
	"database/sql"
	"strings"
	"time"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/utils"
)

type ServiceRepository struct {
	db *sql.DB
}

func NouveauServiceRepository(db *sql.DB) *ServiceRepository {
	return &ServiceRepository{db: db}
}

func (r *ServiceRepository) CompetencesPourService(serviceID int) ([]modeles.Competence, error) {
	rows, err := r.db.Query(`
		SELECT c.id, c.nom, c.description FROM competence c
		JOIN service_competence sc ON sc.competence_id = c.id
		WHERE sc.service_id = ?
		ORDER BY c.nom
	`, serviceID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	var list []modeles.Competence
	for rows.Next() {
		var c modeles.Competence
		if err := rows.Scan(&c.ID, &c.Nom, &c.Description); err != nil {
			return nil, err
		}
		list = append(list, c)
	}
	return list, nil
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
	for i := range list {
		list[i].Competences, _ = r.CompetencesPourService(list[i].ID)
	}
	return list, nil
}

// CreerService crée le service et, si NouvelleCompetence est renseigné, crée cette
// compétence et l'associe au service, le tout dans une même transaction.
func (r *ServiceRepository) CreerService(s *modeles.ServiceCreation) (int, error) {
	tx, err := r.db.Begin()
	if err != nil {
		return 0, err
	}
	defer tx.Rollback()

	result, err := tx.Exec(`INSERT INTO service (nom, description, type) VALUES (?, ?, ?)`, s.Nom, s.Description, s.Type)
	if err != nil {
		return 0, err
	}
	id, _ := result.LastInsertId()

	nom := strings.TrimSpace(s.NouvelleCompetence)
	if nom != "" {
		compResult, err := tx.Exec(`INSERT INTO competence (nom, description) VALUES (?, ?)`, nom, s.DescriptionCompetence)
		if err != nil {
			return 0, err
		}
		competenceID, _ := compResult.LastInsertId()
		if _, err := tx.Exec(`INSERT INTO service_competence (service_id, competence_id) VALUES (?, ?)`, id, competenceID); err != nil {
			return 0, err
		}
	}

	if err := tx.Commit(); err != nil {
		return 0, err
	}
	return int(id), nil
}

func (r *ServiceRepository) TrouverServiceParID(id int) (*modeles.Service, error) {
	var s modeles.Service
	err := r.db.QueryRow(`SELECT id, nom, description, type FROM service WHERE id = ?`, id).
		Scan(&s.ID, &s.Nom, &s.Description, &s.Type)
	if err != nil {
		return nil, err
	}
	s.Competences, _ = r.CompetencesPourService(s.ID)
	return &s, nil
}

func (r *ServiceRepository) MettreAJourService(s *modeles.Service) error {
	_, err := r.db.Exec(`UPDATE service SET nom = ?, description = ?, type = ? WHERE id = ?`, s.Nom, s.Description, s.Type, s.ID)
	return err
}

func (r *ServiceRepository) SupprimerService(id int) error {
	_, err := r.db.Exec(`DELETE FROM service WHERE id = ?`, id)
	return err
}

func (r *ServiceRepository) CreerPlanning(p *modeles.ServicePlanningCreation) (int, error) {
	debut, err := utils.ParseDateTime(p.DateHeureDebut)
	if err != nil {
		return 0, err
	}
	fin, err := utils.ParseDateTime(p.DateHeureFin)
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
		       s.nom, s.description, s.type,
		       u.nom, u.prenom,
		       (SELECT COUNT(*) FROM service_inscription si WHERE si.service_planning_id = sp.id) AS nb_inscrits
		FROM service_planning sp
		JOIN service s ON s.id = sp.service_id
		LEFT JOIN benevole b ON b.id = sp.benevole_id
		LEFT JOIN utilisateur u ON u.id = b.id
		ORDER BY sp.date_heure_debut DESC
	`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	var list []modeles.ServicePlanning
	for rows.Next() {
		sp, err := scanPlanningRow(rows)
		if err != nil {
			return nil, err
		}
		list = append(list, *sp)
	}
	return list, nil
}

func scanPlanningRow(rows *sql.Rows) (*modeles.ServicePlanning, error) {
	var sp modeles.ServicePlanning
	var svc modeles.Service
	var benevoleNom, benevolePrenom sql.NullString
	if err := rows.Scan(
		&sp.ID, &sp.DateHeureDebut, &sp.DateHeureFin, &sp.CapaciteMax, &sp.Statut, &sp.ServiceID, &sp.BenevoleID,
		&svc.Nom, &svc.Description, &svc.Type, &benevoleNom, &benevolePrenom, &sp.NbInscrits,
	); err != nil {
		return nil, err
	}
	svc.ID = sp.ServiceID
	sp.Service = &svc
	if sp.BenevoleID > 0 && benevoleNom.Valid {
		var benevole modeles.Benevole
		benevole.ID = sp.BenevoleID
		benevole.Nom = benevoleNom.String
		benevole.Prenom = benevolePrenom.String
		sp.Benevole = &benevole
	}
	return &sp, nil
}

func (r *ServiceRepository) TrouverPlanningParID(id int) (*modeles.ServicePlanning, error) {
	rows, err := r.db.Query(`
		SELECT sp.id, sp.date_heure_debut, sp.date_heure_fin, sp.capacite_max, sp.statut, sp.service_id, sp.benevole_id,
		       s.nom, s.description, s.type,
		       u.nom, u.prenom,
		       (SELECT COUNT(*) FROM service_inscription si WHERE si.service_planning_id = sp.id) AS nb_inscrits
		FROM service_planning sp
		JOIN service s ON s.id = sp.service_id
		LEFT JOIN benevole b ON b.id = sp.benevole_id
		LEFT JOIN utilisateur u ON u.id = b.id
		WHERE sp.id = ?
	`, id)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	if !rows.Next() {
		return nil, sql.ErrNoRows
	}
	return scanPlanningRow(rows)
}

func (r *ServiceRepository) MettreAJourPlanning(p *modeles.ServicePlanningUpdate) error {
	debut, err := utils.ParseDateTime(p.DateHeureDebut)
	if err != nil {
		return err
	}
	fin, err := utils.ParseDateTime(p.DateHeureFin)
	if err != nil {
		return err
	}
	var benevoleID interface{}
	if p.BenevoleID > 0 {
		benevoleID = p.BenevoleID
	}
	_, err = r.db.Exec(`
		UPDATE service_planning
		SET date_heure_debut = ?, date_heure_fin = ?, capacite_max = ?, service_id = ?, benevole_id = ?, statut = ?
		WHERE id = ?
	`, debut, fin, p.CapaciteMax, p.ServiceID, benevoleID, p.Statut, p.ID)
	return err
}

func (r *ServiceRepository) SupprimerPlanning(id int) error {
	_, err := r.db.Exec(`DELETE FROM service_planning WHERE id = ?`, id)
	return err
}

func (r *ServiceRepository) CreerAdherent(a *modeles.AdherentCreation) (int, error) {
	tx, err := r.db.Begin()
	if err != nil {
		return 0, err
	}
	defer tx.Rollback()

	var nbExistants int
	if err = tx.QueryRow(`SELECT COUNT(*) FROM utilisateur WHERE email = ?`, a.Email).Scan(&nbExistants); err != nil {
		return 0, err
	}
	if nbExistants > 0 {
		return 0, ErrEmailExiste
	}

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

	_, err = tx.Exec(`
		INSERT INTO adherent (id, date_debut_adhesion, date_fin_adhesion, statut_adhesion, demande_renouvellement, est_renouvele_automatiquement)
		VALUES (?, ?, ?, 'en_attente', 0, ?)
	`, id, debut, fin, a.EstRenouveleAutomatiquement)
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

func (r *ServiceRepository) SupprimerInscription(adherentID, planningID int) error {
	_, err := r.db.Exec(`
		DELETE FROM service_inscription WHERE adherent_id = ? AND service_planning_id = ?
	`, adherentID, planningID)
	return err
}

func (r *ServiceRepository) ListerInscriptionsParAdherent(adherentID int) ([]modeles.ServicePlanning, error) {
	rows, err := r.db.Query(`
		SELECT sp.id, sp.date_heure_debut, sp.date_heure_fin, sp.capacite_max, sp.statut, sp.service_id, sp.benevole_id,
		       s.nom, s.description, s.type,
		       u.nom, u.prenom,
		       (SELECT COUNT(*) FROM service_inscription si2 WHERE si2.service_planning_id = sp.id) AS nb_inscrits
		FROM service_inscription si
		JOIN service_planning sp ON sp.id = si.service_planning_id
		JOIN service s ON s.id = sp.service_id
		LEFT JOIN benevole b ON b.id = sp.benevole_id
		LEFT JOIN utilisateur u ON u.id = b.id
		WHERE si.adherent_id = ?
		ORDER BY sp.date_heure_debut DESC
	`, adherentID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	var list []modeles.ServicePlanning
	for rows.Next() {
		sp, err := scanPlanningRow(rows)
		if err != nil {
			return nil, err
		}
		list = append(list, *sp)
	}
	return list, nil
}

func (r *ServiceRepository) ListerAdherents(recherche, statut string) ([]modeles.Adherent, error) {
	query := `
		SELECT u.id, u.email, u.nom, u.prenom, u.telephone, u.adresse, u.date_inscription, u.est_actif,
		       a.date_debut_adhesion, a.date_fin_adhesion, a.statut_adhesion, a.demande_renouvellement, a.est_renouvele_automatiquement
		FROM utilisateur u
		JOIN adherent a ON u.id = a.id
		WHERE u.est_actif = 1
	`
	var args []interface{}
	if recherche != "" {
		query += " AND (u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ?)"
		like := "%" + recherche + "%"
		args = append(args, like, like, like)
	}
	if statut != "" {
		query += " AND a.statut_adhesion = ?"
		args = append(args, statut)
	}
	query += " ORDER BY u.nom"

	rows, err := r.db.Query(query, args...)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	var list []modeles.Adherent
	for rows.Next() {
		a, err := scanAdherentRow(rows)
		if err != nil {
			return nil, err
		}
		list = append(list, *a)
	}
	return list, nil
}

func scanAdherentRow(rows *sql.Rows) (*modeles.Adherent, error) {
	var a modeles.Adherent
	var telephone, adresse sql.NullString
	if err := rows.Scan(
		&a.ID, &a.Email, &a.Nom, &a.Prenom, &telephone, &adresse, &a.DateInscription, &a.EstActif,
		&a.DateDebutAdhesion, &a.DateFinAdhesion, &a.StatutAdhesion, &a.DemandeRenouvellement, &a.EstRenouveleAutomatiquement,
	); err != nil {
		return nil, err
	}
	a.Telephone = telephone.String
	a.Adresse = adresse.String
	return &a, nil
}

func (r *ServiceRepository) TrouverAdherentParID(id int) (*modeles.Adherent, error) {
	rows, err := r.db.Query(`
		SELECT u.id, u.email, u.nom, u.prenom, u.telephone, u.adresse, u.date_inscription, u.est_actif,
		       a.date_debut_adhesion, a.date_fin_adhesion, a.statut_adhesion, a.demande_renouvellement, a.est_renouvele_automatiquement
		FROM utilisateur u
		JOIN adherent a ON u.id = a.id
		WHERE u.id = ? AND u.est_actif = 1
	`, id)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	if !rows.Next() {
		return nil, sql.ErrNoRows
	}
	return scanAdherentRow(rows)
}

func (r *ServiceRepository) MettreAJourAdherent(a *modeles.Adherent) error {
	tx, err := r.db.Begin()
	if err != nil {
		return err
	}
	defer tx.Rollback()

	_, err = tx.Exec(`UPDATE utilisateur SET nom = ?, prenom = ?, telephone = ?, adresse = ? WHERE id = ?`,
		a.Nom, a.Prenom, a.Telephone, a.Adresse, a.ID)
	if err != nil {
		return err
	}

	_, err = tx.Exec(`
		UPDATE adherent SET date_debut_adhesion = ?, date_fin_adhesion = ?, est_renouvele_automatiquement = ?
		WHERE id = ?
	`, a.DateDebutAdhesion, a.DateFinAdhesion, a.EstRenouveleAutomatiquement, a.ID)
	if err != nil {
		return err
	}

	return tx.Commit()
}

func (r *ServiceRepository) ValiderAdhesionAdherent(id int) error {
	_, err := r.db.Exec(`UPDATE adherent SET statut_adhesion = 'valide' WHERE id = ?`, id)
	return err
}

func (r *ServiceRepository) DemanderRenouvellementAdherent(id int) error {
	_, err := r.db.Exec(`UPDATE adherent SET demande_renouvellement = 1 WHERE id = ?`, id)
	return err
}

func (r *ServiceRepository) RenouvelerAdhesionAdherent(id int, nouvelleDateFin time.Time) error {
	_, err := r.db.Exec(`
		UPDATE adherent SET date_fin_adhesion = ?, demande_renouvellement = 0 WHERE id = ?
	`, nouvelleDateFin, id)
	return err
}

func (r *ServiceRepository) SupprimerAdherent(id int) error {
	_, err := r.db.Exec(`UPDATE utilisateur SET est_actif = 0 WHERE id = ?`, id)
	return err
}

func (r *ServiceRepository) TrouverAdhesionsAdherentExpirantBientot(moisAlerte int) ([]modeles.Adherent, error) {
	rows, err := r.db.Query(`
		SELECT u.id, u.email, u.nom, u.prenom, u.telephone, u.adresse, u.date_inscription, u.est_actif,
		       a.date_debut_adhesion, a.date_fin_adhesion, a.statut_adhesion, a.demande_renouvellement, a.est_renouvele_automatiquement
		FROM utilisateur u
		JOIN adherent a ON u.id = a.id
		WHERE u.est_actif = 1 AND a.statut_adhesion = 'valide'
		  AND a.date_fin_adhesion BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? MONTH)
	`, moisAlerte)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	var list []modeles.Adherent
	for rows.Next() {
		a, err := scanAdherentRow(rows)
		if err != nil {
			return nil, err
		}
		list = append(list, *a)
	}
	return list, nil
}

func (r *ServiceRepository) CompterInscriptions(planningID int) (int, error) {
	var count int
	err := r.db.QueryRow(`SELECT COUNT(*) FROM service_inscription WHERE service_planning_id = ?`, planningID).Scan(&count)
	return count, err
}
