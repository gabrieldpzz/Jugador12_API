FROM php:8.3-fpm

# Dependencias y extensiones para Laravel + PostgreSQL
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev libpng-dev \
 && docker-php-ext-install pdo pdo_pgsql zip gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
