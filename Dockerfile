FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo pdo_pgsql

# MATIIN semua MPM + bersihin total
RUN a2dismod mpm_event mpm_worker mpm_prefork || true \
    && rm -rf /etc/apache2/mods-enabled/mpm_* \
    && rm -rf /etc/apache2/mods-available/mpm_event.* \
    && rm -rf /etc/apache2/mods-available/mpm_worker.*

# NYALAIN cuma prefork
RUN a2enmod mpm_prefork

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80