# Image du frontend PHP (Apache + mod_php), servant frontend/public comme racine web et
# appelant l'API Go du conteneur "backend" via API_URL.

FROM php:8.3-apache

# ext-curl est indispensable : ClientApi.php parle à l'API Go exclusivement via curl_*().
RUN apt-get update \
    && apt-get install -y --no-install-recommends libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite

COPY frontend/ /var/www/html/

# DocumentRoot pointé sur public/, avec AllowOverride All pour que le .htaccess (réécriture
# d'URL vers index.php) soit pris en compte.
RUN { \
        echo '<VirtualHost *:80>'; \
        echo '    DocumentRoot /var/www/html/public'; \
        echo '    <Directory /var/www/html/public>'; \
        echo '        AllowOverride All'; \
        echo '        Require all granted'; \
        echo '    </Directory>'; \
        echo '    ErrorLog ${APACHE_LOG_DIR}/error.log'; \
        echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined'; \
        echo '</VirtualHost>'; \
    } > /etc/apache2/sites-available/000-default.conf

# Le site est servi à la racine du domaine en conteneur : pas de préfixe de chemin nécessaire.
RUN printf '%s\n' \
        'RewriteEngine On' \
        'RewriteCond %{REQUEST_FILENAME} !-f' \
        'RewriteCond %{REQUEST_FILENAME} !-d' \
        'RewriteRule ^ index.php [QSA,L]' \
        > /var/www/html/public/.htaccess

EXPOSE 80

CMD ["apache2-foreground"]
