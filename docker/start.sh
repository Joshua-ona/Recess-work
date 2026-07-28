#!/bin/sh

php artisan migrate --force || true
php artisan storage:link || true
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan serve --host=0.0.0.0 --port=$PORT