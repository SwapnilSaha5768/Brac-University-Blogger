FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Set working directory to default web root
WORKDIR /var/www/html

# Copy the Frontend directory contents to the web root
COPY Frontend/ .

# Expose port 80
EXPOSE 80
