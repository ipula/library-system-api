#!/bin/sh
set -e

echo "Starting Laravel container..."

# Ensure permissions
chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create .env if missing
if [ ! -f /var/www/html/.env ]; then
  echo ".env not found, creating..."
  cp /var/www/html/.env.example /var/www/html/.env
fi

# Generate app key if missing
if ! grep -q "APP_KEY=base64" /var/www/html/.env; then
  echo "Generating APP_KEY..."
  php artisan key:generate --force
fi

# Cache config
php artisan config:clear
php artisan config:cache

echo "Laravel ready."

# Start PHP-FPM
exec php-fpm
