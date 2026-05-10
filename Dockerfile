FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libmagickwand-dev \
    tesseract-ocr \
    tesseract-ocr-ind \
    --no-install-recommends && rm -rf /var/lib/apt/lists/*

RUN pecl install imagick && docker-php-ext-enable imagick