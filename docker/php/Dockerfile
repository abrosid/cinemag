FROM php:8.4-fpm

# Install necessary packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    zip unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql intl opcache

# Install Xdebug
RUN pecl install xdebug
#    && docker-php-ext-enable xdebug

COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN mkdir -p /var/log && touch /var/log/xdebug.log && chmod 666 /var/log/xdebug.log

WORKDIR /var/www/html

# install dependencies
COPY composer.json /var/www/html/composer.json

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

