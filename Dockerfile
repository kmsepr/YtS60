FROM php:apache

# Install dependencies
RUN apt-get update \
    && apt-get install -y ffmpeg curl python3 python3-pip python3-venv \
    && python3 -m venv /opt/venv \
    && /opt/venv/bin/pip install --no-cache-dir yt-dlp \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Add yt-dlp to PATH
ENV PATH="/opt/venv/bin:$PATH"

# Copy project files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose Apache port
EXPOSE 80