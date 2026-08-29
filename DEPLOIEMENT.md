# Déploiement (Docker)

Le site (API Go + frontend PHP + base MySQL) se déploie en une seule commande, sans rien
installer d'autre que Docker.

## Prérequis

- [Docker](https://docs.docker.com/get-docker/) et Docker Compose

## Déploiement

```bash
cp .env.example .env
# éditer .env si besoin (mots de passe, port d'écoute...)

./deploy.sh          # Linux/Mac
# ou
.\deploy.ps1          # Windows PowerShell
```

Le script construit les images, démarre les conteneurs (base de données, API, frontend,
nginx), attend qu'ils répondent, puis affiche les URLs d'accès :

- Site : http://localhost/
- API : http://localhost:8081/api/health

La base de données est initialisée automatiquement au premier démarrage à partir de
`scripts/init_db.sql` (schéma + données de démonstration).

## Commandes utiles

```bash
docker compose logs -f backend    # logs de l'API
docker compose logs -f frontend   # logs Apache/PHP
docker compose down               # arrêter (les données sont conservées)
docker compose down -v            # arrêter et tout réinitialiser (y compris la base)
```
