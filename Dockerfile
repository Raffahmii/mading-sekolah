FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo pdo_pgsql

# FORCE Apache pakai prefork doang (override config)
RUN echo "LoadModule mpm_prefork_module /usr/lib/apache2/modules/mod_mpm_prefork.so" > /etc/apache2/mods-enabled/mpm_prefork.load \
 && rm -f /etc/apache2/mods-enabled/mpm_event.load \
 && rm -f /etc/apache2/mods-enabled/mpm_worker.load

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80