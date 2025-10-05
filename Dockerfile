# Use official PHP image with Apache
FROM php:8.2-apache

# Enable file uploads and set max upload size
RUN docker-php-ext-install mysqli
RUN echo "upload_max_filesize = 5M\npost_max_size = 5M" > /usr/local/etc/php/conf.d/uploads.ini

# Copy all project files to the Apache server directory
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80
