FROM php:8.2-apache as app

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd zip opcache intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/apache2/default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www
