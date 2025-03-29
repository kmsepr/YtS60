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

# Enable mod_rewrite for Apache (for clean URLs)
RUN a2enmod rewrite

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy all project files into the container
COPY . /var/www/html/

# Ensure the streams directory exists and has correct permissions
RUN mkdir -p /var/www/html/streams /tmp && chmod -R 777 /var/www/html/streams /tmp

# Expose port 80 (default HTTP port)
EXPOSE 80

# Start Apache when the container runs
CMD ["apache2-foreground"]