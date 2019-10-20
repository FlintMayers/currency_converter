FROM php:apache

RUN apt-get update && apt-get install -y \
unzip \
zip

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

COPY composer.json /var/www/html

COPY composer.lock /var/www/html

WORKDIR /var/www/html

RUN composer install --no-scripts --no-autoloader

COPY . /var/www/html
