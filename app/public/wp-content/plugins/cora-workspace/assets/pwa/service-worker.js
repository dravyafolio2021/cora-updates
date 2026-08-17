// Service worker for Cora Admin PWA
const CACHE_NAME = 'cora-workspace-v3.4.71';
const DYNAMIC_CACHE = 'cora-dynamic-v3.4.71';
const MAX_DYNAMIC_CACHE_ITEMS = 150;

const URLs_TO_CACHE = [
  '/wp-content/plugins/cora-workspace/assets/pwa/manifest.json',
  '/wp-content/plugins/cora-workspace/assets/pwa/icon_192.png',
  '/wp-content/plugins/cora-workspace/assets/pwa/icon_512.png',
  '/wp-content/plugins/cora-workspace/assets/images/cora-favicon.png',
  '/wp-content/plugins/cora-workspace/assets/images/apple-touch-icon.png',
  '/wp-content/plugins/cora-workspace/assets/css/admin-style.css',
  '/wp-content/plugins/cora-workspace/assets/js/admin-script.js',
  '/wp-content/plugins/cora-workspace/assets/js/cora-autosave-engine.js',
  '/cora-offline.html'
];

/**
 * Trim the dynamic cache to MAX_DYNAMIC_CACHE_ITEMS by evicting the oldest
 * entries first (FIFO). Called after every dynamic cache write.
 */
async function trimCache(cacheName, maxItems) {
  const cache = await caches.open(cacheName);
  const keys = await cache.keys();
  if (keys.length > maxItems) {
    // Delete the oldest entries until we're at the limit
    const toDelete = keys.slice(0, keys.length - maxItems);
    await Promise.all(toDelete.map(key => cache.delete(key)));
  }
}

/**
 * Stale-While-Revalidate: serve from cache immediately, then fetch a fresh
 * copy in the background and update the cache for next time.
 */
function staleWhileRevalidate(event) {
  event.respondWith(
    caches.open(DYNAMIC_CACHE).then(cache => {
      return cache.match(event.request).then(cachedResponse => {
        const fetchPromise = fetch(event.request).then(networkResponse => {
          if (networkResponse.status === 200) {
            cache.put(event.request, networkResponse.clone());
            trimCache(DYNAMIC_CACHE, MAX_DYNAMIC_CACHE_ITEMS);
          }
          return networkResponse;
        }).catch(() => {
          // Network failed — cachedResponse (if any) was already returned
        });

        // Return the cached version immediately, or wait for network
        return cachedResponse || fetchPromise;
      });
    })
  );
}

// ─── Install ────────────────────────────────────────────────────────────────
self.addEventListener('install', event => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      // Pre-cache vital resources including the offline page
      return cache.addAll(URLs_TO_CACHE);
    })
  );
});

// ─── Activate ───────────────────────────────────────────────────────────────
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keyList => {
      return Promise.all(keyList.map(key => {
        if (key !== CACHE_NAME && key !== DYNAMIC_CACHE) {
          return caches.delete(key);
        }
      }));
    }).then(() => self.clients.claim())
  );
});

// ─── Message (skipWaiting on demand) ────────────────────────────────────────
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'skipWaiting') {
    self.skipWaiting();
  }
});

// ─── Fetch ──────────────────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
  // Only handle GET requests from the same origin
  if (event.request.method !== 'GET' || !event.request.url.startsWith(self.location.origin)) {
    return;
  }

  // Bypass service worker completely for public Canvas site URLs
  if (event.request.url.includes('/site/')) {
    return;
  }

  // Network-Only for AJAX / REST API requests (never cache)
  if (event.request.url.includes('/wp-admin/admin-ajax.php') || event.request.url.includes('/wp-json/')) {
    return;
  }

  // Stale-While-Revalidate for CSS, JS, and font assets
  const url = new URL(event.request.url);
  if (/\.(css|js|woff2?|ttf|otf|eot)(\?.*)?$/i.test(url.pathname)) {
    staleWhileRevalidate(event);
    return;
  }

  // Network-First strategy for HTML pages with Server Error & Offline fallback
  if (event.request.headers.get('accept')?.includes('text/html')) {
    event.respondWith(
      fetch(event.request)
        .then(response => {
          // If response is successful, update cache copy and return it
          if (response.status === 200) {
            const copy = response.clone();
            caches.open(CACHE_NAME).then(cache => cache.put(event.request, copy));
            return response;
          }
          
          // If we got a server error (e.g., 502 Bad Gateway, 503 Service Unavailable, 504 Timeout)
          if (response.status >= 500) {
            return caches.match(event.request).then(cachedResponse => {
              return cachedResponse || caches.match('/cora-offline.html');
            });
          }
          
          return response;
        })
        .catch(() => {
          // Network connection dropped, DNS name not resolved, or completely offline
          return caches.match(event.request).then(cachedResponse => {
            return cachedResponse || caches.match('/cora-offline.html');
          });
        })
    );
    return;
  }

  // Cache-First strategy for other static assets (images, etc.)
  event.respondWith(
    caches.match(event.request).then(response => {
      return response || fetch(event.request).then(networkResponse => {
        if (networkResponse.status === 200) {
          const copy = networkResponse.clone();
          caches.open(DYNAMIC_CACHE).then(cache => {
            cache.put(event.request, copy);
            trimCache(DYNAMIC_CACHE, MAX_DYNAMIC_CACHE_ITEMS);
          });
        }
        return networkResponse;
      });
    })
  );
});

// Push event listener for dynamic Push-to-Pull notification delivery
self.addEventListener('push', event => {
  const params = new URLSearchParams(self.location.search);
  const token = params.get('token') || '';
  
  event.waitUntil(
    fetch('/wp-json/cora-pwa/v1/get-notification?token=' + token)
      .then(response => {
        if (response.status !== 200) {
          throw new Error('Failed to fetch notification data');
        }
        return response.json();
      })
      .then(data => {
        if (data && data.success && data.notification) {
          const notif = data.notification;
          return self.registration.showNotification(notif.title, {
            body: notif.body,
            icon: notif.icon || '/wp-content/plugins/cora-workspace/assets/pwa/icon_192.png',
            badge: notif.badge || '/wp-content/plugins/cora-workspace/assets/pwa/icon_192.png',
            data: {
              url: notif.url || '/workspace/dashboard'
            }
          });
        }
      })
      .catch(err => {
        console.error('Error fetching notification content:', err);
      })
  );
});

// Handle notification click: focus or open workspace window
self.addEventListener('notificationclick', event => {
  event.notification.close();
  const urlToOpen = event.notification.data?.url || '/workspace/dashboard';
  
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true })
      .then(windowClients => {
        // Find existing workspace window and navigate/focus it
        for (let i = 0; i < windowClients.length; i++) {
          const client = windowClients[i];
          if (client.url.includes('/workspace/') && 'focus' in client) {
            return client.navigate(urlToOpen).then(c => c.focus());
          }
        }
        // Otherwise open a new tab/window
        if (clients.openWindow) {
          return clients.openWindow(urlToOpen);
        }
      })
  );
});
