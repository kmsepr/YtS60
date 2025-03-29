FROM php:apache

# Install dependencies
RUN apt-get update \
    && apt-get install -y ffmpeg curl python3 python3-pip python3-venv \
    && python3 -m venv /opt/venv \
    && /opt/venv/bin/pip install --no-cache-dir yt-dlp \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set the cache directory for yt-dlp
ENV YTDLP_CACHE_DIR=/tmp/yt-dlp-cache

# Ensure Apache uses this environment variable
RUN echo 'export YTDLP_CACHE_DIR=/tmp/yt-dlp-cache' >> /etc/apache2/envvars

# Create the cache directory and set proper permissions
RUN mkdir -p /tmp/yt-dlp-cache && chmod -R 777 /tmp/yt-dlp-cache

# Add yt-dlp to PATH
ENV PATH="/opt/venv/bin:$PATH"

# Copy project files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose Apache port
EXPOSE 80