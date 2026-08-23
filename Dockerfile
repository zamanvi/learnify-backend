FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    curl \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mbstring xml zip gd bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN cp .env.example .env && \
    sed -i 's|APP_KEY=|APP_KEY=base64:EjIyrSONiaLkRS8kEe3fcNpTYkCDnYZSel6rtu5rH1w=|' .env

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8080

# php -S is single-threaded by default, so it was serializing the live app's
# constant API traffic behind whatever the admin dashboard was doing (and vice
# versa) - slow/queued responses were the real cause of admin users
# double-clicking Save/Delete, which then hit already-processed records and
# surfaced as intermittent 404s. PHP_CLI_SERVER_WORKERS (native since PHP 7.4,
# Linux only - fine here) forks worker processes instead of handling one
# request at a time, with no change to the command or serving stack itself.
ENV PHP_CLI_SERVER_WORKERS=4

CMD php artisan migrate --force && php -S 0.0.0.0:${PORT:-8080} -t public
