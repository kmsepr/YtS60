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

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Expose Apache on port 80 (since Koyeb does not support RTSP)
EXPOSE 80

# Start Apache and FFmpeg in parallel
CMD service apache2 start && \
    ffmpeg -rtsp_transport tcp -i "rtsp://your-stream-url" -f mjpeg http://localhost/stream.mjpeg