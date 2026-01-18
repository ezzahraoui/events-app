FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Install msmtp for mail delivery
RUN apt-get update && apt-get install -y msmtp && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Configure msmtp to relay to MailHog
RUN echo "account default" > /etc/msmtprc && \
    echo "host events-app-mailhog" >> /etc/msmtprc && \
    echo "port 1025" >> /etc/msmtprc && \
    echo "from noreply@events.com" >> /etc/msmtprc && \
    chmod 644 /etc/msmtprc

# Copy custom PHP configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy Apache config for pretty URLs
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf