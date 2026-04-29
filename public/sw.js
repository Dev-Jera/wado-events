const CACHE_VERSION = 'wado-v2';
const SHELL_CACHE   = CACHE_VERSION + '-shell';
const IMAGE_CACHE   = CACHE_VERSION + '-images';
const PAGE_CACHE    = CACHE_VERSION + '-pages';

// Pre-cached at install time
const SHELL_ASSETS = [
  '/offline.html',
  '/images/logos/logo-no-bg.png',
  '/site.webmanifest',
];

// ── Install: pre-cache the app shell ────────────────────────────────────────
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(SHELL_CACHE)
      .then((cache) => cache.addAll(SHELL_ASSETS))
      .catch(() => null)
  );
  self.skipWaiting();
});

// ── Activate: purge old caches ───────────────────────────────────────────────
self.addEventListener('activate', (event) => {
  const currentCaches = [SHELL_CACHE, IMAGE_CACHE, PAGE_CACHE];
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => !currentCaches.includes(key))
          .map((key) => caches.delete(key))
      )
    )
  );
  self.clients.claim();
});

// ── Fetch ────────────────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Never intercept non-GET, cross-origin, admin, or API requests
  if (
    request.method !== 'GET' ||
    url.origin !== self.location.origin ||
    url.pathname.startsWith('/admin') ||
    url.pathname.startsWith('/broadcasting') ||
    url.pathname.startsWith('/payments/') ||
    url.pathname.startsWith('/livewire')
  ) {
    return;
  }

  // Vite build assets (/build/**) — cache-first, they're content-hashed
  if (url.pathname.startsWith('/build/')) {
    event.respondWith(cacheFirst(request, SHELL_CACHE));
    return;
  }

  // Images — cache-first with a size-limited image cache
  if (/\.(png|jpe?g|gif|webp|svg|ico)$/i.test(url.pathname)) {
    event.respondWith(cacheFirst(request, IMAGE_CACHE));
    return;
  }

  // HTML pages — network-first, fall back to cache, then offline page
  // Note: Google Fonts are cross-origin and exit above; the browser caches
  // them natively via their long-lived Cache-Control headers — no SW needed.
  if (request.headers.get('Accept')?.includes('text/html')) {
    event.respondWith(networkFirstWithOfflineFallback(request));
    return;
  }

  // Everything else — network-first, cache on success
  event.respondWith(networkFirst(request, PAGE_CACHE));
});

// ── Strategies ───────────────────────────────────────────────────────────────

async function cacheFirst(request, cacheName) {
  const cached = await caches.match(request);
  if (cached) return cached;

  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(cacheName);
      cache.put(request, response.clone()).catch(() => null);
    }
    return response;
  } catch {
    return new Response('', { status: 503 });
  }
}

async function networkFirst(request, cacheName) {
  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(cacheName);
      cache.put(request, response.clone()).catch(() => null);
    }
    return response;
  } catch {
    const cached = await caches.match(request);
    return cached ?? new Response('', { status: 503 });
  }
}

async function networkFirstWithOfflineFallback(request) {
  try {
    const response = await fetch(request);
    if (response.ok) {
      const cache = await caches.open(PAGE_CACHE);
      cache.put(request, response.clone()).catch(() => null);
    }
    return response;
  } catch {
    const cached = await caches.match(request);
    if (cached) return cached;

    // Show the offline page for any uncached HTML request
    const offlinePage = await caches.match('/offline.html');
    return offlinePage ?? new Response('<h1>You are offline</h1>', {
      headers: { 'Content-Type': 'text/html' },
      status: 503,
    });
  }
}
