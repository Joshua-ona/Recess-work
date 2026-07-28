FROM php:8.4-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    libpq-dev \
    curl \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip


# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .


RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache


RUN chmod -R 775 storage bootstrap/cache


# PHP dependencies
RUN composer install --no-dev --optimize-autoloader


# Frontend dependencies + build
RUN npm install
RUN npm run build


RUN php artisan storage:link || true


EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=$PORT