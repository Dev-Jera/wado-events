#!/bin/bash
set -e

echo "==> Ensuring required storage directories exist..."
# When a Railway volume is mounted at /app/storage it starts empty.
# Laravel cannot boot at all without these directories, so create them
# with raw mkdir before any php artisan command is attempted.
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "==> Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

echo "==> Clearing stale caches from previous deploy..."
php artisan cache:clear || echo "Warning: cache:clear failed (Redis may not be ready yet), continuing..."

echo "==> Rebuilding bootstrap caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Cache Filament's auto-discovered resources, pages, and widgets so the admin
# panel does not re-scan the filesystem on every request in production.
php artisan filament:cache-components 2>/dev/null || true

echo "==> Signalling any lingering queue workers to stop after their current job..."
# This writes a restart token into Redis. Any worker from a prior container
# that is somehow still alive will drain its current job and exit cleanly.
# On fresh containers this is a safe no-op.
php artisan queue:restart || echo "Warning: queue:restart failed (Redis may not be ready yet), continuing..."

echo "==> Starting queue worker (tickets → notifications → default)..."
# Queue priority order matters — tickets first (ticket issuance after payment),
# then notifications (email/SMS), then default (expiry jobs, misc).
#
# RULE: if a new job class calls ->onQueue('something-new'), that name MUST
# be added to the --queue= list below or those jobs will never run.
#
# Current queues used:
#   tickets       — IssueTicketForPayment
#   notifications — SendTicketNotification
#   default       — ExpirePendingPayment, misc
php artisan queue:work --daemon \
  --queue=tickets,notifications,default \
  --tries=3 \
  --timeout=60 \
  --max-time=3600 &

echo "==> Starting Laravel server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT
