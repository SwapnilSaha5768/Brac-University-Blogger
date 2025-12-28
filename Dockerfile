FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Set working directory to default web root
WORKDIR /var/www/html

# Copy the Frontend directory contents from the subdirectory to the web root
COPY "Brac University Blogger/Frontend/" .

# Expose port 80
EXPOSE 80
