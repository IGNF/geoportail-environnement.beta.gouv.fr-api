#!/bin/sh
set -e


# Vérifier l'état des migrations avant de les appliquer
if php bin/console doctrine:migrations:up-to-date -e prod; then
    echo "Toutes les migrations sont déjà appliquées."
else
    echo "Application des migrations..."
    php bin/console doctrine:migrations:migrate --no-interaction  -e prod
fi

php bin/console lexik:jwt:generate-keypair --overwrite

# Lancer PHP-FPM en mode foreground
exec php-fpm -F --pid /opt/bitnami/php/tmp/php-fpm.pid -y /opt/bitnami/php/etc/php-fpm.conf
