const CACHE_NAME = 'belcare-cache-v1';
const API_CACHE_NAME = 'belcare-api-cache-v1';

// Assets to cache on install
const urlsToCache = [
    '/',
    '/offline.html',
    '/css/app.css',
    '/js/app.js',
    '/manifest.json',
    '/logo.png'
];

// Install event - cache static assets
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
    );
});

// Fetch event - serve from cache when offline
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);
    
    // Handle API requests
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    // Clone the response before caching
                    const responseToCache = response.clone();
                    
                    caches.open(API_CACHE_NAME)
                        .then(cache => {
                            // Cache API responses for 1 hour
                            cache.put(event.request, responseToCache);
                        });
                    
                    return response;
                })
                .catch(() => {
                    // If offline, return cached API response
                    return caches.match(event.request);
                })
        );
    } 
    // Handle static assets and pages
    else {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    return caches.match(event.request)
                        .then(response => {
                            if (response) {
                                return response;
                            }
                            // If not in cache, return offline page for navigation requests
                            if (event.request.mode === 'navigate') {
                                return caches.match('/offline.html');
                            }
                        });
                })
        );
    }
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME, API_CACHE_NAME];
    
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});
