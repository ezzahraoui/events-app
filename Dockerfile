FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy Apache config for pretty URLs
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf