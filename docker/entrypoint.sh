#!/bin/sh
set -e

echo "Starting Laravel..."

# Fix permissions
chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Cache
php artisan config:clear
php artisan config:cache

exec php-fpm
