// LRS Service Worker — PWA Offline Cache
// Version: 1.0.0

var CACHE_NAME = 'lrs-v3.4';
var CACHE_URLS = [
  './',
  './index.php',
  'https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700;800&family=Sarabun:wght@300;400;500;600;700&display=swap',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css'
];

self.addEventListener('install', function(event) {
  event.waitUntil(
    caches.open(CACHE_NAME).then(function(cache) {
      return cache.addAll(CACHE_URLS).catch(function() {});
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', function(event) {
  event.waitUntil(
    caches.keys().then(function(keys) {
      return Promise.all(keys.filter(function(k) { return k !== CACHE_NAME; }).map(function(k) { return caches.delete(k); }));
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', function(event) {
  var url = event.request.url;

  // Never cache API calls
  if (url.includes('api.php')) {
    event.respondWith(fetch(event.request).catch(function() {
      return new Response(JSON.stringify({ok:false,error:'ไม่มีการเชื่อมต่ออินเทอร์เน็ต'}), {
        headers: {'Content-Type': 'application/json'}
      });
    }));
    return;
  }

  // Network-First for HTML navigation / index.php
  if (event.request.mode === 'navigate' || url.includes('index.php')) {
    event.respondWith(
      fetch(event.request).then(function(res) {
        if (res && res.status === 200) {
          var clone = res.clone();
          caches.open(CACHE_NAME).then(function(cache) { cache.put(event.request, clone); });
        }
        return res;
      }).catch(function() {
        return caches.match(event.request).then(function(cached) { return cached || caches.match('./index.php'); });
      })
    );
    return;
  }

  // Cache-first for static assets
  event.respondWith(
    caches.match(event.request).then(function(cached) {
      if (cached) return cached;
      return fetch(event.request).then(function(response) {
        if (response && response.status === 200 && response.type === 'basic') {
          var clone = response.clone();
          caches.open(CACHE_NAME).then(function(cache) { cache.put(event.request, clone); });
        }
        return response;
      });
    })
  );
});

// PWA Push Notifications & Interaction
self.addEventListener('push', function(event) {
  var data = { title: 'ระบบใบลาราชการ LRS', body: 'มีการอัปเดตสถานะเอกสารใบลาราชการ', url: './#/my' };
  if (event.data) {
    try { data = event.data.json(); } catch(e) { data.body = event.data.text(); }
  }
  var options = {
    body: data.body,
    icon: 'manifest-icon-192.png',
    badge: 'manifest-icon-192.png',
    data: { url: data.url || './#/my' }
  };
  event.waitUntil(self.registration.showNotification(data.title, options));
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  var targetUrl = (event.notification.data && event.notification.data.url) ? event.notification.data.url : './#/my';
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
      for (var i = 0; i < clientList.length; i++) {
        var client = clientList[i];
        if ('focus' in client) return client.focus();
      }
      if (clients.openWindow) return clients.openWindow(targetUrl);
    })
  );
});
