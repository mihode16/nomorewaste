package config

import (
	"fmt"
	"log"
	"os"

	"github.com/joho/godotenv"
)

type Config struct {
    DBHost     string
    DBPort     string
    DBUser     string
    DBPassword string
    DBName     string
    APIPort    string
}

func ChargerConfig() *Config {
    if err := godotenv.Load(); err != nil {
        log.Println("Aucun fichier .env trouvé, utilisation des variables d'environnement")
    }

    return &Config{
        DBHost:     getEnv("DB_HOST", "db"),
        DBPort:     getEnv("DB_PORT", "3306"),
        DBUser:     getEnv("DB_USER", "nomorewaste_user"),
        DBPassword: getEnv("DB_PASSWORD", "nomorewaste_pass"),
        DBName:     getEnv("DB_NAME", "nomorewaste"),
        APIPort:    getEnv("API_PORT", "8080"),
    }
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

// Fonction utilitaire gardée pour une utilisation future
// Si tu veux l'utiliser plus tard, décommente-la
/*
func getEnvInt(key string, defaultValue int) int {
    if value := os.Getenv(key); value != "" {
        if intVal, err := strconv.Atoi(value); err == nil {
            return intVal
        }
    }
    return defaultValue
}
*/