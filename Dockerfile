# Use official PHP with Apache
FROM php:8.2-apache

# Install required dependencies
RUN apt-get update && apt-get install -y \
    ffmpeg \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install yt-dlp (ensure it's accessible for www-data)
RUN curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -o /usr/local/bin/yt-dlp \
    && chmod a+rx /usr/local/bin/yt-dlp \
    && mkdir -p /var/www/.cache/yt-dlp \
    && chown -R www-data:www-data /var/www/.cache

# Set up Apache with WebSocket support
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite proxy proxy_http proxy_wstunnel

# Copy website files
COPY . /var/www/html/
WORKDIR /var/www/html

# Set permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]