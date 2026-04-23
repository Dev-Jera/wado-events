# WADO Tickets — Credentials & Setup Checklist

This document lists every secret and credential the platform needs, where to get each one, and whether it is already configured locally and on Railway.

**Status key:**
- ✅ Done
- ⚠️ Needs action
- ⏳ Waiting (depends on something else first)

---

## 1. Core App

| Variable | Local (.env) | Railway | Notes |
|----------|-------------|---------|-------|
| `APP_KEY` | ✅ | ✅ | Auto-generated. Never change on production — breaks all sessions. |
| `APP_URL` | ⚠️ Still `http://localhost` | ✅ Set to Railway domain | Change locally to `http://127.0.0.1:8000` |
| `APP_ENV` | `local` | ✅ Should be `production` | |
| `APP_DEBUG` | `true` | ✅ Must be `false` | Never enable debug on production — exposes code. |
| `TICKET_SIGNING_KEY` | ✅ | ✅ | Auto-generated. Never change — invalidates all existing QR codes. |

---

## 2. Database (MySQL)

| Variable | Local | Railway | Notes |
|----------|-------|---------|-------|
| `DB_HOST` | ✅ `127.0.0.1` | ✅ Set by Railway MySQL service | |
| `DB_PORT` | ✅ `3306` | ✅ | |
| `DB_DATABASE` | ✅ `wado-events` | ✅ | |
| `DB_USERNAME` | ✅ `root` | ✅ | |
| `DB_PASSWORD` | ✅ (empty locally) | ✅ Set by Railway | |

---

## 3. Redis

| Variable | Local | Railway | Notes |
|----------|-------|---------|-------|
| `REDIS_HOST` | ✅ `127.0.0.1` (Memurai) | ✅ Set by Railway Redis service | |
| `REDIS_PORT` | ✅ `6379` | ✅ | |
| `REDIS_PASSWORD` | ✅ `null` | ✅ Set by Railway | |
| `REDIS_CLIENT` | ✅ `predis` | ✅ | |

---

## 4. Session & Logging

| Variable | Local | Railway | Notes |
|----------|-------|---------|-------|
| `SESSION_DRIVER` | `file` | ⚠️ Must be `redis` | File sessions fail on Railway — set this in Railway Variables. |
| `LOG_CHANNEL` | `stack` | ⚠️ Must be `stderr` | Without this, Railway Logs tab shows nothing from the app. |

**Action needed on Railway:** Add these two variables if not already done:
```
SESSION_DRIVER=redis
LOG_CHANNEL=stderr
```

---

## 5. Email — Brevo

| Variable | Local | Railway | Notes |
|----------|-------|---------|-------|
| `BREVO_API_KEY` | ⚠️ Not set locally | ✅ Set on Railway | Used for ticket confirmation emails and password reset emails. Get from Brevo → API keys & MCP. |
| `MAIL_USERNAME` | ✅ `a8c070001@smtp-brevo.com` | ✅ | Brevo SMTP login — found in Brevo → SMTP & API → SMTP tab. |
| `MAIL_PASSWORD` | ✅ Set | ✅ Set | Brevo SMTP key (starts with `xsmtpsib-`). |
| `MAIL_FROM_ADDRESS` | ✅ `wadoconcepts@gmail.com` | ✅ | Must be a verified sender in Brevo. |

**Note:** `BREVO_API_KEY` should also be set locally if you want to test password reset and ticket emails in local dev.

---

## 6. SMS — Africa's Talking

| Variable | Local | Railway | Notes |
|----------|-------|---------|-------|
| `AFRICASTALKING_USERNAME` | ⚠️ `sandbox` (test only) | ⚠️ Not set | Change to your live username from africastalking.com → Settings. |
| `AFRICASTALKING_API_KEY` | ⚠️ Placeholder | ⚠️ Not set | Get from africastalking.com → Settings → API Key. |
| `AFRICASTALKING_FROM` | ⚠️ Empty | ⚠️ Not set | Your registered sender ID (e.g. `WADO`). Apply for one in the AT dashboard. |

**Where to get:** [africastalking.com](https://africastalking.com) → Create account → Dashboard → Settings → API Key.

---

## 7. Payments — MarzePay

| Variable | Local | Railway | Notes |
|----------|-------|---------|-------|
| `MARZEPAY_API_KEY` | ✅ Set | ✅ | |
| `MARZEPAY_API_SECRET` | ✅ Set | ✅ | |
| `MARZEPAY_WEBHOOK_SECRET` | ⚠️ Still placeholder `your_marzpay_webhook_secret` | ⚠️ Check Railway | Get from MarzPay dashboard → Webhook settings → Signing secret. Must match exactly on both sides. |

**Action needed:** Replace `your_marzpay_webhook_secret` in local `.env` with the real value from MarzPay dashboard, and verify it matches on Railway.

---

## 8. WebSockets — Reverb

| Variable | Local | Railway | Notes |
|----------|-------|---------|-------|
| `REVERB_APP_ID` | ✅ `wado-events` | ✅ | You chose these values — they just need to match on server and JS client. |
| `REVERB_APP_KEY` | ✅ `wado-events-key` | ✅ | |
| `REVERB_APP_SECRET` | ✅ `wado-events-secret` | ✅ | Consider changing to something more random on production. |
| `REVERB_HOST` | ✅ `127.0.0.1` | ✅ Set to Railway domain | |
| `REVERB_PORT` | ✅ `8080` | ✅ | |

---

## 9. Google OAuth (Social Login)

| Variable | Local | Railway | Status |
|----------|-------|---------|--------|
| `GOOGLE_CLIENT_ID` | ⚠️ Not set | ⚠️ Not set | ⏳ Waiting — need to create credentials |
| `GOOGLE_CLIENT_SECRET` | ⚠️ Not set | ⚠️ Not set | ⏳ Waiting |
| `GOOGLE_REDIRECT_URI` | ⚠️ Not set | ⚠️ Not set | ⏳ Waiting |

**How to get:**
1. Go to [console.cloud.google.com](https://console.cloud.google.com)
2. Create a project (or use existing)
3. APIs & Services → Credentials → Create OAuth 2.0 Client ID
4. Application type: **Web application**
5. Authorised redirect URI: `https://your-domain.com/auth/google/callback`
6. Copy Client ID and Client Secret

**Add to Railway:**
```
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=https://your-domain.com/auth/google/callback
```

---

## 10. Facebook OAuth (Social Login)

| Variable | Local | Railway | Status |
|----------|-------|---------|--------|
| `FACEBOOK_CLIENT_ID` | ⚠️ Not set | ⚠️ Not set | ⏳ Waiting — Facebook verification pending |
| `FACEBOOK_CLIENT_SECRET` | ⚠️ Not set | ⚠️ Not set | ⏳ Waiting |
| `FACEBOOK_REDIRECT_URI` | ⚠️ Not set | ⚠️ Not set | ⏳ Waiting |

**Note:** Facebook login currently shows a "Coming Soon" modal to users. The code is ready — it just needs credentials once the Facebook app is verified.

**How to get (when ready):**
1. Go to [developers.facebook.com](https://developers.facebook.com)
2. Create App → Consumer type
3. Add Facebook Login product
4. Settings → Valid OAuth Redirect URIs: `https://your-domain.com/auth/facebook/callback`
5. Copy App ID and App Secret

---

## 11. Cloudflare Turnstile (Bot Protection)

| Variable | Local | Railway | Status |
|----------|-------|---------|--------|
| `TURNSTILE_SITE_KEY` | ⚠️ Not set | ⚠️ Not set | ⏳ Waiting — need Cloudflare account + custom domain |
| `TURNSTILE_SECRET_KEY` | ⚠️ Not set | ⚠️ Not set | ⏳ Waiting |

**Note:** Checkout and register forms silently skip Turnstile validation when these keys are empty — nothing breaks without them. Activate once you have a custom domain and Cloudflare account.

**How to get:**
1. cloudflare.com → Turnstile → Add site
2. Enter your domain, choose **Managed** widget type
3. Copy Site Key and Secret Key

---

## 12. File Storage — AWS S3 (Optional, Not Active)

| Variable | Status | Notes |
|----------|--------|-------|
| `AWS_ACCESS_KEY_ID` | ⚠️ Not set | Not needed yet — files stored locally. Set up when moving ticket QR codes and event images to S3. |
| `AWS_SECRET_ACCESS_KEY` | ⚠️ Not set | |
| `AWS_BUCKET` | ⚠️ Not set | |

**Note:** `FILESYSTEM_DISK=local` currently — files live on the server. This works fine until you scale to multiple Railway instances or need persistent file storage.

---

## Summary — What to do next

### Immediate (affects production today)
- [ ] Add `SESSION_DRIVER=redis` to Railway Variables
- [ ] Add `LOG_CHANNEL=stderr` to Railway Variables
- [ ] Fix `MARZEPAY_WEBHOOK_SECRET` in local `.env` and Railway (replace placeholder with real value from MarzPay dashboard)
- [ ] Add `BREVO_API_KEY` to local `.env` for local dev testing

### When you get a custom domain
- [ ] Set `APP_URL` to your real domain on Railway
- [ ] Set up Cloudflare (proxy, DDoS, bot protection)
- [ ] Create Turnstile widget → add `TURNSTILE_SITE_KEY` and `TURNSTILE_SECRET_KEY` to Railway
- [ ] Create Google OAuth credentials → add to Railway
- [ ] Set up Reverb host to your domain on Railway

### When ready for SMS
- [ ] Create Africa's Talking account
- [ ] Register a sender ID (`WADO` or similar)
- [ ] Add `AFRICASTALKING_USERNAME`, `AFRICASTALKING_API_KEY`, `AFRICASTALKING_FROM` to Railway

### When ready for Facebook login
- [ ] Complete Facebook app verification
- [ ] Add `FACEBOOK_CLIENT_ID`, `FACEBOOK_CLIENT_SECRET`, `FACEBOOK_REDIRECT_URI` to Railway
