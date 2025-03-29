FROM php:apache

# Install dependencies
RUN apt-get update \
    && apt-get install -y ffmpeg curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy project files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose Apache port
EXPOSE 80