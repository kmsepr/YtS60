FROM debian:latest

# Install dependencies
RUN apt update && apt install -y \
    apache2 php libapache2-mod-php php-cli ffmpeg python3 python3-venv curl wget unzip \
    && apt clean

# Enable necessary Apache modules
RUN a2enmod rewrite

# Set up yt-dlp in a virtual environment
RUN python3 -m venv /opt/venv \
    && /opt/venv/bin/pip install --upgrade pip \
    && /opt/venv/bin/pip install --no-cache-dir yt-dlp

# Create necessary directories
RUN mkdir -p /mnt/data/yt-dlp-cache /mnt/data/cache /var/www/html

# Copy website files
COPY . /var/www/html
WORKDIR /var/www/html

# Set correct permissions for Apache
RUN chown -R www-data:www-data /var/www/html /mnt/data \
    && chmod -R 755 /var/www/html /mnt/data

# Expose only port 80 (Koyeb limitation)
EXPOSE 80

# Start Apache on container boot
CMD ["apachectl", "-D", "FOREGROUND"]
