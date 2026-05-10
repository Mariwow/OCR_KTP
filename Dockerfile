# ... (bagian atas tetap sama)

# Copy seluruh file proyek ke folder /app
COPY . /app

# Pindah ke folder /app
WORKDIR /app

# Pastikan file artisan ada dan bisa dieksekusi
RUN chmod +x artisan

# Instal composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Perintah menjalankan aplikasi (Gunakan port dinamis Railway)
CMD php artisan serve --host=0.0.0.0 --port=$PORT
