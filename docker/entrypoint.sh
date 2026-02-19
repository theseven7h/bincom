#!/bin/sh
set -e

cd /var/www/html

# Copy .env if not exists
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --no-interaction --force
fi

# Clear and cache config/routes/views
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix storage permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Start services via supervisor
exec supervisord -c /etc/supervisord.conf
