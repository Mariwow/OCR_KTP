# Gunakan image PHP resmi dengan FPM
FROM php:8.2-fpm

# Instal library sistem (Wajib untuk ImageMagick & Tesseract)
RUN apt-get update && apt-get install -y \
    libmagickwand-dev \
    tesseract-ocr \
    tesseract-ocr-ind \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    --no-install-recommends && rm -rf /var/lib/apt/lists/*

# Instal ekstensi PHP
RUN pecl install imagick && docker-php-ext-enable imagick
RUN docker-php-ext-install pdo_mysql gd

# Instal Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy seluruh file proyek ke dalam folder /app di server
COPY . /app

# Jalankan instalasi library Laravel
RUN composer install --no-dev --optimize-autoloader

# Beri izin akses untuk folder storage (Penting untuk upload KTP)
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Jalankan Laravel menggunakan port dinamis dari Railway
CMD php artisan serve --host=0.0.0.0 --port=$PORT
