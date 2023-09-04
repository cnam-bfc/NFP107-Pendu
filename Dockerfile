# PHP docker container
FROM php:8.2.10-apache

# Install PDO MySQL driver (optional)
RUN docker-php-ext-install pdo_mysql

# Install PDO Firebird driver (optional)
RUN apt-get update && apt-get install -y firebird-dev && docker-php-ext-install pdo_firebird && apt-get clean

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Application environment variables (optional)
ENV DATABASE_TYPE=mysql
ENV DATABASE_HOST=localhost
ENV DATABASE_PORT=3306
ENV DATABASE_CHARSET=utf8
ENV DATABASE_NAME=database
ENV DATABASE_USER=user
ENV DATABASE_PASSWORD=password
ENV DATABASE_FIREBIRD_ROLE=role

# Copy application files
COPY . /var/www/html/

# Enable mod rewrite (optional)
RUN a2enmod rewrite

# Application port (optional)
EXPOSE 80

# Container start command
CMD ["apache2-foreground"]

