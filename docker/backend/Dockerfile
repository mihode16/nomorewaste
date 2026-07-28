FROM golang:1.21-alpine

WORKDIR /app

# Installer les dépendances système
RUN apk add --no-cache git curl

# Copier les fichiers de dépendances
COPY backend/go.mod backend/go.sum ./
RUN go mod download

# Copier le code source
COPY backend/ .

# Installer Air pour le hot-reload (développement)
RUN go install github.com/cosmtrek/air@latest

# Exposer le port
EXPOSE 8080

# Démarrer avec Air (hot-reload)
CMD ["air", "-c", ".air.toml"]