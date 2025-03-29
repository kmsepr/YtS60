FROM debian:latest

# Install dependencies
RUN apt-get update && apt-get install -y \
    apache2 php wget curl ffmpeg python3 \
    && apt-get clean

# Install yt-dlp
RUN wget -q https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -O /usr/local/bin/yt-dlp && chmod a+rx /usr/local/bin/yt-dlp

# Enable Apache modules
RUN a2enmod rewrite && a2enmod headers

# Set up web directory
COPY . /var/www/html/
WORKDIR /var/www/html/

# Expose ports (Apache and RTSP)
EXPOSE 80 554 443 8080 8554

# Start Apache
CMD ["apachectl", "-D", "FOREGROUND"]