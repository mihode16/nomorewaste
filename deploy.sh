#!/usr/bin/env bash
# Script de déploiement automatique NO MORE WASTE (Docker).
# Construit les images, démarre la base de données (avec son schéma + données de départ),
# l'API Go et le frontend PHP, puis vérifie que le site répond avant de rendre la main.
#
# Usage : ./deploy.sh

set -euo pipefail
cd "$(dirname "$0")"

if ! command -v docker &> /dev/null; then
    echo "❌ Docker n'est pas installé ou n'est pas dans le PATH." >&2
    exit 1
fi

if [ ! -f .env ]; then
    echo "ℹ️  Aucun fichier .env trouvé, création à partir de .env.example (à personnaliser si besoin)."
    cp .env.example .env
fi

echo "🚀 Construction des images et démarrage des services..."
docker compose up -d --build

echo "⏳ Attente que les services soient prêts..."
HTTP_PORT=$( (grep -E '^HTTP_PORT_HOTE=' .env || true) | cut -d= -f2 | tr -d '\r' )
HTTP_PORT=${HTTP_PORT:-80}
API_PORT=$( (grep -E '^API_PORT_HOTE=' .env || true) | cut -d= -f2 | tr -d '\r' )
API_PORT=${API_PORT:-8081}

ATTENTE_MAX=90
ecoule=0
until curl -sf "http://localhost:${API_PORT}/api/health" > /dev/null 2>&1; do
    if [ "$ecoule" -ge "$ATTENTE_MAX" ]; then
        echo "❌ L'API Go ne répond toujours pas après ${ATTENTE_MAX}s. Logs :" >&2
        docker compose logs --tail=50 backend
        exit 1
    fi
    sleep 3
    ecoule=$((ecoule + 3))
done
echo "✅ API Go opérationnelle."

until curl -sf "http://localhost:${HTTP_PORT}/" > /dev/null 2>&1; do
    if [ "$ecoule" -ge "$ATTENTE_MAX" ]; then
        echo "❌ Le frontend ne répond toujours pas après ${ATTENTE_MAX}s. Logs :" >&2
        docker compose logs --tail=50 frontend
        exit 1
    fi
    sleep 3
    ecoule=$((ecoule + 3))
done
echo "✅ Frontend opérationnel."

echo ""
echo "🎉 Déploiement terminé."
echo "   Site :  http://localhost:${HTTP_PORT}/"
echo "   API  :  http://localhost:${API_PORT}/api/health"
echo ""
echo "Pour arrêter : docker compose down"
echo "Pour tout réinitialiser (y compris la base de données) : docker compose down -v"
