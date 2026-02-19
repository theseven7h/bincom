#!/bin/sh
set -e

cd /var/www/html

echo "⏳ Setting up Laravel..."

# Copy .env if missing
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Inject DB env vars into .env
sed -i "s|DB_HOST=.*|DB_HOST=${DB_HOST}|" .env
sed -i "s|DB_PORT=.*|DB_PORT=${DB_PORT}|" .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}|" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" .env
sed -i "s|APP_ENV=.*|APP_ENV=${APP_ENV}|" .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=${APP_DEBUG}|" .env
sed -i "s|APP_URL=.*|APP_URL=${APP_URL}|" .env

# Generate app key if not set
php artisan key:generate --no-interaction --force

# Clear caches (important for volume mounts)
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Fix storage permissions
chmod -R 777 storage bootstrap/cache

echo "✅ Laravel ready. Starting server at http://localhost:8000"

exec php artisan serve --host=0.0.0.0 --port=8000
