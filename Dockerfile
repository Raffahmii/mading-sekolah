FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo pdo_pgsql

# FIX ERROR MPM
RUN a2dismod mpm_event && a2enmod mpm_prefork

COPY . /var/www/html/

EXPOSE 80