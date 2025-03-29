FROM php:apache

# Install Dependencies
RUN apt-get update && apt-get install -y \
    curl ffmpeg yt-dlp \
    && apt-get clean

# Set Workdir
WORKDIR /var/www/html

# Copy App Files
COPY . /var/www/html/

# Expose Apache Port
EXPOSE 8080