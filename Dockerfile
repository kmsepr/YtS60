# Use an official PHP-Apache image
FROM php:8.2-apache

# Install required dependencies
RUN apt-get update && apt-get install -y \
    ffmpeg \
    yt-dlp \
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

# Ensure directories exist with proper permissions
RUN mkdir -p /var/www/html/downloads /var/www/html/streams /tmp && chmod -R 777 /var/www/html/downloads /var/www/html/streams /tmp

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Expose Apache on port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]