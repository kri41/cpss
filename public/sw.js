const CACHE_NAME = 'dataraga-v1';
const OFFLINE_URL = '/offline.html';

// Aset yang di-cache saat install
const PRECACHE_URLS = [
    '/',
    '/prasarana',
    '/clubs',
    '/events',
    '/offline.html',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_URLS).catch(() => {
                // Abaikan error individual (misal offline saat install)
            });
        }).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        }).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    // Hanya handle GET request
    if (event.request.method !== 'GET') return;

    // Skip non-http requests
    if (!event.request.url.startsWith('http')) return;

    // Skip API / admin routes — selalu fetch dari network
    const url = new URL(event.request.url);
    if (url.pathname.startsWith('/api/') || url.pathname.includes('/export')) return;

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Clone dan simpan ke cache jika sukses
                if (response && response.status === 200 && response.type === 'basic') {
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                }
                return response;
            })
            .catch(() => {
                // Offline: coba dari cache, fallback ke halaman offline
                return caches.match(event.request).then((cached) => {
                    if (cached) return cached;
                    if (event.request.destination === 'document') {
                        return caches.match(OFFLINE_URL);
                    }
                });
            })
    );
});
