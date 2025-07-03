# Use the official PHP image with Apache, version 8.2
FROM php:8.2-apache

# Set the working directory inside the container
WORKDIR /var/www/html

# Install system dependencies for PHP extensions and Composer
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    pkg-config \
    zip \
    unzip \
    git \
    libonig-dev \
    libzip-dev

# Install PHP extensions required for the application
RUN docker-php-ext-install \
    mysqli \
    pdo_mysql \
    curl \
    mbstring \
    opcache

# Enable Apache modules
RUN a2enmod rewrite headers

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy apache configuration
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Copy composer files first to leverage Docker cache
COPY composer.json composer.lock* ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader --ignore-platform-reqs

# Copy the rest of the application
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize

# Set correct permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# Enable error logging temporarily for debugging
RUN sed -i 's/error_reporting = .*/error_reporting = E_ALL/' /usr/local/etc/php/php.ini-production \
    && sed -i 's/display_errors = .*/display_errors = On/' /usr/local/etc/php/php.ini-production \
    && cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Expose port 80
EXPOSE 80

# Apache is started automatically by the base image
CMD ["apache2-foreground"]