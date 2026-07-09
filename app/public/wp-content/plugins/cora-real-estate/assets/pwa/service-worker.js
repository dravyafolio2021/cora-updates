// Simple service worker for Cora Admin PWA
const CACHE_NAME = 'cora-admin-v2';
const URLs_TO_CACHE = [
  '/',
  '/workspace/dashboard',
  '/wp-content/plugins/cora-real-estate/assets/pwa/manifest.json'
];

self.addEventListener('install', event => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(URLs_TO_CACHE);
    })
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response => {
      // If found in cache, return it. Otherwise fetch from network.
      return response || fetch(event.request);
    })
  );
});

self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(keyList => {
      return Promise.all(keyList.map(key => {
        if (!cacheWhitelist.includes(key)) {
          return caches.delete(key);
        }
      }));
    }).then(() => self.clients.claim())
  );
});
