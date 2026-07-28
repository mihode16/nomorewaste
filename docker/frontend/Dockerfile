FROM php:8.2-apache

# Installer les extensions
RUN docker-php-ext-install pdo_mysql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Activer mod_rewrite
RUN a2enmod rewrite

# Copier les fichiers
COPY frontend/ /var/www/html/

# Installer les dépendances PHP
WORKDIR /var/www/html
RUN composer install --no-interaction || true

# Configurer le DocumentRoot
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Exposer le port
EXPOSE 80

CMD ["apache2-foreground"]