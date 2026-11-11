#!/bin/sh
# Container start-up: listen on the host's port, prepare the SQLite database, then start Apache.
set -e
cd /var/www/html

# Render (and similar hosts) tell the app which port to use in $PORT.
PORT="${PORT:-80}"
sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# .env with an app key (the key is generated on the server, never stored in Git).
if [ ! -f .env ]; then
    cp .env.example .env
fi
if [ -z "$APP_KEY" ] && ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --force
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs

# First start on a new server: create the database, tables and sample data.
FRESH=0
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
    FRESH=1
fi
php artisan migrate --force
if [ "$FRESH" = "1" ]; then
    php artisan db:seed --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

chown -R www-data:www-data storage bootstrap/cache database
exec apache2-foreground
