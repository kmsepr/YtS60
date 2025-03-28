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

# Change Apache to listen on port 8000 (required by Koyeb)
RUN sed -i 's/Listen 80/Listen 8000/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:8000>/' /etc/apache2/sites-enabled/000-default.conf

# Set the working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Expose the correct port
EXPOSE 8000

# Start Apache in foreground
CMD ["apache2-foreground"]