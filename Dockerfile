# Use the official PHP image with Apache
FROM php:8.1-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy PHP files into the Apache server's root
COPY . /var/www/html/

# Install MySQLi extension
RUN docker-php-ext-install mysqli
