FROM php:8.4-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    libpq-dev \
    curl \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

RUN chmod -R 775 storage bootstrap/cache

RUN composer install --no-dev --optimize-autoloader

RUN npm install
RUN npm run build

RUN chmod +x docker/start.sh

EXPOSE 8000

CMD ["sh", "docker/start.sh"]