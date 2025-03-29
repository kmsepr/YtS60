# Base Image
FROM php:8.2-apache

# Install required dependencies
RUN apt-get update && apt-get install -y \
    curl \
    ffmpeg \
    procps \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install yt-dlp
RUN curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -o /usr/local/bin/yt-dlp && \
    chmod a+rx /usr/local/bin/yt-dlp

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Expose HTTP & RTSP ports
EXPOSE 80 554 8080 8554

# Start Apache
CMD ["apache2-foreground"]