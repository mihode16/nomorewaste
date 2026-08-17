package config

import (
	"fmt"
	"log"
	"os"
	"strconv"

	"github.com/joho/godotenv"
)

type Config struct {
	DBHost     string
	DBPort     string
	DBUser     string
	DBPassword string
	DBName     string
	APIPort    string
	LogoPath   string

	// SMTP : utilisé pour les rappels de renouvellement d'adhésion et l'envoi quotidien
	// des plannings bénévoles.
	SMTPHost     string
	SMTPPort     int
	SMTPUser     string
	SMTPPassword string
	SMTPFrom     string

	// SiteURL sert à construire des liens cliquables dans les emails (ex: vers l'espace adhérent).
	SiteURL string

	// Heure locale (0-23) à laquelle tourne la tâche planifiée quotidienne (rappels + plannings).
	SchedulerHeure int
}

func ChargerConfig() *Config {
	if err := godotenv.Load(); err != nil {
		log.Println("Aucun fichier .env trouvé, utilisation des variables d'environnement")
	}

	smtpFrom := getEnv("SMTP_FROM", "")
	smtpUser := getEnv("SMTP_USER", "")
	if smtpFrom == "" {
		smtpFrom = smtpUser
	}

	return &Config{
		DBHost:     getEnv("DB_HOST", "127.0.0.1"),
		DBPort:     getEnv("DB_PORT", "3306"),
		DBUser:     getEnv("DB_USER", "root"),
		DBPassword: getEnv("DB_PASSWORD", "root"),
		DBName:     getEnv("DB_NAME", "nomorewaste"),
		APIPort:    getEnv("API_PORT", "8081"),
		LogoPath:   getEnv("LOGO_PATH", "../assets/logo.png"),

		SMTPHost:     getEnv("SMTP_HOST", "smtp.gmail.com"),
		SMTPPort:     getEnvInt("SMTP_PORT", 587),
		SMTPUser:     smtpUser,
		SMTPPassword: getEnv("SMTP_PASSWORD", ""),
		SMTPFrom:     smtpFrom,

		SiteURL: getEnv("SITE_URL", "http://localhost/nomorewaste/frontend/public"),

		SchedulerHeure: getEnvInt("SCHEDULER_HEURE", 6),
	}
}

// EmailActif indique si les identifiants SMTP nécessaires à l'envoi d'emails sont configurés.
func (c *Config) EmailActif() bool {
	return c.SMTPUser != "" && c.SMTPPassword != ""
}

func (c *Config) GetDSN() string {
	return fmt.Sprintf("%s:%s@tcp(%s:%s)/%s?charset=utf8mb4&parseTime=True&loc=Local",
		c.DBUser, c.DBPassword, c.DBHost, c.DBPort, c.DBName)
}

func getEnv(key, defaultValue string) string {
	if value := os.Getenv(key); value != "" {
		return value
	}
	return defaultValue
}

func getEnvInt(key string, defaultValue int) int {
	if value := os.Getenv(key); value != "" {
		if intVal, err := strconv.Atoi(value); err == nil {
			return intVal
		}
	}
	return defaultValue
}
