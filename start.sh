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

echo "==> Starting Laravel server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT
