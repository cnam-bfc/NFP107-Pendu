FROM php:8.2.7-apache

# Install PDO MySQL driver (optional)
RUN docker-php-ext-install pdo_mysql

# Enable mod rewrite (optional)
RUN a2enmod rewrite