package repositories

import (
	"database/sql"

	"nomorewaste/internal/modeles"
)

type MessageRepository struct {
	db *sql.DB
}

func NouveauMessageRepository(db *sql.DB) *MessageRepository {
	return &MessageRepository{db: db}
}

// chargerParticipant résume un utilisateur pour l'affichage d'une conversation/d'un message
// (nom/prénom, et raison sociale en plus s'il s'agit d'un commerçant).
func (r *MessageRepository) chargerParticipant(id int) (*modeles.ParticipantConversation, error) {
	var p modeles.ParticipantConversation
	err := r.db.QueryRow(`SELECT id, nom, prenom, type_utilisateur FROM utilisateur WHERE id = ?`, id).
		Scan(&p.ID, &p.Nom, &p.Prenom, &p.TypeUtilisateur)
	if err != nil {
		return nil, err
	}
	if p.TypeUtilisateur == "commercant" {
		var raison sql.NullString
		r.db.QueryRow(`SELECT raison_sociale FROM commercant WHERE id = ?`, id).Scan(&raison)
		p.RaisonSociale = raison.String
	}
	return &p, nil
}

// CreerConversation ouvre une nouvelle discussion avec son premier message.
func (r *MessageRepository) CreerConversation(cc *modeles.ConversationCreation) (int, error) {
	tx, err := r.db.Begin()
	if err != nil {
		return 0, err
	}
	defer tx.Rollback()

	typeConv := cc.Type
	if typeConv == "" {
		typeConv = "admin"
	}
	var destinataireID interface{}
	if typeConv == "pair" && cc.DestinataireID > 0 {
		destinataireID = cc.DestinataireID
	}
	var collecteID interface{}
	if cc.CollecteID > 0 {
		collecteID = cc.CollecteID
	}

	result, err := tx.Exec(`
		INSERT INTO conversation (type, initiateur_id, destinataire_id, collecte_id, sujet)
		VALUES (?, ?, ?, ?, ?)
	`, typeConv, cc.InitiateurID, destinataireID, collecteID, cc.Sujet)
	if err != nil {
		return 0, err
	}
	id, _ := result.LastInsertId()

	_, err = tx.Exec(`
		INSERT INTO message (conversation_id, expediteur_id, contenu)
		VALUES (?, ?, ?)
	`, id, cc.InitiateurID, cc.Contenu)
	if err != nil {
		return 0, err
	}

	if err := tx.Commit(); err != nil {
		return 0, err
	}
	return int(id), nil
}

const selectConversationBase = `
	SELECT id, type, initiateur_id, destinataire_id, collecte_id, sujet, cloturee, date_creation, date_cloture
	FROM conversation
`

func scanConversation(rows *sql.Rows) (modeles.Conversation, error) {
	var co modeles.Conversation
	var destinataireID sql.NullInt64
	var collecteID sql.NullInt64
	var dateCloture sql.NullTime
	err := rows.Scan(&co.ID, &co.Type, &co.InitiateurID, &destinataireID, &collecteID, &co.Sujet, &co.Cloturee, &co.DateCreation, &dateCloture)
	if err != nil {
		return co, err
	}
	if destinataireID.Valid {
		id := int(destinataireID.Int64)
		co.DestinataireID = &id
	}
	if collecteID.Valid {
		id := int(collecteID.Int64)
		co.CollecteID = &id
	}
	if dateCloture.Valid {
		co.DateCloture = &dateCloture.Time
	}
	return co, nil
}

func (r *MessageRepository) enrichirParticipants(list []modeles.Conversation) {
	for i := range list {
		list[i].Initiateur, _ = r.chargerParticipant(list[i].InitiateurID)
		if list[i].DestinataireID != nil {
			list[i].Destinataire, _ = r.chargerParticipant(*list[i].DestinataireID)
		}
	}
}

// ListerPourAdmin retourne toutes les conversations adressées à l'association (type='admin'),
// avec le nombre de messages non encore lus par un admin. filtreCloturee : "" (toutes),
// "ouvertes" ou "cloturees".
func (r *MessageRepository) ListerPourAdmin(filtreCloturee string) ([]modeles.Conversation, error) {
	query := selectConversationBase + " WHERE type = 'admin'"
	if filtreCloturee == "ouvertes" {
		query += " AND cloturee = 0"
	} else if filtreCloturee == "cloturees" {
		query += " AND cloturee = 1"
	}
	query += " ORDER BY date_creation DESC"

	rows, err := r.db.Query(query)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var list []modeles.Conversation
	for rows.Next() {
		co, err := scanConversation(rows)
		if err != nil {
			return nil, err
		}
		list = append(list, co)
	}
	r.enrichirParticipants(list)
	for i := range list {
		r.db.QueryRow(`
			SELECT COUNT(*) FROM message
			WHERE conversation_id = ? AND expediteur_id = ? AND lu = 0
		`, list[i].ID, list[i].InitiateurID).Scan(&list[i].NbNonLus)
	}
	return list, nil
}

// ListerPourUtilisateur retourne les conversations d'un utilisateur (commerçant ou adhérent) :
// sa discussion avec l'association (type='admin') et, le cas échéant, ses messageries privées
// avec d'autres adhérents (type='pair'), avec le nombre de messages non lus par lui.
func (r *MessageRepository) ListerPourUtilisateur(utilisateurID int) ([]modeles.Conversation, error) {
	query := selectConversationBase + `
		WHERE (type = 'admin' AND initiateur_id = ?)
		   OR (type = 'pair' AND (initiateur_id = ? OR destinataire_id = ?))
		ORDER BY date_creation DESC
	`
	rows, err := r.db.Query(query, utilisateurID, utilisateurID, utilisateurID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var list []modeles.Conversation
	for rows.Next() {
		co, err := scanConversation(rows)
		if err != nil {
			return nil, err
		}
		list = append(list, co)
	}
	r.enrichirParticipants(list)
	for i := range list {
		r.db.QueryRow(`
			SELECT COUNT(*) FROM message
			WHERE conversation_id = ? AND expediteur_id != ? AND lu = 0
		`, list[i].ID, utilisateurID).Scan(&list[i].NbNonLus)
	}
	return list, nil
}

// TrouverParID récupère une conversation avec l'ensemble de ses messages (auteur inclus).
func (r *MessageRepository) TrouverParID(id int) (*modeles.Conversation, error) {
	rows, err := r.db.Query(selectConversationBase+" WHERE id = ?", id)
	if err != nil {
		return nil, err
	}
	var co *modeles.Conversation
	if rows.Next() {
		c, err := scanConversation(rows)
		if err != nil {
			rows.Close()
			return nil, err
		}
		co = &c
	}
	rows.Close()
	if co == nil {
		return nil, sql.ErrNoRows
	}
	list := []modeles.Conversation{*co}
	r.enrichirParticipants(list)
	*co = list[0]

	msgRows, err := r.db.Query(`
		SELECT id, conversation_id, expediteur_id, contenu, date_envoi, lu
		FROM message
		WHERE conversation_id = ?
		ORDER BY date_envoi ASC, id ASC
	`, id)
	if err != nil {
		return nil, err
	}
	defer msgRows.Close()

	for msgRows.Next() {
		var m modeles.Message
		if err := msgRows.Scan(&m.ID, &m.ConversationID, &m.ExpediteurID, &m.Contenu, &m.DateEnvoi, &m.Lu); err != nil {
			return nil, err
		}
		m.Expediteur, _ = r.chargerParticipant(m.ExpediteurID)
		co.Messages = append(co.Messages, m)
	}
	return co, nil
}

// AjouterMessage ajoute une réponse à une conversation existante.
func (r *MessageRepository) AjouterMessage(conversationID int, m *modeles.MessageCreation) (int, error) {
	result, err := r.db.Exec(`
		INSERT INTO message (conversation_id, expediteur_id, contenu)
		VALUES (?, ?, ?)
	`, conversationID, m.ExpediteurID, m.Contenu)
	if err != nil {
		return 0, err
	}
	id, _ := result.LastInsertId()
	return int(id), nil
}

func (r *MessageRepository) TrouverType(conversationID int) (string, bool, error) {
	var typeConv string
	var cloturee bool
	err := r.db.QueryRow(`SELECT type, cloturee FROM conversation WHERE id = ?`, conversationID).Scan(&typeConv, &cloturee)
	return typeConv, cloturee, err
}

func (r *MessageRepository) Cloturer(id int) error {
	_, err := r.db.Exec(`UPDATE conversation SET cloturee = 1, date_cloture = NOW() WHERE id = ?`, id)
	return err
}

// CompterNonLus compte, pour un rôle donné (admin ou utilisateur), le nombre de messages en
// attente de lecture. Pour un utilisateur, la recherche est restreinte à ses conversations.
func (r *MessageRepository) CompterNonLus(role string, utilisateurID int) (int, error) {
	var n int
	var err error
	if role == "admin" {
		err = r.db.QueryRow(`
			SELECT COUNT(*) FROM message m
			JOIN conversation co ON m.conversation_id = co.id
			WHERE co.type = 'admin' AND m.expediteur_id = co.initiateur_id AND m.lu = 0
		`).Scan(&n)
	} else {
		err = r.db.QueryRow(`
			SELECT COUNT(*) FROM message m
			JOIN conversation co ON m.conversation_id = co.id
			WHERE m.expediteur_id != ? AND m.lu = 0
			  AND ((co.type = 'admin' AND co.initiateur_id = ?)
			   OR (co.type = 'pair' AND (co.initiateur_id = ? OR co.destinataire_id = ?)))
		`, utilisateurID, utilisateurID, utilisateurID, utilisateurID).Scan(&n)
	}
	return n, err
}

// MarquerLu marque comme lus, pour le lecteur désigné, les messages qu'il n'a pas envoyés
// lui-même dans une conversation donnée.
func (r *MessageRepository) MarquerLu(conversationID int, lecteurID int) error {
	_, err := r.db.Exec(`UPDATE message SET lu = 1 WHERE conversation_id = ? AND expediteur_id != ?`, conversationID, lecteurID)
	return err
}

// MarquerLuAdmin marque comme lus, côté admin, les messages envoyés par l'initiateur d'une
// conversation type='admin' (n'importe quel admin peut avoir déjà répondu).
func (r *MessageRepository) MarquerLuAdmin(conversationID int) error {
	_, err := r.db.Exec(`
		UPDATE message SET lu = 1
		WHERE conversation_id = ? AND expediteur_id = (SELECT initiateur_id FROM conversation WHERE id = ?)
	`, conversationID, conversationID)
	return err
}
