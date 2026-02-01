#!/bin/sh
set -e

echo "Starting Laravel..."

cd /var/www/html

# Ensure storage paths exist before maintenance mode
if [ -w /var/www/html/storage ]; then
	mkdir -p /var/www/html/storage/framework \
		/var/www/html/storage/framework/cache \
		/var/www/html/storage/framework/sessions \
		/var/www/html/storage/framework/views \
		/var/www/html/bootstrap/cache

	# Put app in maintenance mode (ignore if already down)
	php artisan down || true
else
	echo "Warning: /var/www/html/storage is not writable; skipping maintenance mode."
fi

# Fix permissions (DO NOT touch .env)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Clear & cache config
php artisan config:clear
php artisan config:cache

echo "Laravel ready."

exec php-fpm
