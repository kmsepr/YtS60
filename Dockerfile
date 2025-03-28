# Use the official PHP-Apache image
FROM php:8.2-apache

# Install dependencies
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

# Set the working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html/

# Set correct permissions
RUN mkdir -p /var/www/html/streams /tmp && chmod -R 777 /var/www/html/streams /tmp

# Expose Apache on port 80 (Koyeb only supports HTTP)
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]