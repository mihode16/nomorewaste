package services

import (
	"errors"

	"nomorewaste/internal/modeles"
	"nomorewaste/internal/repositories"
)

type MessageService struct {
	repo *repositories.MessageRepository
}

func NouveauMessageService(repo *repositories.MessageRepository) *MessageService {
	return &MessageService{repo: repo}
}

func (s *MessageService) CreerConversation(cc *modeles.ConversationCreation) (int, error) {
	if cc.InitiateurID <= 0 {
		return 0, errors.New("initiateur requis")
	}
	if cc.Sujet == "" || cc.Contenu == "" {
		return 0, errors.New("sujet et contenu requis")
	}
	if cc.Type == "pair" && cc.DestinataireID <= 0 {
		return 0, errors.New("destinataire requis pour un message entre adhérents")
	}
	if cc.Type == "pair" && cc.DestinataireID == cc.InitiateurID {
		return 0, errors.New("impossible de s'envoyer un message à soi-même")
	}
	return s.repo.CreerConversation(cc)
}

func (s *MessageService) ListerPourAdmin(filtreCloturee string) ([]modeles.Conversation, error) {
	return s.repo.ListerPourAdmin(filtreCloturee)
}

func (s *MessageService) ListerPourUtilisateur(utilisateurID int) ([]modeles.Conversation, error) {
	return s.repo.ListerPourUtilisateur(utilisateurID)
}

func (s *MessageService) TrouverParID(id int) (*modeles.Conversation, error) {
	return s.repo.TrouverParID(id)
}

func (s *MessageService) AjouterMessage(conversationID int, m *modeles.MessageCreation) (int, error) {
	if m.Contenu == "" {
		return 0, errors.New("contenu requis")
	}
	if m.ExpediteurID <= 0 {
		return 0, errors.New("expéditeur requis")
	}
	_, cloturee, err := s.repo.TrouverType(conversationID)
	if err != nil {
		return 0, errors.New("conversation introuvable")
	}
	if cloturee {
		return 0, errors.New("cette conversation est clôturée")
	}
	return s.repo.AjouterMessage(conversationID, m)
}

func (s *MessageService) Cloturer(id int) error {
	typeConv, _, err := s.repo.TrouverType(id)
	if err != nil {
		return errors.New("conversation introuvable")
	}
	if typeConv != "admin" {
		return errors.New("seule une conversation avec l'association peut être clôturée")
	}
	return s.repo.Cloturer(id)
}

func (s *MessageService) CompterNonLus(role string, utilisateurID int) (int, error) {
	return s.repo.CompterNonLus(role, utilisateurID)
}

func (s *MessageService) MarquerLu(conversationID int, role string, utilisateurID int) error {
	if role == "admin" {
		return s.repo.MarquerLuAdmin(conversationID)
	}
	return s.repo.MarquerLu(conversationID, utilisateurID)
}
