#!/bin/bash
set -e

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

echo "==> Caching config, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Starting queue worker in background..."
php artisan queue:work --daemon --queue=tickets,notifications,default --tries=3 --timeout=60 --max-time=3600 &

echo "==> Starting Laravel server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT
