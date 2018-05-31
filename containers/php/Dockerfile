FROM php:7-fpm

RUN apt-get update
RUN docker-php-ext-install pdo_mysql mbstring
COPY ./code /var/www/html