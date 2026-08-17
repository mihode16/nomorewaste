package utils

import (
	"io"
	"log"

	"nomorewaste/internal/config"

	"gopkg.in/mail.v2"
)

// PieceJointe est un fichier à joindre à un email (ex: le planning .xlsx d'un bénévole).
type PieceJointe struct {
	Nom     string
	Contenu []byte
}

// EnvoyerEmail envoie un email HTML via le SMTP configuré. Si aucun identifiant SMTP n'est
// configuré, l'envoi est simplement journalisé et ignoré.
func EnvoyerEmail(cfg *config.Config, destinataire, sujet, corpsHTML string, piecesJointes ...PieceJointe) error {
	if !cfg.EmailActif() {
		log.Printf("✉️ (SMTP non configuré, email non envoyé) À: %s — Sujet: %s", destinataire, sujet)
		return nil
	}

	m := mail.NewMessage()
	m.SetHeader("From", cfg.SMTPFrom)
	m.SetHeader("To", destinataire)
	m.SetHeader("Subject", sujet)
	m.SetBody("text/html", corpsHTML)

	for _, pj := range piecesJointes {
		contenu := pj.Contenu
		m.Attach(pj.Nom, mail.SetCopyFunc(func(w io.Writer) error {
			_, err := w.Write(contenu)
			return err
		}))
	}

	d := mail.NewDialer(cfg.SMTPHost, cfg.SMTPPort, cfg.SMTPUser, cfg.SMTPPassword)
	return d.DialAndSend(m)
}
