package database

import (
	"database/sql"
	"fmt"
	"log"

	"nomorewaste/internal/config"

	_ "github.com/go-sql-driver/mysql"
)

var DB *sql.DB

func Connecter() error {
    config := config.ChargerConfig()
    dsn := config.GetDSN()

    var err error
    DB, err = sql.Open("mysql", dsn)
    if err != nil {
        return fmt.Errorf("erreur de connexion à la base de données: %v", err)
    }

    if err = DB.Ping(); err != nil {
        return fmt.Errorf("erreur de ping à la base de données: %v", err)
    }

    DB.SetMaxOpenConns(25)
    DB.SetMaxIdleConns(25)
    DB.SetConnMaxLifetime(5 * 60) // 5 minutes

    log.Println("Connexion à la base de données réussie")
    return nil
}

func Fermer() error {
    if DB != nil {
        return DB.Close()
    }
    return nil
}