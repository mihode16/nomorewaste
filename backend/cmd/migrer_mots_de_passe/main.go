// Commande à usage unique : hache en bcrypt tous les mots de passe encore stockés en clair
// dans la table utilisateur (ceux créés avant la mise en place du hachage). Un mot de passe
// déjà haché en bcrypt (préfixe $2a$/$2b$/$2y$, longueur 60) est laissé tel quel, ce qui rend
// la commande sûre à relancer plusieurs fois sans double-hacher les mots de passe déjà migrés.
package main

import (
	"log"
	"strings"

	"nomorewaste/internal/config"
	"nomorewaste/internal/database"
	"nomorewaste/internal/utils"
)

func ressembleAUnHachageBcrypt(valeur string) bool {
	return len(valeur) == 60 && (strings.HasPrefix(valeur, "$2a$") || strings.HasPrefix(valeur, "$2b$") || strings.HasPrefix(valeur, "$2y$"))
}

func main() {
	if err := database.Connecter(config.ChargerConfig()); err != nil {
		log.Fatal("Erreur de connexion à la base de données:", err)
	}
	defer database.Fermer()

	db := database.DB

	rows, err := db.Query(`SELECT id, mot_de_passe FROM utilisateur`)
	if err != nil {
		log.Fatal("Erreur lecture des utilisateurs:", err)
	}

	type utilisateur struct {
		id         int
		motDePasse string
	}
	var aTraiter []utilisateur
	for rows.Next() {
		var u utilisateur
		if err := rows.Scan(&u.id, &u.motDePasse); err != nil {
			log.Fatal("Erreur lecture ligne:", err)
		}
		if !ressembleAUnHachageBcrypt(u.motDePasse) {
			aTraiter = append(aTraiter, u)
		}
	}
	rows.Close()

	log.Printf("%d mot(s) de passe en clair à migrer vers bcrypt (sur un total scanné)", len(aTraiter))

	nbOK := 0
	for _, u := range aTraiter {
		hache, err := utils.HacherMotDePasse(u.motDePasse)
		if err != nil {
			log.Printf("❌ Échec hachage pour utilisateur id=%d: %v", u.id, err)
			continue
		}
		if _, err := db.Exec(`UPDATE utilisateur SET mot_de_passe = ? WHERE id = ?`, hache, u.id); err != nil {
			log.Printf("❌ Échec mise à jour pour utilisateur id=%d: %v", u.id, err)
			continue
		}
		nbOK++
	}

	log.Printf("✅ Migration terminée : %d/%d mot(s) de passe hachés avec succès", nbOK, len(aTraiter))
}
