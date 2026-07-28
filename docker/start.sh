#!/bin/sh

php artisan migrate --force || true

php artisan storage:link || true

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Recommendation Engine..."

cd /var/www/html/app/recommendation_engine/deployment

python3 api.py &
FLASK_PID=$!

sleep 5

echo "Checking Flask process..."
ps aux | grep python

echo "Flask startup check completed"