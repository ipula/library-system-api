#!/bin/sh
set -e

echo "Starting Laravel..."

# Fix permissions (DO NOT touch .env)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Clear & cache config
php artisan config:clear
php artisan config:cache

echo "Laravel ready."

exec php-fpm
