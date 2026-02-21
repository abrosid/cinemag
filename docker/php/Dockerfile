FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    zip unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql intl opcache

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN mkdir -p /var/log && touch /var/log/xdebug.log && chmod 666 /var/log/xdebug.log

WORKDIR /var/www/html

COPY composer.json /var/www/html/composer.json
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

