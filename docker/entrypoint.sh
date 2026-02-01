#!/bin/sh
set -e

echo "Starting Laravel..."

cd /var/www/html

# Ensure storage paths exist
mkdir -p /var/www/html/storage/framework/cache \
  /var/www/html/storage/framework/sessions \
  /var/www/html/storage/framework/views \
  /var/www/html/bootstrap/cache 2>/dev/null || true

# Fix permissions (DO NOT touch .env)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Clear & cache config
php artisan config:clear
php artisan config:cache

echo "Laravel ready."

exec php-fpm
