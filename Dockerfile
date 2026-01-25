# --------------------------
# Stage 1: Composer deps
# --------------------------
FROM composer:2 AS vendor

WORKDIR /app

RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    $PHPIZE_DEPS \
 && docker-php-ext-install intl zip mbstring

COPY composer.json composer.lock ./

RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .

RUN composer dump-autoload --no-dev --optimize


# --------------------------
# Stage 2: PHP Runtime
# --------------------------
FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

# System deps
RUN apk add --no-cache \
    bash curl git \
    icu-dev oniguruma-dev libzip-dev \
    freetype-dev libjpeg-turbo-dev libpng-dev

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    intl \
    zip \
    gd \
    opcache

# Redis extension
RUN apk add --no-cache $PHPIZE_DEPS \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && apk del $PHPIZE_DEPS

# Copy app
COPY --from=vendor /app /var/www/html

# Copy PHP config
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy entrypoint (as root)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Permissions (as root)
RUN chmod +x /usr/local/bin/entrypoint.sh \
 && chown -R www-data:www-data /var/www/html \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Switch to non-root user
USER www-data

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
