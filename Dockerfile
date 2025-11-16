FROM php:8.2-apache

# Installer l'extension PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Activer mod_rewrite pour l'URL rewriting (utile avec public/.htaccess)
RUN a2enmod rewrite

# Dossier de travail
WORKDIR /var/www/html

# Copier le code de l'application dans le conteneur
COPY . /var/www/html

# Faire pointer le DocumentRoot vers /public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80

# En prod
ENV APP_ENV=production