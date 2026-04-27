# WADO Events Tickets — Technical Implementation Guide

**Version:** 1.1  
**Date:** April 27, 2026  
**Project:** WADO Events Tickets  
**Framework:** Laravel 12 (PHP 8.2)  
**Prepared by:** Engineering Team

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Technology Stack](#2-technology-stack)
3. [System Architecture](#3-system-architecture)
4. [The Booking Flow — End to End](#4-the-booking-flow--end-to-end)
5. [Infrastructure Components](#5-infrastructure-components)
   - 5.1 Redis
   - 5.2 Queue Workers & Laravel Horizon
   - 5.3 WebSockets & Laravel Reverb
   - 5.4 Database & Indexes
   - 5.5 Rate Limiting
   - 5.6 Caching Strategy
6. [Route Reference](#6-route-reference)
7. [API Documentation (Scribe)](#7-api-documentation-scribe)
8. [Environment Setup](#8-environment-setup)
9. [Development Workflow](#9-development-workflow)
10. [Deployment Guide (Railway)](#10-deployment-guide-railway)
11. [Monitoring & Observability](#11-monitoring--observability)
12. [Testing Guide](#12-testing-guide)
13. [Troubleshooting](#13-troubleshooting)
14. [Package Reference](#14-package-reference)
15. [Files Changed in This Implementation](#15-files-changed-in-this-implementation)
16. [Feature Additions — April 2026](#16-feature-additions--april-2026)

---

## 1. Project Overview

WADO Events Tickets is a full-stack event ticketing platform built on Laravel 12. It allows event organisers to publish events and manage ticket categories, and allows the public to browse events, purchase tickets via mobile money (MarzPay), and receive QR-coded tickets via email and SMS.

The platform supports three types of users:

- **Public buyers** — browse events, check out tickets, receive confirmation emails and SMS, download PDF tickets, request refunds
- **Gate agents** — scan QR codes at the entrance using the gate portal, process walk-in cash sales, scan physically-printed gate-sale tickets
- **Administrators** — manage events, categories, users, view finance reports, manually confirm or refund payments, monitor queues and system health, manage site content, handle package enquiries, generate and print batches of physical gate-sale tickets

The system is designed to handle real-time concurrent ticket sales with race condition protection, background job processing, WebSocket-based live updates at the gate, and Redis-backed caching and queuing throughout.

---

## 2. Technology Stack

| Layer | Technology | Purpose |
|-------|-----------|---------|
| Backend framework | Laravel 12 (PHP 8.2) | Application logic, routing, ORM |
| Admin panel | Filament 5.4 | Dashboard, resource management |
| Database | MySQL (production) / SQLite (local dev) | Primary data store |
| Cache & Queue | Redis (Memurai on Windows, Railway Redis on production) | Fast key-value store for queues, cache, locks |
| Queue monitoring | Laravel Horizon | Production queue dashboard and process supervisor |
| WebSockets | Laravel Reverb | Real-time gate scan feed |
| Redis PHP client | predis/predis | Pure PHP Redis client (cross-platform) |
| Payment gateway | MarzPay | Mobile money payment processing (Uganda) |
| Email | Brevo / Resend | Transactional email delivery |
| SMS | Africa's Talking | Ticket confirmation SMS |
| PDF generation | barryvdh/laravel-dompdf | PDF ticket generation |
| QR codes | endroid/qr-code | QR code generation embedded in tickets |
| Monitoring | Laravel Telescope | Local and production query/job/request monitoring |
| API docs | Scribe (knuckleswtf/scribe) | Auto-generated interactive API documentation |
| Frontend build | Vite + Tailwind CSS | Asset compilation |
| Hosting | Railway | Production deployment (app + Redis + MySQL) |

---

## 3. System Architecture

### 3.1 High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         PUBLIC INTERNET                          │
└───────────────────────┬─────────────────────────────────────────┘
                        │ HTTPS
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│                      RAILWAY PLATFORM                           │
│                                                                  │
│  ┌──────────────────────┐    ┌──────────────┐  ┌─────────────┐ │
│  │   Laravel App Server │    │    Redis      │  │   MySQL     │ │
│  │   (PHP + Reverb)     │◄──►│  (Queue +    │  │  (Primary   │ │
│  │                      │    │   Cache +    │  │   Store)    │ │
│  │  ┌────────────────┐  │    │   Locks)     │  │             │ │
│  │  │ Queue Worker   │  │    └──────────────┘  └─────────────┘ │
│  │  │ (Horizon)      │  │                                       │
│  │  └────────────────┘  │                                       │
│  └──────────────────────┘                                       │
└─────────────────────────────────────────────────────────────────┘
```

### 3.2 Request Lifecycle

When a user makes a booking request, the following happens:

1. The request hits the Laravel app server
2. Rate limiting is checked (Redis-backed throttle)
3. A Redis checkout slot is acquired (concurrency gate — max 20 per event)
4. A database transaction begins with `lockForUpdate()` on the ticket category and event rows
5. Seat availability is verified and decremented
6. A payment transaction record is created
7. A `ExpirePendingPayment` job is dispatched to Redis queue (fires after 5 minutes if payment not completed)
8. The transaction commits
9. The checkout slot is released back to Redis
10. The user is redirected to the payment gateway
11. When MarzPay calls the webhook, an `IssueTicketForPayment` job is dispatched to Redis
12. The job issues the ticket, generates the QR code, and dispatches `SendTicketNotification`
13. The notification job sends the confirmation email and SMS

The user gets a fast response at step 9. All the heavy work (email, SMS, PDF, QR) happens in background jobs.

### 3.3 Redis Database Allocation

Redis uses multiple logical databases to keep concerns separated:

| Redis DB | Purpose | Config Key |
|----------|---------|-----------|
| DB 0 | Default (queue jobs, locks, rate limits) | `REDIS_DB=0` |
| DB 1 | Cache (events, home page, gate portal data) | `REDIS_CACHE_DB=1` |

---

## 4. The Booking Flow — End to End

### 4.1 Free Ticket Flow

```
User visits /events/{event}/checkout
    ↓
Fills checkout form (name, email, quantity)
    ↓
POST /events/{event}/checkout
    ↓
Rate limit check (30 req/min + 10 per event per user per 10 min)
    ↓
Redis concurrency gate acquired
    ↓
DB::transaction() + lockForUpdate() on ticket_category and event
    ↓
Seat availability verified and decremented
    ↓
Ticket created with QR code
    ↓
SendTicketNotification dispatched to Redis queue
    ↓
Redis slot released
    ↓
User redirected to /my-tickets/{ticket}
    ↓
[Background] Queue worker picks up job
    ↓
Confirmation email + SMS sent
```

### 4.2 Paid Ticket Flow

```
User visits /events/{event}/checkout
    ↓
Fills checkout form (name, email, quantity, phone, payment provider)
    ↓
POST /events/{event}/checkout
    ↓
Rate limiting + Redis concurrency gate
    ↓
DB transaction: seat reserved, payment_transaction created (status: pending)
    ↓
ExpirePendingPayment dispatched (fires in 5 minutes)
    ↓
User redirected to MarzPay payment page
    ↓
User completes payment on MarzPay
    ↓
MarzPay calls POST /payments/marzepay/webhook
    ↓
Webhook verifies HMAC signature
    ↓
IssueTicketForPayment dispatched to Redis queue
    ↓
[Background] Job runs inside DB transaction + lockForUpdate()
    ↓
Ticket created, QR code generated, payment_transaction status → confirmed
    ↓
SendTicketNotification dispatched
    ↓
Email + SMS confirmation sent to buyer
```

### 4.3 Gate Scan Flow

```
Gate agent opens /ticket-verification
    ↓
Selects event from dropdown
    ↓
Scans QR code (camera or manual entry)
    ↓
POST /ticket-verification/scan (JSON endpoint, 60 req/min limit)
    ↓
QR payload decoded — check payload['type']
    ↓
    ├─ type missing or 'online' → online ticket path (queries tickets table)
    │       HMAC verified via TicketQrService (v2 payload)
    │       Redis lock: Cache::lock('scan:{code}', 10s)
    │       DB transaction + lockForUpdate()
    │       Ticket marked used, TicketScanned broadcast via Reverb
    │
    └─ type = 'gate_print' → gate-sale ticket path (queries gate_tickets table)
            HMAC verified via GatePrintTicketService (gate_print payload)
            Redis lock: Cache::lock('scan:gate:{code}', 10s)
            DB transaction + lockForUpdate()
            GateTicket status → 'used', used_at + used_by stamped
    ↓
JSON response returned to gate app
    ↓
[Live] Admin dashboard receives WebSocket push — scan appears in real time
```

Both paths share the same distributed-lock + DB-transaction pattern to prevent double-scan race conditions. The `type` field in the QR payload is what differentiates an online ticket from a gate-sale printed ticket. Gate-sale payloads carry `batch_id` in addition to `code` and `event_id`.

---

## 5. Infrastructure Components

### 5.1 Redis

Redis is the central infrastructure layer of the platform. It serves four distinct functions:

**Queue backend** — All background jobs are pushed to Redis queues instead of being processed inline. This means booking responses return in milliseconds regardless of how long email delivery or PDF generation takes. Jobs are stored in Redis and consumed by a separate queue worker process. If the queue worker is down, jobs accumulate in Redis and are processed when it comes back up.

**Application cache** — Frequently read data that does not change often is stored in Redis with a TTL (time to live). This prevents the database from being queried on every page load for data like the event listing, home page featured events, and gate portal inventory. When the TTL expires, the next request fetches fresh data from MySQL and repopulates the cache.

**Concurrency gating** — When many users try to book at the same time, Redis `INCR` (atomic increment) is used to count how many checkouts are currently in flight for a given event. If the count exceeds 20, new requests are rejected immediately with a "busy" response rather than overloading the database.

**Distributed locks** — When a ticket is scanned, a Redis lock is acquired for that specific QR code for 10 seconds. If a second scan request for the same ticket arrives within those 10 seconds (e.g., a double scan), it blocks until the first request finishes. This prevents a race condition where the same ticket could be marked as used twice and cause incorrect audit logs.

**Redis connection** (`config/database.php`):
```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'predis'),
    'default' => [
        'host'     => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port'     => env('REDIS_PORT', 6379),
        'database' => env('REDIS_DB', 0),
    ],
    'cache' => [
        'host'     => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port'     => env('REDIS_PORT', 6379),
        'database' => env('REDIS_CACHE_DB', 1),
    ],
]
```

**Why `predis` instead of `phpredis`:** `phpredis` is a PHP C extension that must be compiled and installed separately for each server environment. It is not available in standard XAMPP on Windows. `predis` is a pure PHP library that works on every platform with no extension installation. The behaviour is identical for all operations used in this application.

### 5.2 Queue Workers & Laravel Horizon

**Why queues exist:** When someone buys a ticket, several things need to happen — generate a unique ticket code, generate a QR code image, send a confirmation email with the ticket attached, send an SMS. If all of that ran inside the HTTP request, the user would wait 3–10 seconds for a response. Under load with many buyers at once, requests would time out. Instead, the app returns a response to the user immediately ("payment received") and hands the slow work to a background queue worker, which processes it a few seconds later. The user never waits for email delivery or file generation.

A secondary benefit: if the email server is temporarily down, the job retries automatically up to 3 times with a delay between attempts. Nothing is lost.

**What belongs in a queue vs what does not:**

The rule is: if a task calls an external service (email provider, SMS gateway) or generates a file (QR code, PDF), it goes in a queue. Everything else runs directly in the request because it is fast enough.

| Task | Runs in queue? | Reason |
|------|---------------|--------|
| Send ticket confirmation email | Yes | Calls Brevo API — external, slow |
| Send ticket SMS | Yes | Calls Brevo API — external, slow |
| Generate QR code image | Yes | File generation — slow |
| Issue ticket record after payment | Yes | Chains into email/SMS jobs |
| Expire unpaid payment after timeout | Yes | Delayed task — must not block |
| Load event listings page | No | Simple database query, milliseconds |
| Show checkout page | No | Just renders a view |
| Process payment webhook | No | Quick DB write, then dispatches a job |
| Scan a ticket at the gate | No | Quick DB lookup and update |

These are the only things that need to be in queues for what the app currently does. The three existing job classes cover all of them. No new queue work is needed unless a completely new category of slow background task is added.

**Three job classes:**

`SendTicketNotification` — runs on the `notifications` queue. Sends the confirmation email and SMS to the ticket buyer. Has 3 retries with 30-second backoff. If it fails after all retries, it lands in the failed jobs table.

`IssueTicketForPayment` — runs on the `tickets` queue. Called by the payment webhook. Verifies the payment is confirmed (not duplicate), creates the ticket record, generates and stores the QR code, and dispatches `SendTicketNotification`. Has 4 retries with 10/20/30-second backoff. Runs inside a DB transaction with `lockForUpdate()` to prevent duplicate ticket issuance.

`ExpirePendingPayment` — runs on the default queue with a 5-minute delay. If a user initiates payment but never completes it, this job releases the reserved seat back to inventory. It checks if the payment is still pending before acting, so it is safe even if payment is completed before the delay fires.

**The queue worker on production** is started inside `start.sh` — the script Railway runs every time the app boots:

```bash
php artisan queue:work --daemon --queue=tickets,notifications,default --tries=3 --timeout=60 --max-time=3600 &
```

The `--queue` flag is critical. It tells the worker which queues to listen on, in priority order. `tickets` is checked first (ticket issuance), then `notifications` (emails/SMS), then `default` (everything else). If a queue name is missing from this list, jobs dispatched to it will sit in Redis indefinitely and never be processed — this was a real incident where emails stopped sending because the worker was started without the queue names.

**Rule: whenever a new job class is added that calls `$this->onQueue('some-name')`, that name must be added to the `--queue=` list in `start.sh` before deploying.**

The `--max-time=3600` flag restarts the worker after one hour. This prevents gradual memory growth from a long-running PHP process. The `&` runs it in the background so the script continues to start the web server.

**What "queues" actually are:** You do not create queues manually. A queue is just a named list in Redis. When a job calls `$this->onQueue('tickets')`, Laravel pushes a serialised job payload into a Redis list called `queues:tickets`. When the worker runs with `--queue=tickets`, it pops items from that list and executes them. The list is created automatically the first time a job is pushed to it. There is nothing to configure or provision — queues exist as long as jobs are being dispatched to them.

### 5.3 WebSockets & Laravel Reverb

Reverb is Laravel's built-in WebSocket server. It allows the server to push data to connected browsers in real time without the browser needing to poll.

**What it is used for:** The gate portal receives live ticket scan events. When a gate agent scans a ticket at the event entrance, the verification result is broadcast via WebSocket to all connected gate portal sessions for that event. This means the gate admin watching the dashboard sees scans appear in real time without refreshing the page.

**How it works technically:**
1. `TicketVerificationController::scanJson()` fires a `TicketScanned` event after a successful scan
2. `TicketScanned` implements `ShouldBroadcast` and broadcasts to a private channel: `event.{eventId}.scans`
3. Reverb receives the broadcast and pushes it to all WebSocket clients subscribed to that channel
4. The gate portal JavaScript (via Laravel Echo) listens on the channel and updates the scan feed in the DOM

**Reverb runs as a separate process** alongside the web server. In development, it is started automatically by `composer dev`. On production, it must be configured as a separate Railway service or process.

### 5.4 Database & Indexes

The platform uses MySQL in production (SQLite for local dev). All critical write operations — seat reservation, ticket issuance, payment state changes, refunds, and scan verification — are wrapped in `DB::transaction()` with `lockForUpdate()` on the rows being modified. This prevents two simultaneous requests from both reading the same "available" seat count and both decrementing it, which would result in overselling.

**Indexes added during this implementation:**

On `payment_transactions`:

| Index | Columns | Why |
|-------|---------|-----|
| `payment_transactions_created_at_idx` | `created_at` | Finance reports filter by date range |
| `payment_transactions_event_status_idx` | `event_id, status` | Admin payment list filters by event and status together |
| `payment_transactions_user_status_idx` | `user_id, status` | User payment history filtered by status |

Note: `user_id`, `event_id`, and `ticket_id` already had indexes via foreign key constraints. The new indexes are composites and the `created_at` standalone.

On `events`:

| Index | Column | Why |
|-------|--------|-----|
| `events_status_idx` | `status` | Event listing filters published events only |
| `events_starts_at_idx` | `starts_at` | Event listing orders by start date |
| `events_is_featured_idx` | `is_featured` | Home page featured events query |

### 5.5 Rate Limiting

Rate limiting prevents abuse and protects against brute force attacks and traffic spikes.

**Route-level throttle middleware:**

| Route | Middleware | Limit | Purpose |
|-------|-----------|-------|---------|
| `POST /login` | `throttle:5,1` | 5 per minute | Brute force login protection |
| `POST /events/{event}/checkout` | `throttle:30,1` | 30 per minute | General checkout flood protection |
| `POST /ticket-verification/scan` | `throttle:60,1` | 60 per minute | Gate scan flood protection |

**Custom RateLimiter inside CheckoutController:**

In addition to the middleware, `CheckoutController` applies a per-event, per-user rate limit of 10 purchase attempts per 600 seconds. The key is built from the event ID and either the authenticated user's ID or the visitor's IP address:

```php
$key = 'checkout:purchase:' . $event->id . ':' . ($user?->id ? 'user:'.$user->id : 'ip:'.$request->ip());
```

This prevents a single user from hammering the checkout endpoint even if they clear the general throttle.

**Login RateLimiter inside AuthController:**

Manual `RateLimiter::hit()` and `tooManyAttempts()` calls per email+IP combination, with a 60-second lockout message showing the remaining wait time.

### 5.6 Caching Strategy

All cache reads use `Cache::remember()`, which returns the cached value if it exists or runs the callback, stores the result, and returns it. All cache entries are stored in Redis DB 1.

| Cache Key Pattern | TTL | What Is Cached | Invalidation |
|-------------------|-----|----------------|--------------|
| `home:featured_events` | 5 minutes | Featured events for home page hero | TTL expiry |
| `home:category_pills` | 1 hour | Event category filter list | TTL expiry |
| `events:list` | 2 minutes | Public event listing | TTL expiry |
| `gate:portal:events:{userId}` | 15 seconds | Gate agent's assigned events | TTL expiry |
| `gate:portal:categories:{eventId}` | 15 seconds | Ticket category inventory per event | TTL expiry |

The short TTLs on gate portal data (15 seconds) ensure inventory counts stay near-real-time without hitting the database on every scan. The longer TTL on category pills (1 hour) is appropriate because categories change rarely.

---

## 6. Route Reference

This section documents every HTTP endpoint in the application — what it does, who can access it, and what it expects.

---

### Authentication

#### `GET /login`
Displays the login form. Accessible to unauthenticated users only (redirects authenticated users to the home page).

#### `POST /login`
Processes login credentials. Validates email and password, applies rate limiting (5 attempts per minute per email+IP), logs the login attempt to the `login_attempts` table, and creates a session. Redirects to the intended page or home.

**Rate limit:** 5 per minute  
**Fields:** `email`, `password`, `remember` (optional)

#### `GET /register`
Displays the registration form.

#### `POST /register`
Creates a new user account. Validates name, email (unique), and password. Logs the user in immediately after registration.

**Fields:** `name`, `email`, `password`, `password_confirmation`

#### `POST /logout`
Destroys the current session and redirects to the login page.

---

### Events

#### `GET /`
Home page. Displays featured events and category filters. Data served from Redis cache (5-minute TTL for featured events, 1-hour for categories).

#### `GET /events`
Public event listing. Shows all published events. Cached in Redis for 2 minutes. Supports filtering by category.

#### `GET /events/{event}`
Single event detail page. Shows event description, date, venue, ticket categories and their availability. The `{event}` parameter resolves by slug.

#### `POST /events/{event}/bookmark`
Toggles a bookmark on an event for the authenticated user. Returns a JSON response indicating the new bookmark state. Requires authentication.

---

### Checkout

#### `GET /events/{event}/checkout`
Displays the checkout form for an event. Pre-fills fields from session (for users returning after a failed payment attempt). Shows available ticket categories and quantities. Aborts with 404 if the event is sold out or if checkout is closed.

#### `POST /events/{event}/checkout`
The core booking endpoint. Processes a ticket purchase or reservation.

**Rate limit:** 30 per minute (middleware) + 10 per event per user per 10 minutes (custom)  
**Fields:** `ticket_category_id`, `quantity`, `holder_name`, `email`, `phone_number`, `payment_provider`, `idempotency_key`, `create_account` (optional)

**What happens:**
1. Validates all input fields via `CheckoutRequest`
2. Acquires Redis concurrency slot (max 20 concurrent per event)
3. Opens DB transaction with `lockForUpdate()` on category and event rows
4. For free tickets: creates ticket immediately, dispatches notification job
5. For paid tickets: reserves seats, creates pending payment transaction, dispatches expiry job, initiates MarzPay payment
6. Releases Redis slot
7. Redirects to ticket page (free) or payment URL (paid)

If the idempotency key matches an existing transaction, returns the existing result without creating a duplicate.

---

### Tickets

#### `GET /my-tickets`
Lists all tickets belonging to the authenticated user, ordered by purchase date descending. Shows ticket status, event name, and quick actions.

#### `GET /my-tickets/{ticket}`
Displays a single ticket's detail page. Shows QR code, event details, seat information, and ticket holder name. Authorises that the ticket belongs to the current user.

#### `GET /my-tickets/{ticket}/download`
Downloads the ticket QR code as an SVG file.

#### `GET /my-tickets/{ticket}/pdf`
Generates and streams a PDF version of the ticket. The PDF includes the event details, holder name, QR code, and WADO branding. Generated by DomPDF.

#### `POST /my-tickets/{ticket}/refund-request`
Submits a refund request for a ticket. Records the reason and timestamps the request. Does not process the refund automatically — an admin must review and confirm. Requires the ticket to be in a refundable state.

**Fields:** `reason`

#### `POST /my-tickets/{ticket}/dismiss`
Dismisses a ticket notification or banner. Updates the `dismissed_at` timestamp so the UI no longer shows alerts for this ticket.

---

### Gate Portal

#### `GET /gate-portal`
The gate agent dashboard. Shows the agent's assigned events and, for the selected event, a list of all tickets with their scan status. Requires the `gate_agent` role assigned to the event. Data is cached in Redis for 15 seconds per user and event.

Also serves as the WebSocket listening page — the gate portal connects to Reverb on load and receives live scan events for the selected event.

#### `POST /gate-portal/walk-in-sale`
Processes a cash walk-in sale at the gate. Creates a ticket directly without going through the payment gateway. Subject to per-agent daily cash limits (`WALKIN_CASH_MAX_TICKETS_PER_SALE` and `WALKIN_CASH_DAILY_LIMIT_UGX`). Requires the `gate_agent` role.

**Fields:** `event_id`, `ticket_category_id`, `quantity`, `holder_name`, `phone_number`

---

### Ticket Verification

#### `GET /ticket-verification`
Displays the ticket verification interface. Allows gate staff to search tickets by QR code or manually enter a code. Shows scan history for the selected event. Requires the `verify` permission on tickets.

#### `POST /ticket-verification`
Processes a ticket verification (non-JSON, web form). Looks up a ticket by code, checks its validity, and marks it as used if valid.

**Fields:** `ticket_code`, `event_id`

#### `POST /ticket-verification/scan`
JSON endpoint used by the live scan interface. Called when a QR code is scanned via camera.

**Rate limit:** 60 per minute  
**Response:** JSON with `ok` (bool), `reason`, holder name, event name, and timestamp.

**Security:** Acquires a Redis distributed lock on the ticket code for 10 seconds before processing. Verifies the QR payload HMAC signature. Runs inside a DB transaction. Dispatches to one of two sub-handlers depending on the `type` field in the QR payload — see Section 16 for the gate-print ticket path.

**Fields:** `selected_event_id`, `ticket_code` (optional), `scanned_payload` (signed JSON string), `device_id` (optional)

#### `GET /ticket-verification/export`
Exports the scan audit log for the selected event as a CSV file. Includes ticket code, holder name, scan time, and gate agent.

---

### Admin — Payments

These routes require the `super_admin` or admin role.

#### `GET /admin/payments`
Lists all payment transactions with filtering by status, event, and date range. Used by admins to track payments, spot failures, and manage disputes.

#### `POST /admin/payments/{paymentTransaction}/confirm`
Manually marks a payment transaction as confirmed. Used when a payment completed on MarzPay but the webhook was not received. Triggers ticket issuance and buyer notification.

#### `POST /admin/payments/{paymentTransaction}/refund`
Processes a refund for a confirmed payment. Updates the transaction status to `refunded`, releases the reserved seat back to inventory, and sends a refund notification to the buyer.

#### `POST /admin/payments/{paymentTransaction}/resend`
Resends the confirmation email and SMS for an already-confirmed payment. Used when a buyer claims they did not receive their ticket.

---

### Ticket Packages

#### `GET /ticket-packages`
Public page listing WADO's ticket printing and event management service packages (VIP wristbands, gate-sale printing, online ticketing). Content is editable from the admin content management page.

#### `POST /ticket-packages/enquire`
Submits a package enquiry from the public contact form. Validates contact details and the selected package, stores the enquiry in the `enquiries` table, and sends a notification email to `wadoconcepts@gmail.com` with reply-to set to the enquirer's address.

**Fields:** `name`, `email`, `phone` (optional), `package`, `event_date` (optional), `attendance` (optional), `message` (optional)  
**Response:** JSON `{ "success": true }`

---

### Gate Ticket Batches

#### `GET /gate-batches/{batchId}/download-pdf`
Downloads a printable PDF of all tickets in a gate-sale batch. Requires authentication and admin/event-owner role. If the batch is still in `draft` status (tickets not yet generated), ticket codes are auto-generated before the PDF is rendered. Each ticket's HMAC signature is verified before inclusion — tampered records are silently excluded.

**Access:** `super_admin`, `admin`, or `event_owner` (event owners restricted to their own events)  
**Response:** PDF file download

---

### Webhooks

#### `POST /payments/marzepay/webhook`
Receives payment status callbacks from MarzPay. This route is called by MarzPay's servers, not by the browser.

**Security:** Verifies the request HMAC signature against `MARZEPAY_WEBHOOK_SECRET` before processing. Rejects any request with an invalid signature with a 401 response.

**What it does:**
- Looks up the matching pending payment transaction by provider reference
- If status is `successful`: dispatches `IssueTicketForPayment` to the Redis queue
- If status is `failed`: updates transaction to failed, releases seat reservation
- Returns `200 OK` immediately — actual processing is async

---

### Internal / Monitoring

#### `GET /health`
Health check endpoint. Returns the application status. Used by Railway to verify the deployment is running.

#### `GET /telescope` *(dev + admin only)*
Laravel Telescope monitoring dashboard. Shows recent queries, jobs, cache hits, requests, exceptions, and Redis commands. In production, accessible to super-admins only.

#### `GET /horizon` *(production, admin only)*
Laravel Horizon queue dashboard. Shows queue throughput, failed jobs, worker health, and job metrics. Only available on Linux (production server).

#### `GET /docs`
Scribe-generated interactive API documentation. See Section 7.

---

## 7. API Documentation (Scribe)

### 7.1 What Scribe Is

Scribe is an API documentation generator for Laravel. Unlike Swagger, which requires you to write documentation annotations manually on every endpoint, Scribe reads your existing code — routes, FormRequest validation rules, controller docblocks, and response structures — and generates documentation automatically. This means documentation stays in sync with the code without extra maintenance work.

Each time you run `php artisan scribe:generate`, Scribe re-reads all your routes and produces three outputs:

- **An interactive HTML page** at `/docs` with a sidebar, grouped endpoints, request parameter tables, example code in multiple languages, and a "Try It Out" button that fires real requests against your application
- **A Postman collection** at `/docs.postman` — a JSON file you can import directly into Postman to test all endpoints with pre-filled parameters
- **An OpenAPI 3.0 specification** at `/docs.openapi` — a YAML file compatible with Swagger UI, Insomnia, and any other OpenAPI-compatible tool

### 7.2 Accessing the Documentation

| URL | What You Get |
|-----|-------------|
| `/docs` | Full interactive HTML documentation page |
| `/docs.postman` | Postman collection (JSON) — download and import into Postman |
| `/docs.openapi` | OpenAPI 3.0 YAML spec — use with Swagger UI, Insomnia, etc. |

> **Important:** `/docs.openapi` and `/docs.postman` are **machine-readable files**, not pages meant to be opened in a browser. If you visit `/docs.openapi` directly in Chrome or Firefox you will see a wall of raw YAML — that is normal and correct, it is not broken. These files are inputs for external tools, not for reading.

**The primary human-readable interface is `/docs`.** That is where you browse endpoints, read descriptions, and fire real requests using the Try It Out buttons.

#### Using `/docs.openapi` with Swagger UI

Swagger UI is a popular open-source API explorer that renders an OpenAPI spec into a visual, interactive interface. To load your spec into it:

1. Go to **https://editor.swagger.io**
2. Click **File → Import URL**
3. Paste: `http://127.0.0.1:8000/docs.openapi`
4. Swagger UI renders your full API with try-it-out buttons, request schemas, and response examples

This is useful when sharing the API with a frontend developer or external party who does not have access to the running application.

#### Using `/docs.postman` with Postman

Postman is the standard tool for API testing. To import your collection:

1. Open Postman
2. Click **Import** in the top-left
3. Choose **Link** and paste: `http://127.0.0.1:8000/docs.postman`
4. Postman creates a folder for every endpoint group with all parameters pre-filled

Once imported, set up an environment in Postman with `baseUrl = http://127.0.0.1:8000` and log in via the `/login` request so Postman captures the session cookie.

### 7.3 How Authentication Works in the Docs

This platform uses **session-based authentication** — not API tokens. There is no `Authorization: Bearer` header. Authentication is a browser cookie set by the `/login` endpoint.

When using the **Try It Out** buttons in the docs, do this:

1. Log in at `/login` in the same browser tab or window
2. Navigate to `/docs`
3. Click any endpoint and hit **Send Request**

Your browser automatically includes the session cookie in every request, so you are authenticated without needing to configure anything extra in the docs UI.

### 7.4 Endpoint Groups

All endpoints are organised into named groups. The grouping is defined by `@group` docblocks on each controller class. The order of groups in the sidebar is controlled by the `groups.order` setting in `config/scribe.php`.

| Group | Controller | What It Covers |
|-------|-----------|----------------|
| Authentication | `AuthController` | Login, registration, logout |
| Events | `EventController` | Public event browsing and detail pages |
| Checkout | `CheckoutController` | Ticket purchase and seat reservation |
| Tickets | `TicketController` | Viewing, downloading, and managing tickets |
| Gate Portal | `GatePortalController` | Gate agent dashboard and walk-in cash sales |
| Ticket Verification | `TicketVerificationController` | QR scan and verification at the entrance |
| Admin — Payments | `PaymentController` | Admin payment confirmation, refunds, resends |
| Webhooks | `PaymentWebhookController` | Incoming MarzPay payment status callbacks |

### 7.5 How to Document a New Endpoint

When you add a new controller method, add a PHP docblock above it with the following Scribe-recognised tags:

```php
/**
 * Submit a booking
 *
 * Reserves a seat and initiates payment. Returns a redirect to the payment
 * gateway for paid tickets, or directly to the ticket page for free events.
 *
 * @authenticated
 * @group Checkout
 *
 * @bodyParam ticket_category_id int required The ID of the ticket category. Example: 3
 * @bodyParam quantity int required Number of tickets (1–6). Example: 2
 * @bodyParam holder_name string required Name on the ticket. Example: Jane Doe
 * @bodyParam email string required Contact email for confirmation. Example: jane@example.com
 *
 * @response 302 scenario="Free ticket booked" {}
 * @response 302 scenario="Paid ticket — redirects to MarzPay" {}
 * @response 422 scenario="Validation error" {"message": "The quantity field is required."}
 */
public function store(CheckoutRequest $request, Event $event): RedirectResponse
```

Scribe automatically reads validation rules from `CheckoutRequest` and merges them with your `@bodyParam` annotations. You do not need to repeat rules that are already in the FormRequest.

### 7.6 Regenerating Documentation

Run this command any time you add or change endpoints:

```bash
php artisan scribe:generate
```

To force a full re-extraction (ignoring the cache):

```bash
php artisan scribe:generate --force
```

The generated files are committed to the repository:
- `.scribe/` — endpoint YAML cache (commit this so teammates do not need to regenerate)
- `resources/views/scribe/index.blade.php` — the rendered docs Blade view
- `public/vendor/scribe/` — CSS and JS assets for the docs page

### 7.7 Scribe Configuration Reference

Scribe is configured in `config/scribe.php`. Key settings:

| Setting | Current Value | What It Controls |
|---------|--------------|-----------------|
| `title` | WADO Events Tickets — API & Endpoint Reference | HTML page title and OpenAPI info block |
| `type` | `laravel` | Serves docs as a Blade view via a registered `/docs` route |
| `routes[].match.prefixes` | `['*']` | Includes all routes (not just `api/*`) |
| `routes[].exclude` | `dashboard/*`, `telescope/*`, `horizon/*` | Excludes Filament admin panel, Telescope, and Horizon routes |
| `try_it_out.enabled` | `true` | Shows Send Request button on each endpoint |
| `postman.enabled` | `true` | Generates Postman collection on every run |
| `openapi.enabled` | `true` | Generates OpenAPI spec on every run |
| `example_languages` | `bash`, `javascript`, `php` | Languages shown in code sample tabs |
| `groups.order` | Authentication → Events → ... → Webhooks | Explicit sidebar ordering |
| `examples.faker_seed` | `1234` | Fixed seed so example values are consistent across runs |

---

## 8. Environment Setup

### 8.1 Local Development (Windows + XAMPP)

**Prerequisites:**
- PHP 8.2 (XAMPP)
- MySQL (XAMPP)
- Node.js + npm
- Composer
- Redis (Memurai)

**Install Redis (run PowerShell as Administrator):**
```
winget install Memurai.MemuraiDeveloper
```

Memurai installs as a Windows service and starts automatically. Verify it is running:
```
& "C:\Program Files\Memurai\memurai-cli.exe" ping
# Expected output: PONG
```

**Clone and set up the project:**
```bash
git clone https://github.com/Dev-Jera/wado-events.git
cd wado-events
composer install
cp .env.example .env
php artisan key:generate
# Edit .env with your local DB credentials
php artisan migrate
npm install
```

**Start the full development stack:**
```bash
composer dev
```

This single command starts five concurrent processes:

| Process | Command | Colour | Purpose |
|---------|---------|--------|---------|
| server | `php artisan serve` | Blue | Laravel web server on :8000 |
| queue | `php artisan queue:listen --tries=1 --timeout=0` | Purple | Background job worker |
| reverb | `php artisan reverb:start` | Green | WebSocket server on :8080 |
| logs | `php artisan pail --timeout=0` | Pink | Real-time log viewer |
| vite | `npm run dev` | Orange | Frontend asset compilation |

**Required `.env` values:**
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wado-events
DB_USERNAME=root
DB_PASSWORD=

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

QUEUE_CONNECTION=redis
CACHE_STORE=redis
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=wado-events
REVERB_APP_KEY=wado-events-key
REVERB_APP_SECRET=wado-events-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
```

### 8.2 Production (Railway)

**Required services in Railway project:**
1. Laravel app (PHP)
2. MySQL database
3. Redis

**Environment variables to set on the app service:**

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.railway.app

DB_CONNECTION=mysql
DB_HOST=<Railway MySQL host>
DB_PORT=<Railway MySQL port>
DB_DATABASE=<Railway MySQL database>
DB_USERNAME=<Railway MySQL username>
DB_PASSWORD=<Railway MySQL password>

REDIS_CLIENT=predis
REDIS_HOST=<Railway Redis host>
REDIS_PORT=<Railway Redis port>
REDIS_PASSWORD=<Railway Redis password>

QUEUE_CONNECTION=redis
CACHE_STORE=redis
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=<your app id>
REVERB_APP_KEY=<your key>
REVERB_APP_SECRET=<your secret>
REVERB_HOST=<your production domain>
REVERB_PORT=443
REVERB_SCHEME=https

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

TICKET_SIGNING_KEY=<your signing key — never change this in production>
```

**Queue worker on production:**

On the Linux production server, use Horizon instead of `queue:listen`:

```bash
php artisan horizon
```

Horizon is available at `/horizon` (super-admin only).

---

## 9. Development Workflow

### Day-to-Day Commands

```bash
# Start everything
composer dev

# Run migrations
php artisan migrate

# Regenerate API docs after changing endpoints
php artisan scribe:generate

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# View failed jobs
php artisan queue:failed

# Retry all failed jobs
php artisan queue:retry all

# Run tests
composer test
```

### Adding a New Endpoint

1. Create or update the controller method
2. Create a FormRequest if the endpoint accepts input
3. Add the route in `routes/web.php`
4. Add a `@group` docblock to the controller class if it is new
5. Add a method docblock with `@authenticated`, `@bodyParam`, `@response` as needed
6. Run `php artisan scribe:generate` to update the docs
7. Test via `/docs` Try It Out or Postman

---

## 10. Deployment Guide (Railway)

### 10.1 Initial Deployment

1. Push code to `master` branch on GitHub
2. Railway auto-deploys on push (if GitHub integration is configured)
3. After first deploy, run migrations:
   ```bash
   php artisan migrate --force
   ```
4. Create the storage symlink:
   ```bash
   php artisan storage:link
   ```

### 10.2 Subsequent Deployments

Push to `master`. Railway redeploys automatically. Migrations run automatically if configured in the Railway start command or Procfile.

### 10.3 After Deploying New Migrations

Railway runs `php artisan migrate --force` as part of the deploy if configured. Verify in the deploy logs.

### 10.4 Checking Queue Worker Health

On Railway, confirm the Horizon process is running:
- Visit `/horizon` — should show green status
- If it shows "inactive", restart the service

---

## 11. Monitoring & Observability

### Laravel Telescope (`/telescope`)

Telescope is the primary debugging and monitoring tool in development. It captures:

- Every HTTP request and its response time
- All database queries with execution time (slow query threshold: 500ms)
- All cache hits and misses
- All queued jobs and their outcomes
- All Redis commands
- All mail sent (or attempted)
- All exceptions with full stack traces

In production, Telescope is restricted to logging only exceptions, failed requests, and failed jobs. The UI is accessible only to super-admins.

### Laravel Horizon (`/horizon`)

Horizon provides queue-specific observability in production:

- Jobs processed per minute (throughput)
- Failed jobs with full payloads and error messages
- Queue depth (how many jobs are waiting)
- Worker process count and status
- Job duration percentiles

If a notification job is failing consistently, Horizon is the first place to check.

### Application Logs

**Local development:** Logs are written to `storage/logs/laravel.log`. The `pail` process in `composer dev` streams these to the terminal in real time with colour coding by level.

**On Railway:** Laravel writes to `storage/logs/laravel.log` by default, but that file lives inside the container filesystem and is not visible anywhere. To make logs visible, set this environment variable on Railway:

```
LOG_CHANNEL=stderr
```

With `stderr`, Laravel writes every log line to standard error output, which Railway captures and displays in the **Logs tab** of your service in real time. You will see every error, job failure, slow query warning, and email send as it happens. Without this setting, the Railway Logs tab shows only server startup output and nothing from the application itself.

Slow database queries (over 500ms) are automatically logged:
```
[slow query] 1234ms: SELECT * FROM payment_transactions WHERE ...
```

---

## 12. Testing Guide

### 12.1 Testing Redis Is Connected

```bash
# Check Redis is reachable
& "C:\Program Files\Memurai\memurai-cli.exe" ping
# Expected: PONG

# Test cache from Laravel
php artisan tinker
>>> Cache::put('test', 'working', 60);
>>> Cache::get('test');
# Expected: "working"

# Check which driver is active
>>> config('cache.default')
# Expected: "redis"
>>> config('queue.default')
# Expected: "redis"
```

### 12.2 Testing Queue Jobs

Start `composer dev`, then trigger a free ticket checkout. Watch the queue process label in the terminal — you should see the `SendTicketNotification` job appear and complete.

Alternatively, process one job manually:
```bash
php artisan queue:work --queue=tickets,notifications,default --once
```

Check Telescope at `/telescope` → Jobs tab to see all processed and failed jobs.

### 12.3 Testing WebSocket Connection

1. Start `composer dev`
2. Open the gate portal at `/ticket-verification` or `/gate-portal`
3. Open browser DevTools → Network → WS tab
4. A WebSocket connection to `ws://127.0.0.1:8080` should appear
5. Scan a ticket — watch the WebSocket frame appear in the DevTools WS inspector

### 12.4 Testing the API Docs

1. Start `composer dev`
2. Log in at `http://localhost:8000/login`
3. Visit `http://localhost:8000/docs`
4. All endpoint groups should appear in the sidebar
5. Click any GET endpoint → hit Send Request — should return a real response

### 12.5 Verifying Database Indexes

```bash
php artisan tinker
>>> DB::select("SHOW INDEX FROM payment_transactions");
>>> DB::select("SHOW INDEX FROM events");
```

Expected: indexes `payment_transactions_created_at_idx`, `payment_transactions_event_status_idx`, `payment_transactions_user_status_idx`, `events_status_idx`, `events_starts_at_idx`, `events_is_featured_idx` should all appear.

### 12.6 Testing Rate Limiting

Send 6 rapid POST requests to `/login` with wrong credentials. The 6th should return a `429 Too Many Requests` response.

---

## 13. Troubleshooting

### "Class Redis not found" error

**Cause:** `REDIS_CLIENT=phpredis` but the phpredis PHP extension is not installed (common on Windows/XAMPP).  
**Fix:** Change to `REDIS_CLIENT=predis` in `.env`. Ensure `predis/predis` is in `composer.json`.

### Queue jobs not processing / emails not sending

**Check 1:** Is the queue worker running? Look for the `queue` process in `composer dev` output locally. On Railway, check the Logs tab for the `Starting queue worker` line on startup.  
**Check 2:** Does the worker command include all queue names? In `start.sh`, the `--queue=` flag must list every queue your jobs use: `tickets,notifications,default`. If a job calls `$this->onQueue('refunds')` but `refunds` is not in that list, those jobs will never run.  
**Check 3:** Is `QUEUE_CONNECTION=redis` set in both `.env` and Railway Variables?  
**Check 4:** Is Redis running? Run `memurai-cli.exe ping` locally.  
**Check 5:** Check failed jobs: `php artisan queue:failed`. Jobs that failed after all retries appear here.

### Reverb WebSocket not connecting

**Check 1:** Is the reverb process running? Look for the `reverb` label in `composer dev` output.  
**Check 2:** Is `BROADCAST_CONNECTION=reverb` in `.env`?  
**Check 3:** Are the `REVERB_*` and `VITE_REVERB_*` variables set?  
**Check 4:** Check browser DevTools console for WebSocket errors.

### Docs not updating after code changes

Run `php artisan scribe:generate` again. If changes still do not appear, run with `--force`:
```bash
php artisan scribe:generate --force
```

### Logs not visible on Railway

Set `LOG_CHANNEL=stderr` in Railway → your app service → Variables. Without this, Laravel writes logs to a file inside the container that is not accessible. With `stderr`, every log line appears in the Railway Logs tab.

### Double booking occurred

Check if the database is SQLite (which does not support `lockForUpdate()`). Production must use MySQL. Check `DB_CONNECTION` in the Railway environment variables.

---

## 14. Package Reference

| Package | Version | Type | Purpose |
|---------|---------|------|---------|
| `laravel/framework` | ^12.0 | Production | Core framework |
| `filament/filament` | 5.4 | Production | Admin panel |
| `laravel/reverb` | ^1.0 | Production | WebSocket server |
| `predis/predis` | ^3.0 | Production | Pure PHP Redis client |
| `barryvdh/laravel-dompdf` | ^3.1 | Production | PDF ticket generation |
| `endroid/qr-code` | ^6.0 | Production | QR code generation |
| `getbrevo/brevo-php` | ^4.0 | Production | Email and SMS via Brevo |
| `resend/resend-laravel` | ^1.3 | Production | Email via Resend |
| `laravel/tinker` | ^2.10 | Production | REPL for debugging |
| `knuckleswtf/scribe` | ^5.9 | Dev | API documentation generator |
| `laravel/telescope` | ^5.20 | Dev | Request/query/job monitoring |
| `laravel/pail` | ^1.2 | Dev | Real-time log viewer |
| `laravel/pint` | ^1.24 | Dev | PHP code style fixer |
| `laravel/sail` | ^1.41 | Dev | Docker environment |

---

## 15. Files Changed in This Implementation

The following files were created or modified during the scaling and infrastructure work documented in this guide.

| File | Status | Description |
|------|--------|-------------|
| `composer.json` | Modified | Added Horizon, Reverb, predis, Scribe; updated dev script to include Reverb; pinned package versions |
| `composer.lock` | Modified | Updated lock file with all new dependencies |
| `config/database.php` | Modified | Changed Redis client default fallback from `phpredis` to `predis` |
| `config/horizon.php` | Existing (confirmed) | Horizon queue configuration — production workers and auto-scaling |
| `config/reverb.php` | New | Published by `reverb:install` — WebSocket server configuration |
| `config/scribe.php` | New | Scribe configuration — route matching, groups, auth description, output settings |
| `.env` | Modified | Changed `REDIS_CLIENT` from `phpredis` to `predis` |
| `.env.example` | Modified | Changed `REDIS_CLIENT` to `predis`; set `QUEUE_CONNECTION` and `CACHE_STORE` to `redis`; set `BROADCAST_CONNECTION` to `reverb`; added Reverb variable block |
| `app/Providers/HorizonServiceProvider.php` | New | Published by `horizon:install` — registers Horizon routes and gates |
| `app/Http/Controllers/AuthController.php` | Modified | Added `@group Authentication` docblock |
| `app/Http/Controllers/EventController.php` | Modified | Added `@group Events` docblock |
| `app/Http/Controllers/CheckoutController.php` | Modified | Added `@group Checkout` docblock |
| `app/Http/Controllers/TicketController.php` | Modified | Added `@group Tickets` docblock |
| `app/Http/Controllers/GatePortalController.php` | Modified | Added `@group Gate Portal` docblock |
| `app/Http/Controllers/TicketVerificationController.php` | Modified | Added `@group Ticket Verification` docblock |
| `app/Http/Controllers/PaymentController.php` | Modified | Added `@group Admin — Payments` docblock |
| `app/Http/Controllers/PaymentWebhookController.php` | Modified | Added `@group Webhooks` docblock |
| `database/migrations/2026_04_22_000002_add_indexes_to_payment_transactions_table.php` | New | Indexes: `created_at`, `(event_id, status)`, `(user_id, status)` |
| `database/migrations/2026_04_22_000003_add_indexes_to_events_table.php` | New | Indexes: `status`, `starts_at`, `is_featured` |
| `.scribe/` | New | Scribe endpoint YAML cache files |
| `resources/views/scribe/index.blade.php` | New | Generated docs Blade view |
| `public/vendor/scribe/` | New | Docs CSS and JS assets |
| `docs/scaling-and-redis-implementation.md` | New | This document |

> Section 16 lists all files added or modified in the April 2026 feature sprint.

---

## 16. Feature Additions — April 2026

This section documents every feature built in the April 2026 sprint, the reasoning behind each design decision, and how the pieces fit together.

---

### 16.1 Home Content Management — File Upload Fix

**Problem:** Content saved from the Filament `ContentManagementPage` (hero banners, package images) was not appearing on the home page. Two bugs caused this:

1. The `save()` method in `ContentManagementPage` read from `$this->data` directly. Filament's `FileUpload` component stores temporary Livewire UUIDs in `$this->data` — the actual file move to permanent storage is only triggered by `$this->form->getState()`. So uploaded files never left the temp folder.

2. `FileUpload` components defaulted to the `local` disk (maps to `storage/app/private/` in Laravel 11), which is not web-accessible. All image requests returned `403 Forbidden`.

**Fix:**
- `save()` now calls `$this->form->getState()` which triggers `saveUploadedFiles()` internally.
- All four `FileUpload` components received `->disk('public')`.
- `HomeController` and `TicketPackageController` changed from `Storage::url()` to `Storage::disk('public')->url()`.

**Files changed:**
- `app/Filament/Pages/ContentManagementPage.php`
- `app/Http/Controllers/HomeController.php`
- `app/Http/Controllers/ContentManagementController.php`

---

### 16.2 PWA Manifest

**Problem:** The app's `<link rel="manifest">` pointed to `manifest.webmanifest`, which was the scanner app's manifest (`"name": "Wado Scanner"`, `start_url` pointing to the scanner). When installed to a phone home screen from the main site, users got the scanner app identity.

**Fix:** Created a separate `public/site.webmanifest` for the main site with correct name, theme colour, start URL, and icons. Updated `app.blade.php` to point to `site.webmanifest` and added the correct `theme-color` meta tag and Apple touch icon. The scanner's own manifest (`manifest.webmanifest`) was left untouched.

**Files changed:**
- `public/site.webmanifest` — new, main site PWA manifest
- `resources/views/layouts/app.blade.php` — manifest link, theme-color, apple-touch-icon

---

### 16.3 Ticket Packages Enquiry System

**What was built:** A contact modal on the `/ticket-packages` page and an admin Enquiries dashboard.

#### Public-facing modal

When a visitor clicks "Get in Touch" on a ticket package, a modal opens pre-selecting that package in a dropdown. The form collects name, email, phone, event date, estimated attendance, and a free-text message. On submit it fires a `fetch()` to `POST /ticket-packages/enquire`. No page reload.

#### Server-side handling

`TicketPackageController::enquire()` validates the 7 fields, creates an `Enquiry` record, and sends a branded HTML email to `wadoconcepts@gmail.com` with `->replyTo()` set to the enquirer's address. Clicking "Reply" in Gmail threads directly back to the enquirer.

#### Admin Enquiries resource

`EnquiryResource` in the Filament dashboard (group: Content, sort: 55) shows all incoming enquiries. A blue badge on the sidebar item shows the unread count (`is_read = false`). Per-row actions:
- **View** — opens a modal with all enquiry details; marks the record as read.
- **Reply** — opens a textarea modal; sends a `PackageEnquiryReply` email to the enquirer and stamps `replied_at`.

Status is derived from a model accessor (`getStatusAttribute`): `New` → `Read` → `Replied`.

**Database table:** `enquiries` — `name`, `email`, `phone`, `package`, `event_date`, `attendance`, `message`, `is_read` (bool), `replied_at` (nullable timestamp).

**Files changed/created:**
- `database/migrations/2026_04_27_062052_create_enquiries_table.php`
- `app/Models/Enquiry.php`
- `app/Mail/PackageEnquiry.php`
- `app/Mail/PackageEnquiryReply.php`
- `resources/views/emails/package-enquiry.blade.php`
- `resources/views/emails/package-enquiry-reply.blade.php`
- `app/Http/Controllers/TicketPackageController.php`
- `routes/ticket-packages.php`
- `app/Filament/Resources/Enquiries/EnquiryResource.php`
- `app/Filament/Resources/Enquiries/Pages/ListEnquiries.php`
- `resources/views/filament/modals/enquiry-detail.blade.php`
- `resources/views/ticket-packages/index.blade.php`

---

### 16.4 Navbar Cleanup

Removed "My Tickets" from the profile dropdown (it already exists as a persistent navbar link for logged-in users). Removed "Ticket Packages" from the dropdown entirely — it is a public page accessible from the footer, not a user account action.

**Files changed:**
- `resources/views/components/navbar.blade.php`

---

### 16.5 Gate-Sale Bulk Ticket Printing System

This is the largest feature in this sprint. It allows admins and event owners to generate hundreds or thousands of physical, verifiable, hard-to-fake tickets for sale at the event entrance.

#### How it works end-to-end

```
Admin creates a batch (event, label, price, quantity, size)  →  status: draft
    ↓
Admin clicks "Generate" in dashboard
    ↓
GatePrintTicketService::generateTickets() runs inside a DB transaction
    — acquires lockForUpdate() on the batch row to prevent double-generation
    — generates N unique codes (format: GP-{eventId}-{8 random chars})
    — builds an HMAC-SHA256 signed JSON payload for each code
    — bulk-inserts into gate_tickets in chunks of 500
    — sets batch status → 'active', records printed_at
    ↓
Admin clicks "Print PDF"  →  GET /gate-batches/{id}/download-pdf
    ↓
GateBatchController::downloadPdf()
    — verifies HMAC of every ticket's stored payload (tamper detection)
    — generates QR PNG data URIs in-memory (not stored to disk)
    — renders PDF via dompdf from resources/views/pdf/gate-tickets.blade.php
    — streams PDF as file download
    ↓
Admin prints PDF, cuts tickets, sells at gate
    ↓
At the gate: scanner reads QR code
    ↓
TicketVerificationController::scanJson() detects type = 'gate_print'
    — delegates to scanGatePrintJson()
    — HMAC re-verified
    — Redis lock: Cache::lock('scan:gate:{code}', 10s)
    — DB transaction + lockForUpdate() on gate_tickets row
    — status → 'used', used_at + used_by stamped
    — duplicate scan caught and rejected
```

#### QR payload structure

Each gate-sale ticket encodes a signed JSON payload in its QR code:

```json
{
  "v": 1,
  "type": "gate_print",
  "code": "GP-12-ABCDEF01",
  "batch_id": 4,
  "event_id": 12,
  "sig": "<hmac-sha256>"
}
```

The `type` field is how `TicketVerificationController` routes the scan to the correct handler. The HMAC is computed over `gate_print|GP-12-ABCDEF01|12|4` using the same `TICKET_SIGNING_KEY` used for online tickets. Changing the code, event_id, or batch_id in the QR data invalidates the signature — the ticket is rejected as fake.

#### Ticket sizes

Three sizes are supported, controlled by the `ticket_size` field on the batch:

| Size | Dimensions | Per A4 page |
|------|-----------|-------------|
| small | ~90 × 42 mm | 4 across × 5 rows = 20 per page |
| standard | ~140 × 55 mm | 2 across × 4 rows = 8 per page |
| large | ~190 × 70 mm | 1 across × 3 rows = 3 per page |

The ticket design is a two-column layout: left column (65–72% width) holds event name, category label, date, venue, and price; right column is a torn-stub section with the QR code and ticket code. A red accent stripe runs across the top.

#### Security layers

| Layer | Mechanism |
|-------|----------|
| Code uniqueness | Checked against `gate_tickets.ticket_code` (unique index) before insert |
| Forgery prevention | HMAC-SHA256 signature on each QR payload; verified at both PDF generation and scan time |
| Double-scan prevention | Redis distributed lock + `lockForUpdate()` inside DB transaction |
| Double-generation prevention | `lockForUpdate()` on batch row re-checks `status = 'draft'` before generating; concurrent requests bail silently |
| PDF tamper detection | Controller re-verifies every ticket's HMAC before rendering; corrupted rows excluded |
| Access control — dashboard | `canViewAny()` restricted to `super_admin`, `admin`, `event_owner` (not gate agents) |
| Access control — query scope | Event owners' Eloquent query scoped to `event.user_id = auth()->id()` |
| Access control — actions | `generate` and `void` call `abort_unless(userCanModifyBatch($record), 403)` server-side, regardless of what the UI shows |
| Access control — PDF download | Controller checks role and event ownership; voided batches return 422 |
| Ownership on create | `mutateFormDataBeforeCreate` verifies the selected event belongs to the user before saving |

#### Database tables

**`gate_batches`**

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `event_id` | FK → events | Cascades on delete |
| `created_by` | FK → users | Who created the batch |
| `label` | varchar | e.g. "VIP", "General Entry" |
| `price` | decimal(12,2) | Face value per ticket |
| `quantity` | unsigned int | Number of tickets to generate |
| `ticket_size` | enum(small, standard, large) | Controls PDF layout |
| `notes` | text nullable | Internal notes |
| `status` | enum(draft, active, closed, voided) | Lifecycle state |
| `printed_at` | timestamp nullable | Set when generation completes |

Index: `(event_id, status)`

**`gate_tickets`**

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `batch_id` | FK → gate_batches | Cascades on delete |
| `event_id` | FK → events | Denormalised for fast lookup at scan time |
| `ticket_code` | varchar unique | e.g. `GP-12-ABCDEF01` |
| `qr_payload` | text | Signed JSON string encoded in the QR code |
| `status` | enum(unprinted, sold, used, void) | Lifecycle state |
| `sold_at` | timestamp nullable | When ticket was sold at gate |
| `used_at` | timestamp nullable | When QR was scanned for entry |
| `used_by` | FK → users nullable | Gate agent who scanned it |

Indexes: `(batch_id, status)`, `(event_id, status)`

**Files created:**
- `database/migrations/2026_04_27_064640_create_gate_batches_table.php`
- `database/migrations/2026_04_27_064645_create_gate_tickets_table.php`
- `app/Models/GateBatch.php`
- `app/Models/GateTicket.php`
- `app/Services/GatePrint/GatePrintTicketService.php`
- `app/Filament/Resources/GateBatches/GateBatchResource.php`
- `app/Filament/Resources/GateBatches/Pages/ListGateBatches.php`
- `app/Filament/Resources/GateBatches/Pages/CreateGateBatch.php`
- `app/Http/Controllers/GateBatchController.php`
- `resources/views/pdf/gate-tickets.blade.php`

**Files modified:**
- `app/Http/Controllers/TicketVerificationController.php` — added `GatePrintTicketService` injection and `scanGatePrintJson()` method; routed `type=gate_print` payloads to it
- `routes/web.php` — added `GET /gate-batches/{batchId}/download-pdf`

---

### 16.6 Admin Panel Primary Color

The Filament admin panel primary color was changed from amber/brown (`#f8b26a`) to blue (`#2563eb`). This affects all primary buttons, active sidebar states, badge highlights, and form focus rings.

**File changed:**
- `app/Providers/Filament/AdminPanelProvider.php`

---

### 16.7 Deployment Script Updates (`start.sh`)

The Railway startup script was updated to harden deployments:

| Change | Why |
|--------|-----|
| Added `php artisan cache:clear` before rebuilding caches | Prevents stale Redis application cache (old event data, old settings) from persisting across deploys |
| Added `php artisan filament:cache-components` | Caches Filament's resource/page/widget discovery so the admin panel does not scan the filesystem on every request in production |
| Added `php artisan queue:restart` | Signals any lingering queue worker to stop cleanly after its current job, preventing stale autoloader issues from a prior container |
| Added inline comments explaining the `--queue=` flag | Makes it clear that any new job class's queue name must be added here or its jobs will never run |
