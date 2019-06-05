FROM composer:1.8.5 AS composer

COPY composer.json \
    composer.lock \
    symfony.lock \
    ./

RUN composer --no-interaction install --ignore-platform-reqs --no-autoloader --no-suggest --prefer-dist

COPY src/ src/

RUN composer --no-interaction dump-autoload --optimize



FROM php:7.3.6-fpm-alpine AS app

WORKDIR /app

ENV APP_ENV=dev

RUN mkdir var && \
    chown www-data:www-data var

RUN docker-php-ext-install opcache

COPY . .
COPY --from=composer /app/vendor/ vendor/

RUN bin/console assets:install && \
    rm -rf var/*

USER www-data
