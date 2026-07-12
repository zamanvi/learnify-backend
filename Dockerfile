FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    curl \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mbstring xml zip gd bcmath \
    && a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN cp .env.example .env && \
    sed -i 's|APP_KEY=|APP_KEY=base64:EjIyrSONiaLkRS8kEe3fcNpTYkCDnYZSel6rtu5rH1w=|' .env

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Point Apache's DocumentRoot at Laravel's public/ directory and allow
# .htaccess to actually take effect (Laravel's public/.htaccess needs
# mod_rewrite + AllowOverride All to route everything through index.php).
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && printf '<Directory /var/www/html/public>\n    AllowOverride All\n</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

EXPOSE 8080

# Railway assigns $PORT at runtime, so the listen port has to be patched
# in at container start rather than baked in at build time.
CMD php artisan migrate --force; \
    sed -i "s/Listen 80/Listen ${PORT:-8080}/" /etc/apache2/ports.conf; \
    sed -i "s/:80>/:${PORT:-8080}>/" /etc/apache2/sites-available/000-default.conf; \
    apache2-foreground
