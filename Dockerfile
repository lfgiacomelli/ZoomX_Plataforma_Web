FROM php:8.1-apache

COPY . /var/www/html/

RUN a2enmod rewrite

EXPOSE 80
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql

