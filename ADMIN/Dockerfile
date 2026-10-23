FROM php:8.2-apache

RUN apt-get update && apt-get install -y libmariadb-dev

COPY . /var/www/html/

RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

RUN docker-php-ext-install mysqli pdo pdo_mysql