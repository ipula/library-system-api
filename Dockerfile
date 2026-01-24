# --------------------------
# Stage 1: Composer deps
# --------------------------
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

COPY . .
RUN composer dump-autoload --no-dev --optimize


# --------------------------
# Stage 2: PHP Runtime
# --------------------------
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# System deps
RUN apk add --no-cache \
    bash curl git supervisor \
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

# Redis extension (optional but recommended)
RUN apk add --no-cache $PHPIZE_DEPS \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && apk del $PHPIZE_DEPS

# Copy app from build stage
COPY --from=vendor /app /var/www/html

# Permissions
RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# PHP config
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
