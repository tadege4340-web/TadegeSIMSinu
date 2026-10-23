FROM php:8.2-apache

# Install the necessary system library for MySQL extensions
RUN apt-get update && apt-get install -y libmariadb-dev

# Copy your application files
COPY . /var/www/html/

# Configure Apache to listen on the port Render provides
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Install the PHP MySQL extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql