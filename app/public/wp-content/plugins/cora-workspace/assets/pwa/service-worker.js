// Service worker for Cora Admin PWA — High Performance Engine
const CORA_VERSION = '%%VERSION%%';
const CACHE_NAME = 'cora-workspace-v' + CORA_VERSION;
const DYNAMIC_CACHE = 'cora-dynamic-v' + CORA_VERSION;
const FONT_CACHE = 'cora-fonts-v' + CORA_VERSION;
const MAX_DYNAMIC_CACHE_ITEMS = 200;

const STATIC_ASSETS = [
  '/cora-manifest.json?v=' + CORA_VERSION,
  '%%PLUGIN_URL%%assets/pwa/icon_192.png?v=' + CORA_VERSION,
  '%%PLUGIN_URL%%assets/pwa/icon_512.png?v=' + CORA_VERSION,
  '%%PLUGIN_URL%%assets/images/cora-favicon.png?v=' + CORA_VERSION,
  '%%PLUGIN_URL%%assets/images/apple-touch-icon.png?v=' + CORA_VERSION,
  '%%PLUGIN_URL%%assets/css/tailwind-built.css?v=' + CORA_VERSION,
  '%%PLUGIN_URL%%assets/css/admin-style.css?v=' + CORA_VERSION,
  '%%PLUGIN_URL%%assets/js/admin-script.js?v=' + CORA_VERSION,
  '%%PLUGIN_URL%%assets/js/cora-autosave-engine.js?v=' + CORA_VERSION,
  '/cora-offline.html'
];

async function trimCache(cacheName, maxItems) {
  try {
    const cache = await caches.open(cacheName);
    const keys = await cache.keys();
    if (keys.length > maxItems) {
      const toDelete = keys.slice(0, keys.length - maxItems);
      await Promise.all(toDelete.map(key => cache.delete(key)));
    }
  } catch (e) {
    // Ignore cache trim errors
  }
}

// ─── Install: Precache critical static shell assets ──────────────────────────
self.addEventListener('install', event => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(STATIC_ASSETS).catch(err => {
        console.warn('Cora PWA precache non-fatal warning:', err);
      });
    })
  );
});

// ─── Activate: Purge old cache versions immediately ──────────────────────────
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keyList => {
      return Promise.all(
        keyList.map(key => {
          if (key !== CACHE_NAME && key !== DYNAMIC_CACHE && key !== FONT_CACHE) {
            return caches.delete(key);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// ─── Message handler ────────────────────────────────────────────────────────
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'skipWaiting') {
    self.skipWaiting();
  }
});

// ─── Fetch Interceptor ──────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
  const req = event.request;

  // 1. Ignore non-GET requests and non-http(s) schemas
  if (req.method !== 'GET' || !req.url.startsWith('http')) {
    return;
  }

  // 2. Bypass service worker for dynamic API / AJAX calls
  if (req.url.includes('/wp-admin/admin-ajax.php') || req.url.includes('/wp-json/') || req.url.includes('/api/')) {
    return;
  }

  // 3. Bypass for public canvas site editor preview
  if (req.url.includes('/site/')) {
    return;
  }

  const url = new URL(req.url);

  // 4. Google Fonts (Inter, JetBrains Mono) -> Cache-First Strategy
  if (url.origin === 'https://fonts.googleapis.com' || url.origin === 'https://fonts.gstatic.com') {
    event.respondWith(
      caches.open(FONT_CACHE).then(async cache => {
        const cached = await cache.match(req);
        if (cached) return cached;
        try {
          const networkRes = await fetch(req);
          if (networkRes.status === 200) {
            cache.put(req, networkRes.clone());
          }
          return networkRes;
        } catch (e) {
          return cached;
        }
      })
    );
    return;
  }

  // 5. Static Core CSS / JS / Assets -> Stale-While-Revalidate (Instant Return)
  if (/\.(css|js|woff2?|ttf|otf|eot|png|jpg|jpeg|svg|webp|ico)(\?.*)?$/i.test(url.pathname)) {
    event.respondWith(
      caches.open(DYNAMIC_CACHE).then(async cache => {
        const cached = (await cache.match(req)) || (await caches.match(req));
        
        const fetchPromise = fetch(req)
          .then(networkRes => {
            if (networkRes.status === 200) {
              cache.put(req, networkRes.clone());
              trimCache(DYNAMIC_CACHE, MAX_DYNAMIC_CACHE_ITEMS);
            }
            return networkRes;
          })
          .catch(() => {
            // Network failure — cached response handles it
          });

        return cached || fetchPromise;
      })
    );
    return;
  }

  // 6. HTML Navigation (Workspace Dashboard & Views) -> Robust Network-First with Offline Fallback
  if (req.headers.get('accept')?.includes('text/html') || req.mode === 'navigate') {
    event.respondWith(
      fetch(req)
        .then(async networkRes => {
          if (networkRes && networkRes.status === 200) {
            const cacheCopy = networkRes.clone();
            const cache = await caches.open(CACHE_NAME);
            cache.put(req, cacheCopy);
          }
          return networkRes;
        })
        .catch(async () => {
          const cached = (await caches.match(req)) || (await caches.match('/cora-offline.html'));
          return cached || new Response('<h1>Offline</h1>', { headers: { 'Content-Type': 'text/html' } });
        })
    );
    return;
  }
});

// ─── Push Notifications ─────────────────────────────────────────────────────
self.addEventListener('push', event => {
  const params = new URLSearchParams(self.location.search);
  const token = params.get('token') || '';
  
  event.waitUntil(
    fetch('/wp-json/cora-pwa/v1/get-notification?token=' + token)
      .then(res => res.status === 200 ? res.json() : null)
      .then(data => {
        if (data && data.success && data.notification) {
          const notif = data.notification;
          return self.registration.showNotification(notif.title, {
            body: notif.body,
            icon: notif.icon || ('%%PLUGIN_URL%%assets/pwa/icon_192.png?v=' + CORA_VERSION),
            badge: notif.badge || ('%%PLUGIN_URL%%assets/pwa/icon_192.png?v=' + CORA_VERSION),
            data: { url: notif.url || '/workspace/dashboard' }
          });
        }
      })
      .catch(() => {})
  );
});

self.addEventListener('notificationclick', event => {
  event.notification.close();
  const urlToOpen = event.notification.data?.url || '/workspace/dashboard';
  
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
      for (let i = 0; i < windowClients.length; i++) {
        const client = windowClients[i];
        if (client.url.startsWith(self.location.origin) && 'focus' in client) {
          return client.navigate(urlToOpen).then(c => c.focus());
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(urlToOpen);
      }
    })
  );
});
