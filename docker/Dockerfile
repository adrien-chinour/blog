FROM php:8.2-fpm-alpine AS app

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd zip opcache intl
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
