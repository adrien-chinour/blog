FROM php:8.2-apache AS app

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd zip opcache intl
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2enmod headers
COPY docker/apache2/default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache2/mods-enabled/headers.conf /etc/apache2/mods-enabled/headers.conf

WORKDIR /var/www
