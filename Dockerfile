# Use official PHP-Apache image
FROM php:8.2-apache

# Install required dependencies
RUN apt-get update && apt-get install -y \
    ffmpeg \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Fix Apache's ServerName issue
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html/

# Ensure .htaccess exists
RUN echo "Options +Indexes\nRewriteEngine On\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteRule . /index.php [L]" > /var/www/html/.htaccess

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html && chmod -R 777 /var/www/html

# Ensure streams and tmp directories exist
RUN mkdir -p /var/www/html/streams /tmp && chmod -R 777 /var/www/html/streams /tmp

# Expose Apache on port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]