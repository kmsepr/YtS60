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

# Copy project files
COPY . /var/www/html/

# Ensure necessary directories exist
RUN mkdir -p /var/www/html/streams /tmp \
    && chmod -R 777 /var/www/html/streams /tmp \
    && chown -R www-data:www-data /var/www/html

# Expose Apache on port 80 (since Koyeb does not support RTSP)
EXPOSE 80

# Start Apache and keep container running
CMD ["apache2-foreground"]