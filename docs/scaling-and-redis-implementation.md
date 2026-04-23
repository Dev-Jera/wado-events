# Scaling & Redis Implementation — Technical Reference

**Date:** April 23, 2026  
**Project:** WADO Events Tickets (Laravel 12)  
**Author:** Engineering session log

---

## 1. Overview

This document covers a full audit of the WADO Events Tickets platform against a scaling checklist, followed by the implementation of all missing components. The goal was to ensure the system is production-ready for real-time ticketing traffic, with Redis as the backbone for queues, caching, seat locking, and WebSockets.

---

## 2. Pre-Existing Architecture (What Was Already Done)

Before this session, the following scaling components were already implemented:

### 2.1 Background Job Queues
Three job classes existed in `app/Jobs/`:

| Job | Queue | Purpose |
|-----|-------|---------|
| `SendTicketNotification` | `notifications` | Sends ticket confirmation email/SMS after booking |
| `IssueTicketForPayment` | `tickets` | Issues ticket after payment webhook is confirmed |
| `ExpirePendingPayment` | default | Cancels unpaid reservations after 5 minutes |

All jobs implement `ShouldQueue` and are dispatched from controllers — booking returns a fast response while heavy work runs in the background.

### 2.2 Database Transactions with Pessimistic Locking
Every critical booking path is wrapped in `DB::transaction()` with `lockForUpdate()`:

- `CheckoutController` — free ticket issuance and paid seat reservation
- `IssueTicketForPayment` job — ticket creation after payment confirmation
- `PaymentLifecycleService` — refund and reservation release
- `TicketVerificationController` — scan verification

This prevents race conditions and double bookings at the database level.

### 2.3 Redis Concurrency Gating
`CheckoutController` uses Redis atomic increment to cap concurrent checkouts at 20 per event:

```php
$key    = "checkout:active:{$eventId}";
$active = Redis::incr($key);
Redis::expire($key, 30);
if ($active > $max) {
    Redis::decr($key);
    return false;
}
```

### 2.4 Redis Caching
`Cache::remember()` used throughout with appropriate TTLs:

| Cache Key | TTL | Content |
|-----------|-----|---------|
| `home:featured_events` | 5 min | Featured events on home page |
| `home:category_pills` | 1 hour | Category filter pills |
| `events:list` | 2 min | Public event listing |
| `gate:portal:events:{userId}` | 15 sec | Gate operator event list |
| `gate:portal:categories:{eventId}` | 15 sec | Category inventory per event |

### 2.5 Distributed Scan Lock
Ticket scanning uses a Redis lock + DB transaction combination to prevent race conditions when a ticket is scanned twice in quick succession:

```php
Cache::lock('scan:' . $ticketCode, 10)->block(5, function () {
    DB::transaction(function () { ... });
});
```

### 2.6 Rate Limiting
Throttle middleware applied to all sensitive routes:

| Route | Limit |
|-------|-------|
| `POST /login` | 5 requests/minute |
| `POST /events/{event}/checkout` | 30 requests/minute |
| `POST /ticket-verification/scan` | 60 requests/minute |

Custom per-event, per-user `RateLimiter` also applied inside `CheckoutController` (10 attempts per 10 minutes).

### 2.7 Service Layer Separation
Business logic isolated into domain services under `app/Services/`:

- `PaymentLifecycleService` — refunds, reservation release, state transitions
- `PaymentNotificationService` — email and SMS delivery
- `MarzePayService` — payment gateway integration
- `TicketQrService` — QR code generation
- `InventorySyncService` — ticket inventory synchronisation
- `GuestCheckoutUserResolver` — guest user resolution

### 2.8 Laravel Telescope
Installed in `require-dev` with full watcher configuration. Production mode logs only exceptions, failed requests, and failed jobs. Accessible at `/telescope` by super-admins only.

---

## 3. What Was Missing (Pre-Session)

| # | Feature | Status |
|---|---------|--------|
| Laravel Horizon | Not installed (config existed, package did not) |
| Laravel Reverb | Not installed (config existed, package did not) |
| `predis/predis` | Not installed (app configured for `phpredis` which is unavailable on Windows) |
| DB indexes on `payment_transactions` | Missing `created_at`, composite `(event_id, status)`, `(user_id, status)` |
| DB indexes on `events` | Missing `status`, `starts_at`, `is_featured` |
| `.env.example` defaults | Set to `database` for queue/cache instead of `redis` |
| `config/database.php` Redis client | Hardcoded fallback to `phpredis` instead of `predis` |

---

## 4. Changes Made This Session

### 4.1 Installed Laravel Horizon

```bash
composer require laravel/horizon ^5.0 --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-posix
php artisan horizon:install
```

**Why `--ignore-platform-req`:** Horizon requires `ext-pcntl` and `ext-posix`, which are Linux-only extensions. They are unavailable on Windows/XAMPP but are present on the Railway Linux production server. The package is installed so it deploys correctly to production. Locally, `queue:listen` is used instead.

**What Horizon provides on production:**
- `/horizon` dashboard showing queue throughput, failed jobs, job duration
- Auto-scaling queue workers based on load
- Process supervisor that restarts crashed workers automatically

**Horizon is configured in:** `config/horizon.php`  
Queue environments defined: `production` (5 worker processes for tickets, 3 for notifications) and `local`.

### 4.2 Installed Laravel Reverb

```bash
composer require laravel/reverb ^1.0 --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-posix
php artisan reverb:install
```

Reverb is Laravel's built-in WebSocket server. It powers real-time features:

- Live ticket scan feed at the gate portal
- `TicketScanned` event broadcasts to `PrivateChannel('event.{eventId}.scans')`

**Reverb configuration** (`config/reverb.php` and `.env`):

```
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=wado-events
REVERB_APP_KEY=wado-events-key
REVERB_APP_SECRET=wado-events-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
```

**To start locally:** `php artisan reverb:start` (now included in `composer dev`).

### 4.3 Installed predis/predis

```bash
composer require predis/predis ^3.0 --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-posix
```

`predis` is a pure PHP Redis client. The alternative (`phpredis`) is a PHP C extension that is not available in standard XAMPP on Windows. `predis` works on all platforms with no extension required, making it the correct choice for cross-environment compatibility.

### 4.4 Fixed Redis Client Configuration

**`config/database.php`** — changed hardcoded fallback:
```php
// Before
'client' => env('REDIS_CLIENT', 'phpredis'),

// After
'client' => env('REDIS_CLIENT', 'predis'),
```

**`.env` and `.env.example`** — changed active client:
```
# Before
REDIS_CLIENT=phpredis

# After
REDIS_CLIENT=predis
```

### 4.5 Fixed `.env.example` Defaults

Updated defaults to be Redis-first so any new environment setup is immediately production-aligned:

```
# Before
BROADCAST_CONNECTION=log
QUEUE_CONNECTION=database
CACHE_STORE=database

# After
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=redis
CACHE_STORE=redis
```

Also added the full Reverb variable block to `.env.example` so it is documented for new environments.

### 4.6 Updated `composer dev` Script

Added `php artisan reverb:start` to the local development process, and pinned package versions:

```json
// Before (4 processes)
"npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74\"
  \"php artisan serve\"
  \"php artisan queue:listen --tries=1 --timeout=0\"
  \"php artisan pail --timeout=0\"
  \"npm run dev\"
  --names=server,queue,logs,vite"

// After (5 processes)
"npx concurrently -c \"#93c5fd,#c4b5fd,#86efac,#fb7185,#fdba74\"
  \"php artisan serve\"
  \"php artisan queue:listen --tries=1 --timeout=0\"
  \"php artisan reverb:start\"
  \"php artisan pail --timeout=0\"
  \"npm run dev\"
  --names=server,queue,reverb,logs,vite"
```

Note: `queue:listen` is kept (not `horizon`) because Horizon cannot run on Windows. On the production Linux server, replace with `php artisan horizon`.

### 4.7 Added Missing Database Indexes

**Migration:** `2026_04_22_000002_add_indexes_to_payment_transactions_table.php`

```php
$table->index('created_at', 'payment_transactions_created_at_idx');
$table->index(['event_id', 'status'], 'payment_transactions_event_status_idx');
$table->index(['user_id', 'status'], 'payment_transactions_user_status_idx');
```

**Why:** Admin finance views and payment history queries filter by `event_id + status` and `user_id + status`. Without these, full table scans occur under load.

**Migration:** `2026_04_22_000003_add_indexes_to_events_table.php`

```php
$table->index('status', 'events_status_idx');
$table->index('starts_at', 'events_starts_at_idx');
$table->index('is_featured', 'events_is_featured_idx');
```

**Why:** Event listings filter by `status` (published only), order by `starts_at`, and the home page filters by `is_featured`. All three were missing indexes.

Both migrations ran successfully against the local MySQL database.

---

## 5. Redis Architecture in the Application

```
User Request
     │
     ▼
Laravel App
     │
     ├── Cache::remember()  ──────────────► Redis (cache DB 1)
     │   Home page, events, gate portal        Fast reads, no MySQL hit
     │
     ├── Queue::dispatch()  ──────────────► Redis (queue DB 0)
     │   Ticket emails, SMS, PDF, expiry        Processed by queue worker
     │
     ├── Redis::incr()      ──────────────► Redis (default)
     │   Checkout concurrency gate             Max 20 concurrent per event
     │
     └── Cache::lock()      ──────────────► Redis (default)
         Scan deduplication lock               Prevents double-scan race condition

Queue Worker (separate process)
     │
     ├── Picks jobs from Redis queue
     ├── Sends ticket emails via Brevo/Resend
     ├── Sends SMS via Africa's Talking
     └── Issues tickets, generates QR codes

Reverb WebSocket Server (separate process)
     │
     └── Pushes TicketScanned events ──────► Gate portal browser (live feed)
```

---

## 6. Environment Setup

### 6.1 Local Development (Windows + XAMPP)

**Redis:** Memurai Developer v4.1.2  
**Install command (run as Administrator):**
```
winget install Memurai.MemuraiDeveloper
```
**Verify running:**
```
& "C:\Program Files\Memurai\memurai-cli.exe" ping
# Expected: PONG
```
Memurai runs as a Windows service on `127.0.0.1:6379` and starts automatically on boot.

**Start the full dev stack:**
```bash
composer dev
```
This starts 5 concurrent processes: web server, queue worker, Reverb WebSocket server, log viewer, Vite.

**Required `.env` values for local:**
```
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
QUEUE_CONNECTION=redis
CACHE_STORE=redis
BROADCAST_CONNECTION=reverb
```

### 6.2 Production (Railway)

**Redis:** Railway managed Redis service (added via "+ New Service → Redis" in Railway dashboard).

**Required Railway environment variables (set on the Laravel app service):**
```
REDIS_HOST=<from Railway Redis service Variables tab>
REDIS_PORT=<from Railway Redis service Variables tab>
REDIS_PASSWORD=<from Railway Redis service Variables tab>
REDIS_CLIENT=predis
QUEUE_CONNECTION=redis
CACHE_STORE=redis
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=<your chosen app id>
REVERB_APP_KEY=<your chosen key>
REVERB_APP_SECRET=<your chosen secret>
REVERB_HOST=<your production domain>
REVERB_PORT=443
REVERB_SCHEME=https
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

**Queue worker on production:** Replace `queue:listen` with Horizon:
```bash
php artisan horizon
```
Horizon dashboard available at `/horizon` (super-admin only).

---

## 7. Package Summary

| Package | Version | Purpose | Environment |
|---------|---------|---------|-------------|
| `laravel/horizon` | ^5.0 | Queue monitoring dashboard, process management | Production (Linux only) |
| `laravel/reverb` | ^1.0 | WebSocket server for real-time features | Both |
| `predis/predis` | ^3.0 | Pure PHP Redis client (replaces phpredis extension) | Both |

---

## 8. Files Changed

| File | Change |
|------|--------|
| `composer.json` | Added `laravel/horizon`, `laravel/reverb`, `predis/predis`; updated `dev` script to include Reverb; pinned versions |
| `composer.lock` | Updated with new package lock entries |
| `config/database.php` | Changed Redis client default from `phpredis` to `predis` |
| `.env` | Changed `REDIS_CLIENT` from `phpredis` to `predis` |
| `.env.example` | Changed `REDIS_CLIENT` to `predis`; changed `QUEUE_CONNECTION` and `CACHE_STORE` to `redis`; changed `BROADCAST_CONNECTION` to `reverb`; added full Reverb variable block |
| `config/horizon.php` | Published by `horizon:install` (was pre-existing, confirmed correct) |
| `config/reverb.php` | Published by `reverb:install` |
| `app/Providers/HorizonServiceProvider.php` | Published by `horizon:install` |
| `database/migrations/2026_04_22_000002_add_indexes_to_payment_transactions_table.php` | New — indexes on `created_at`, `(event_id, status)`, `(user_id, status)` |
| `database/migrations/2026_04_22_000003_add_indexes_to_events_table.php` | New — indexes on `status`, `starts_at`, `is_featured` |

---

## 9. Verified Working

| Check | Result |
|-------|--------|
| `php artisan package:discover` | All packages discovered including Horizon and Reverb |
| `php artisan migrate` | Both index migrations ran successfully |
| `config('cache.default')` | Returns `redis` |
| `config('queue.default')` | Returns `redis` |
| `config('database.redis.client')` | Returns `predis` |
| `memurai-cli.exe ping` | Returns `PONG` — Redis live on port 6379 |
