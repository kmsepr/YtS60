# Use an official PHP-Apache image
FROM php:8.2-apache

# Install required dependencies
RUN apt-get update && apt-get install -y \
    ffmpeg \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install yt-dlp (to download YouTube videos)
RUN curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -o /usr/local/bin/yt-dlp \
    && chmod a+rx /usr/local/bin/yt-dlp

# Fix Apache's ServerName issue
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Create directory for HLS streams
RUN mkdir -p /var/www/html/streams && chmod 777 /var/www/html/streams

# Expose only HTTP (port 80)
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]