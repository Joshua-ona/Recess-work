#!/bin/sh

php artisan migrate --force || true

php artisan storage:link || true

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache


echo "Starting Recommendation Engine..."

cd /var/www/html/app/recommendation_engine

python3 api.py &


echo "Starting Laravel..."

cd /var/www/html

php artisan serve --host=0.0.0.0 --port=$PORT