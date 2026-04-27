#!/bin/bash
set -e

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

echo "==> Clearing stale caches from previous deploy..."
# Clear the Redis application cache so no old event listings, settings, or
# gate portal data bleed across from the previous container.
php artisan cache:clear

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
php artisan queue:restart

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
