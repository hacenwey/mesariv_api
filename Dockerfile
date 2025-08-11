FROM php:8.2-apache

# Install mysqli and PDO MySQL extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy custom php.ini
COPY php.ini /usr/local/etc/php/

# Copy app files
COPY . /var/www/html/

# Enable mod_rewrite if needed
RUN a2enmod rewrite
