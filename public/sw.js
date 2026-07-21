// import { precacheAndRoute } from 'workbox-precaching';
// import { registerRoute } from 'workbox-routing';
// import { NetworkFirst, CacheFirst } from 'workbox-strategies';

importScripts('https://storage.googleapis.com/workbox-cdn/releases/7.0.0/workbox-sw.js');

const { precacheAndRoute } = workbox.precaching;
const { registerRoute } = workbox.routing;
const { NetworkFirst, CacheFirst } = workbox.strategies;
const { ExpirationPlugin } = workbox.expiration;

// Mengambil list file dari manifest hasil build Vite
precacheAndRoute([]);

// 1. Strategi untuk Gambar/Assets (Cache First)
registerRoute(
  ({request}) => request.destination === 'image',
  new CacheFirst({ cacheName: 'images' })
);
// 2. Strategi untuk Halaman (Network First)
// Ini membuat halaman tetap bisa diakses saat offline jika pernah dikunjungi sebelumnya
registerRoute(
  ({request}) => request.mode === 'navigate',
  new NetworkFirst({
    cacheName: 'pages',
    networkTimeoutSeconds: 5, // kalau network lambat >5s baru fallback ke cache
    plugins: [
      new ExpirationPlugin({
        maxEntries: 20,
        maxAgeSeconds: 60 * 60 * 24, // 1 hari, biar cache lama otomatis basi
      }),
      {
        // JANGAN cache kalau bukan HTML asli
        cacheWillUpdate: async ({ response }) => {
          const contentType = response.headers.get('content-type') || '';
          if (response.status === 200 && contentType.includes('text/html')) {
            return response;
          }
          return null; // response JSON/Inertia partial ditolak, tidak masuk cache
        },
      },
    ],
  })
);

self.addEventListener('install', event => {
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', function (event) {
    console.log('Push received:', event.data.text());

    let data = {};
    try {
        data = event.data.json();
    } catch (e) {
        console.error('Data push bukan JSON:', event.data.text());
        return;
    }

    const title = data.title || "Notifikasi SiBanksa";
    const options = {
        body: data.body || "Ada pembaruan informasi.",
        icon: '/assets/main-logo.svg', // Pastikan path ini benar/bisa diakses
        badge: '/assets/main-logo.svg', // Icon kecil di tray (optional)
        data: {
            url: data.url || '/'
        },
        // Properti tambahan agar lebih stabil di Chrome/Mac
        vibrate: [100, 50, 100],
        actions: [
            { action: 'open', title: 'Lihat Detail' }
        ]
    };

    // WAJIB: event.waitUntil memberitahu browser untuk tidak mematikan SW
    // sampai notifikasi benar-benar ditampilkan
    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Event saat notifikasi diklik
self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(windowClients => {
            // Jika tab SiBanksa sudah terbuka, fokuskan saja
            for (let i = 0; i < windowClients.length; i++) {
                let client = windowClients[i];
                if (client.url === event.notification.data.url && 'focus' in client) {
                    return client.focus();
                }
            }
            // Jika belum ada tab terbuka, buka tab baru
            if (clients.openWindow) {
                return clients.openWindow(event.notification.data.url);
            }
        })
    );
});
