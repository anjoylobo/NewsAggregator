# Use the official PHP image with FPM
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    zip unzip curl git libpng-dev libonig-dev libxml2-dev cron \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Set permissions for storage and cache
RUN chmod -R 775 storage bootstrap/cache

# Set the user to avoid permission issues
RUN chown -R www-data:www-data /var/www/html

# Add crontab configuration
COPY ./crontab /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron && crontab /etc/cron.d/laravel-cron

# Ensure cron logs are written to stdout/stderr
RUN touch /var/log/cron.log

# Expose port 9000 and start both cron and PHP-FPM
EXPOSE 9000
CMD ["sh", "-c", "cron && php-fpm"]
