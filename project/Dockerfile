# Use PHP with Apache
FROM php:8.2-apache

# Install required extensions
RUN docker-php-ext-install mysqli

# Install MongoDB + Redis
RUN pecl install mongodb redis \
    && docker-php-ext-enable mongodb redis

# Enable Apache rewrite
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port
EXPOSE 80