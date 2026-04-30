# WADO Events & Tickets

A full-stack event ticketing platform built for WADO Concepts. Handles event publishing, ticket sales, QR-based gate scanning, re-entry management, and real-time admin reporting.

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 12 |
| Admin panel | Filament v3 |
| Database | MySQL / MariaDB |
| Cache & queues | Redis + Laravel Horizon |
| WebSockets | Laravel Reverb |
| Payments | MarzePay (Mobile Money) |
| Email | Brevo (Sendinblue) |
| SMS | Africa's Talking |
| Storage | Laravel local disk / Railway volume |
| Deployment | Railway |

## Features

- **Public event listing** — filterable by category, featured events pinned to top
- **Ticket checkout** — multi-ticket purchase, promo code support, Mobile Money STK push via MarzePay
- **QR tickets** — AES-256-GCM signed QR codes, PDF download, email delivery
- **Gate scanning** — camera-based QR scanner with manual code entry fallback; entry and exit modes
- **Re-entry system** — per-event re-entry policy with configurable limits and cooldown periods
- **Anti-oversell locking** — Redis lock + `lockForUpdate()` DB transaction on every purchase and scan
- **Admin panel** — event management, ticket categories, sales reporting, promo codes, refunds, gate batches
- **Security** — HMAC-SHA256 webhook verification, HSTS, fail-closed webhook handling, rate limiting

## Local Setup

```bash
# 1. Clone and install
git clone <repo-url>
cd wado-events-tickets
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database
php artisan migrate
php artisan db:seed   # optional demo data

# 4. Storage link
php artisan storage:link

# 5. Start dev servers
php artisan serve
npm run dev

# 6. Queue worker (separate terminal)
php artisan queue:work
```

## Key Environment Variables

```dotenv
APP_URL=https://your-domain.com
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=...
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

REDIS_HOST=...
REDIS_PASSWORD=...

MARZEPAY_BUSINESS_ID=...
MARZEPAY_API_KEY=...
MARZEPAY_WEBHOOK_SECRET=...

BREVO_API_KEY=...

AFRICASTALKING_USERNAME=...
AFRICASTALKING_API_KEY=...
```

## Deployment (Railway)

The `start.sh` script runs on every deploy:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link --force
php artisan serve --host=0.0.0.0 --port=$PORT
```

## Gate Scanning

Navigate to `/tickets/verify` with a staff account. Toggle between **Entry** and **Exit** mode before scanning. The scanner supports both camera QR reading and manual ticket code entry.

- Entry scan: validates ticket, marks attendee as inside
- Exit scan: records exit, enables re-entry if the event allows it
- Re-entry limits and cooldown periods are configured per event in the admin panel

## Testing

```bash
php artisan test
```

Includes unit tests for checkout security, payment verification, promo code logic, and webhook handling.

## License

Proprietary — WADO Concepts. All rights reserved.
