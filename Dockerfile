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

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

COPY .docker/apache.conf /etc/apache2/sites-available/000-default.conf

ENV PORT=80

EXPOSE 80

CMD php artisan migrate --force; apache2-foreground
