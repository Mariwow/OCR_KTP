# Gunakan image PHP resmi dengan FPM
FROM php:8.2-fpm

# Instal library sistem (Wajib untuk ImageMagick, Tesseract, dan ZIP)
RUN apt-get update && apt-get install -y \
    libmagickwand-dev \
    tesseract-ocr \
    tesseract-ocr-ind \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    --no-install-recommends && rm -rf /var/lib/apt/lists/*

# Instal ekstensi PHP (Sekarang termasuk ZIP)
RUN pecl install imagick && docker-php-ext-enable imagick
RUN docker-php-ext-install pdo_mysql gd zip

# Instal Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy seluruh file proyek
COPY . /app

# Beri izin akses untuk folder storage agar Composer bisa menulis cache jika perlu
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Instal library Laravel
RUN composer install --no-dev --optimize-autoloader

# Jalankan Laravel menggunakan port dinamis dari Railway
CMD php artisan serve --host=0.0.0.0 --port=$PORT
