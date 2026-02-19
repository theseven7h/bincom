#!/bin/sh
set -e
cd /var/www/html

# Always write a fresh .env from environment variables
cat > .env << ENVFILE
APP_NAME="Bincom Election Results"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}
DB_CONNECTION=mysql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
SESSION_DRIVER=file
CACHE_STORE=file
LOG_CHANNEL=stderr
ENVFILE

php artisan key:generate --no-interaction --force
php artisan config:clear
php artisan route:clear
php artisan view:clear

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

exec supervisord -c /etc/supervisord.conf
