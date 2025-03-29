FROM php:apache

# Install dependencies
RUN apt-get update && apt-get install -y ffmpeg curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install yt-dlp for YouTube video downloading
RUN curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -o /usr/local/bin/yt-dlp \
    && chmod a+rx /usr/local/bin/yt-dlp

# Copy project files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose Apache port
EXPOSE 80