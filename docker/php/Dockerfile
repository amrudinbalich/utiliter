FROM php:8.5-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libxml2-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    simplexml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json ./
RUN composer install --no-dev --optimize-autoloader

COPY . .