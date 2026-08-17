# Déploiement (Docker)

Le site (API Go + frontend PHP + base MySQL) se déploie en une seule commande, sans rien
installer d'autre que Docker.

## Prérequis

- [Docker](https://docs.docker.com/get-docker/) et Docker Compose (inclus dans Docker Desktop)

## Déploiement

```bash
cp .env.example .env
# éditer .env si besoin (mots de passe, port d'écoute, identifiants SMTP...)

./deploy.sh          # Linux/Mac
# ou
.\deploy.ps1          # Windows PowerShell
```

Le script construit les images, démarre les 3 conteneurs (base de données, API, frontend),
attend qu'ils répondent, puis affiche les URLs d'accès :

- Site : http://localhost:8080/
- API : http://localhost:8081/api/health

La base de données est initialisée automatiquement au premier démarrage à partir de
`scripts/init_db.sql` (schéma + données de démonstration).

## Emails (rappels de renouvellement + plannings bénévoles)

Le backend envoie automatiquement, une fois par jour (heure configurable via
`SCHEDULER_HEURE` dans `.env`) :
- un email de rappel aux commerçants/adhérents dont l'adhésion expire sous un mois ;
- le planning du jour (fichier Excel en pièce jointe) à chaque bénévole ayant une affectation
  ce jour-là.

Pour activer l'envoi réel (sinon les emails sont simplement journalisés, sans erreur) :

1. Sur le compte Gmail à utiliser pour l'envoi, activer la validation en 2 étapes.
2. Générer un mot de passe d'application : https://myaccount.google.com/apppasswords
3. Renseigner dans `.env` :
   ```
   SMTP_USER=votre-adresse@gmail.com
   SMTP_PASSWORD=le-mot-de-passe-dapplication-genere
   ```
4. Redémarrer : `docker compose up -d --build`

Depuis le tableau de bord admin (`/admin/dashboard`), le bouton **"Exécuter les tâches
quotidiennes maintenant"** permet de déclencher rappels + plannings immédiatement, sans
attendre l'heure planifiée (pratique pour une démonstration).

## Commandes utiles

```bash
docker compose logs -f backend    # logs de l'API (voir les tâches quotidiennes s'exécuter)
docker compose logs -f frontend   # logs Apache/PHP
docker compose down               # arrêter (les données sont conservées)
docker compose down -v            # arrêter et tout réinitialiser (y compris la base)
```
