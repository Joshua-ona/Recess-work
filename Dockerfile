FROM php:8.4-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    python3 \
    python3-pip \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip


# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs


# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


COPY . .


# Laravel folders
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache


RUN chmod -R 775 storage bootstrap/cache


# PHP dependencies
RUN composer install --no-dev --optimize-autoloader


# Python recommendation engine dependencies
RUN pip3 install \
    --break-system-packages \
    -r app/recommendation_engine/deployment/requirements.txt


# Frontend
RUN npm install
RUN npm run build


RUN chmod +x docker/start.sh


EXPOSE 8000


CMD ["sh", "docker/start.sh"]