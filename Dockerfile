FROM php:apache

# Install dependencies
RUN apt-get update \
    && apt-get install -y ffmpeg curl python3 python3-pip \
    && pip3 install --no-cache-dir yt-dlp \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy project files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose Apache port
EXPOSE 80