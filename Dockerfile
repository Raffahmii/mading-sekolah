FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo pdo_pgsql

# FORCE FIX MPM
RUN a2dismod mpm_event
RUN a2dismod mpm_worker || true
RUN a2enmod mpm_prefork

COPY . /var/www/html/

EXPOSE 80