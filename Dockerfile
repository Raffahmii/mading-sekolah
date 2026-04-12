FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo pdo_pgsql

# HAPUS SEMUA MPM dulu (ini kuncinya)
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
           /etc/apache2/mods-enabled/mpm_*.conf

# Aktifin cuma prefork
RUN a2enmod mpm_prefork

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80