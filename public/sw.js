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
        icon: '/assets/home.svg', // Pastikan path ini benar/bisa diakses
        badge: '/assets/home.svg', // Icon kecil di tray (optional)
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
