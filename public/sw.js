self.addEventListener('install', event => {
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', function(event) {
    let data = {};

    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data = {
                title: "SiBanksa",
                body: event.data.text()
            };
        }
    }

    const options = {
        body: data.body || "Ada setoran baru!",
        vibrate: [100, 50, 100],
        data: { url: data.url || '/' }
    };

    event.waitUntil(
        self.registration.showNotification(data.title || "SiBanksa", options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    const url = event.notification?.data?.url || '/';

    event.waitUntil(
        clients.openWindow(url)
    );
});
