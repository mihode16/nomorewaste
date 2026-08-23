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

Le script construit les images, démarre les conteneurs (base de données, API, frontend,
nginx), attend qu'ils répondent, puis affiche les URLs d'accès :

- Site : http://localhost/
- API : http://localhost:8081/api/health

La base de données est initialisée automatiquement au premier démarrage à partir de
`scripts/init_db.sql` (schéma + données de démonstration).

## HTTPS (nom de domaine réel)

`docker/nginx/nginx.conf` fait de nginx le point d'entrée public (HTTP + HTTPS), qui redirige
vers le conteneur `frontend` en interne. Le certificat n'existant pas au tout premier
déploiement, il faut l'obtenir une fois avant que la configuration HTTPS puisse démarrer :

1. Remplacer temporairement `docker/nginx/nginx.conf` par une version sans bloc HTTPS
   (juste la redirection du challenge ACME + le proxy vers `frontend`), et lancer
   `docker compose up -d`.
2. Installer certbot sur la machine hôte (`apt-get install certbot`) et obtenir le
   certificat :
   ```bash
   mkdir -p certbot-webroot
   certbot certonly --webroot -w ./certbot-webroot -d votredomaine.fr -d www.votredomaine.fr
   ```
3. Remettre en place le `nginx.conf` définitif (avec le bloc HTTPS, déjà commité dans le
   dépôt) et relancer : `docker compose up -d --build nginx`.
4. Mettre à jour `SITE_URL=https://votredomaine.fr` dans `.env`, puis
   `docker compose up -d backend`.

Le paquet `certbot` installe automatiquement un renouvellement planifié (`certbot.timer`).
Comme nginx tourne en conteneur et lit les certificats depuis l'hôte, il faut recharger sa
configuration après chaque renouvellement — un hook s'en charge :
```bash
mkdir -p /etc/letsencrypt/renewal-hooks/deploy
cat > /etc/letsencrypt/renewal-hooks/deploy/reload-nginx.sh <<'EOF'
#!/bin/sh
docker exec nomorewaste_nginx nginx -s reload
EOF
chmod +x /etc/letsencrypt/renewal-hooks/deploy/reload-nginx.sh
```

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
