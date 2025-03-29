FROM debian:latest

# Install dependencies
RUN apt update && apt install -y \
    apache2 php php-cli ffmpeg python3 python3-venv curl wget unzip \
    && apt clean

# Set up yt-dlp in a virtual environment
RUN python3 -m venv /opt/venv \
    && /opt/venv/bin/pip install --no-cache-dir yt-dlp flask

# Create necessary directories
RUN mkdir -p /mnt/data/yt-dlp-cache /mnt/data/cache /var/www/html

# Copy website files
COPY . /var/www/html
WORKDIR /var/www/html

# Set correct permissions
RUN chmod -R 777 /mnt/data /var/www/html

# Expose Apache and yt-dlp API ports
EXPOSE 80 9080

# Start Apache and yt-dlp API on container boot
CMD service apache2 start && \
    /opt/venv/bin/python3 -m flask --app /opt/venv/bin/yt-dlp run --host=0.0.0.0 --port=9080