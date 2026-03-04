// Log Mieru – Service Worker
// Strategy: Cache-First for static assets, Network-First for pages/API

const CACHE_NAME    = 'logmieru-v1';
const OFFLINE_URL   = '/offline.html';

// Static shell to pre-cache on install
const PRECACHE_URLS = [
    '/offline.html',
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
];

// ── Install: pre-cache shell ───────────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

// ── Activate: remove old caches ────────────────────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
            )
        ).then(() => self.clients.claim())
    );
});

// ── Fetch strategy ─────────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET, cross-origin, and browser-extension requests
    if (request.method !== 'GET') return;
    if (url.origin !== location.origin) return;

    // API / JSON data endpoints → Network-First (never serve stale data)
    const isApiCall = url.pathname.includes('/data') ||
                      url.pathname.includes('/events') ||
                      url.pathname.includes('/upcoming') ||
                      url.pathname.includes('/list') ||
                      url.pathname.includes('/presence') ||
                      url.pathname.startsWith('/logbooks') ||
                      url.pathname.startsWith('/notifications');

    if (isApiCall) {
        event.respondWith(networkFirst(request));
        return;
    }

    // Static assets (CSS, JS, fonts, images) → Cache-First
    const isStatic = url.pathname.startsWith('/assets/') ||
                     url.pathname.startsWith('/icons/') ||
                     url.pathname.startsWith('/build/') ||
                     /\.(css|js|woff2?|ttf|otf|png|jpg|jpeg|gif|svg|ico|webp)$/.test(url.pathname);

    if (isStatic) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // App pages (HTML) → Network-First, fallback to offline page
    event.respondWith(networkFirstWithOfflineFallback(request));
});

// ── Strategies ─────────────────────────────────────────────────────────────

async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return new Response('Asset not available offline', { status: 503 });
    }
}

async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        return cached || new Response(JSON.stringify({ error: 'offline' }), {
            status: 503,
            headers: { 'Content-Type': 'application/json' },
        });
    }
}

async function networkFirstWithOfflineFallback(request) {
    try {
        const response = await fetch(request);
        return response;
    } catch {
        const cached = await caches.match(request);
        if (cached) return cached;
        return caches.match(OFFLINE_URL);
    }
}

// ── Push notifications (future-ready) ─────────────────────────────────────
self.addEventListener('push', event => {
    if (!event.data) return;
    const data = event.data.json();
    event.waitUntil(
        self.registration.showNotification(data.title || 'Log Mieru', {
            body:  data.body  || '',
            icon:  '/icons/icon-192x192.png',
            badge: '/icons/icon-72x72.png',
            data:  { url: data.url || '/dashboard' },
        })
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(windowClients => {
            const target = event.notification.data?.url || '/dashboard';
            for (const client of windowClients) {
                if (client.url === target && 'focus' in client) return client.focus();
            }
            return clients.openWindow(target);
        })
    );
});
