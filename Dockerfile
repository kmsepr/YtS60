# Use PHP-Apache Base Image
FROM php:8.2-apache

# Install Dependencies
RUN apt-get update && apt-get install -y \
    ffmpeg \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install yt-dlp
RUN curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -o /usr/local/bin/yt-dlp \
    && chmod a+rx /usr/local/bin/yt-dlp

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Set Working Directory
WORKDIR /var/www/html

# Copy Project Files
COPY . /var/www/html/

# Set Correct Permissions
RUN chmod -R 777 /var/www/html

# Expose Apache on Port 8080
EXPOSE 8080

# Start Apache
CMD ["apache2-foreground"]