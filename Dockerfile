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
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .

RUN composer dump-autoload --no-dev --optimize


# --------------------------
# Stage 2: PHP Runtime
# --------------------------
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    bash curl git \
    icu-dev oniguruma-dev libzip-dev \
    freetype-dev libjpeg-turbo-dev libpng-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    intl \
    zip \
    gd \
    opcache

RUN apk add --no-cache $PHPIZE_DEPS \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && apk del $PHPIZE_DEPS

COPY --from=vendor /app /var/www/html

RUN chown -R www-data:www-data \
    storage bootstrap/cache

COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
