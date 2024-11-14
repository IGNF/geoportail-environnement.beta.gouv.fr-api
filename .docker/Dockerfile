# Spécifiez le registre et l'image de base
ARG registry=docker.io
FROM ${registry}/bitnami/php-fpm:8.2

#----------------------------------------------------------------------
# Installation de unzip, git, nano et extension PostgreSQL
#----------------------------------------------------------------------
RUN apt-get update \
    && apt-get install -y unzip git nano \
    && echo "extension=pdo_pgsql.so" >> /opt/bitnami/php/etc/php.ini \
    && rm -rf /var/lib/apt/lists/*

# Changer le répertoire de travail
WORKDIR /app

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier le code Symfony dans le conteneur
COPY . /app
COPY .docker/php-fpm-custom.conf /opt/bitnami/php/etc/php-fpm.d/php-fpm-custom.conf

# Installer les dépendances Composer
RUN COMPOSER_CACHE_READ_ONLY=true composer install --no-interaction


# Exposer le port 9000 pour PHP-FPM
EXPOSE 9000

ENTRYPOINT ["/bin/sh", "/app/.docker/init_db.sh"]