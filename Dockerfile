# Use an official PHP-Apache image
FROM php:8.2-apache

# Install required dependencies
RUN apt-get update && apt-get install -y \
    ffmpeg \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Fix Apache's ServerName issue
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Ensure required directories exist
RUN mkdir -p /var/www/html/videos /tmp && chmod -R 777 /var/www/html/videos /tmp

# Copy project files
COPY . /var/www/html/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Expose Apache on port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]