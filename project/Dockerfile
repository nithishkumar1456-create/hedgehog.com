FROM php:8.2-apache

# Install MySQL
RUN docker-php-ext-install mysqli

# Install MongoDB + Redis
RUN pecl install mongodb redis \
    && docker-php-ext-enable mongodb redis

# Fix Apache warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Allow access (important)
RUN echo "<Directory /var/www/html/> \
    Options Indexes FollowSymLinks \
    AllowOverride All \
    Require all granted \
</Directory>" >> /etc/apache2/apache2.conf

# ✅ FIXED COPY
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80