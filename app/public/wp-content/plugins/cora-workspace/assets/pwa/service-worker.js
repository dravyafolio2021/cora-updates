// Service worker for Cora Admin PWA
const CACHE_NAME = 'cora-workspace-v5';
const URLs_TO_CACHE = [
  '/wp-content/plugins/cora-workspace/assets/pwa/manifest.json',
  '/cora-offline.html'
];

self.addEventListener('install', event => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      // Pre-cache vital resources including the offline page
      return cache.addAll(URLs_TO_CACHE);
    })
  );
});

self.addEventListener('fetch', event => {
  // Only handle GET requests from the same origin
  if (event.request.method !== 'GET' || !event.request.url.startsWith(self.location.origin)) {
    return;
  }

  // Bypass service worker completely for public Canvas site URLs
  if (event.request.url.includes('/site/')) {
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

  // Cache-First strategy for other static assets
  event.respondWith(
    caches.match(event.request).then(response => {
      return response || fetch(event.request).then(networkResponse => {
        if (networkResponse.status === 200) {
          const copy = networkResponse.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(event.request, copy));
        }
        return networkResponse;
      });
    })
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keyList => {
      return Promise.all(keyList.map(key => {
        if (key !== CACHE_NAME) {
          return caches.delete(key);
        }
      }));
    }).then(() => self.clients.claim())
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

